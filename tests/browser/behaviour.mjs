// Drives the Alpine-powered components in a real browser and fails on any console error or warning.
// Runs against the catalog with or without Livewire (CI does both), to prove the components only need Alpine.
// Needs the catalog running: `composer catalog`, or set CATALOG_URL. Exits 1 on any failure.
// BROWSER=firefox runs it in Firefox instead of Chromium.
import { chromium, firefox } from 'playwright';

const base = (process.env.CATALOG_URL ?? 'http://127.0.0.1:8000/catalog').replace(/\/$/, '');
const browser = process.env.BROWSER === 'firefox'
    ? await firefox.launch()
    : await chromium.launch(process.env.CHROME_PATH ? { executablePath: process.env.CHROME_PATH } : {});
const context = await browser.newContext({ viewport: { width: 1280, height: 900 } });
const page = await context.newPage();

const failures = [];
let logs = [];

// Alpine reports expression errors as console warnings, so both count.
page.on('console', (message) => ['error', 'warning'].includes(message.type()) && logs.push(`${message.type()}: ${message.text()}`));
page.on('pageerror', (error) => logs.push(`uncaught: ${error.message}`));

async function visit(path) {
    await page.goto(`${base}/${path}`);
    await page.waitForFunction(() => window.Alpine);
}

function expect(condition, message) {
    if (! condition) {
        throw new Error(message);
    }
}

const focused = () => page.evaluate(() => document.activeElement);

async function check(name, run) {
    logs = [];

    try {
        await run();
        expect(! logs.length, `console: ${logs.join(' | ')}`);
        console.log(`✓ ${name}`);
    } catch (error) {
        failures.push(`${name}: ${error.message.split('\n')[0]}`);
        console.log(`✗ ${name}`);
    }
}

// Every page in the sidebar loads Alpine and logs nothing.
await visit('introduction');
const slugs = await page.$$eval('#sidebar a[href^="/catalog/"]', (links) => links.map((link) => link.getAttribute('href').replace(/^\/catalog\//, '')));

for (const slug of slugs) {
    await check(`${slug} loads cleanly`, () => visit(slug));
}

await check('tabs: arrow keys move the selection', async () => {
    await visit('tabs');
    const tabs = page.locator('article [role=tab][aria-controls]');
    await tabs.first().focus();
    await page.keyboard.press('ArrowRight');
    expect(await tabs.nth(1).getAttribute('aria-selected') === 'true', 'the second tab is not selected');
    expect(await tabs.nth(1).evaluate((tab) => tab === document.activeElement), 'focus did not move to the second tab');
    expect(await page.locator(`#${await tabs.nth(1).getAttribute('aria-controls')}`).isVisible(), 'its panel is hidden');
});

await check('dropdown: opens on the first item, arrow keys move, Escape returns focus', async () => {
    await visit('dropdown');
    const trigger = page.locator('article [aria-haspopup=menu]').first();
    await trigger.click();
    await page.waitForFunction(() => document.activeElement?.getAttribute('role') === 'menuitem');
    const first = await page.evaluate(() => document.activeElement.textContent.trim());
    await page.keyboard.press('ArrowDown');
    const second = await page.evaluate(() => document.activeElement.textContent.trim());
    expect(first !== second, 'ArrowDown did not move focus');
    await page.keyboard.press('Escape');
    expect(await trigger.evaluate((element) => element === document.activeElement), 'focus did not return to the trigger');
});

await check('combobox: arrow keys and Enter pick an option', async () => {
    await visit('combobox');
    const input = page.locator('article [role=combobox]').first();
    await input.focus();
    await page.keyboard.press('ArrowDown');
    expect(await input.getAttribute('aria-expanded') === 'true', 'the listbox did not open');
    expect(await input.getAttribute('aria-activedescendant'), 'no option is active');
    await page.keyboard.press('Enter');
    expect((await input.inputValue()) !== '', 'no value was picked');
    expect(await input.getAttribute('aria-expanded') === 'false', 'the listbox stayed open');
});

await check('date picker: the calendar picks a date', async () => {
    await visit('date-picker');
    await page.locator('article [aria-label="Choose date"]').first().click();
    const calendar = page.locator('article [role=dialog][id$=-calendar]').first();
    expect(await calendar.isVisible(), 'the calendar did not open');
    await calendar.locator('button', { hasText: /^15$/ }).first().click();
    expect((await page.locator('article input[type=date]').first().inputValue()).endsWith('-15'), 'the date was not set');
});

// Firefox can't hide its own picker icon (Chromium hides it with ::-webkit-calendar-picker-indicator), so the
// date picker slides it under our button there. Check our button is what sits on top of it.
if (process.env.BROWSER === 'firefox') {
    await check("date picker: Firefox's own icon is covered by ours", async () => {
        await visit('date-picker');
        const covered = await page.locator('article input[type=date]').first().evaluate((input) => {
            const box = input.getBoundingClientRect();
            const hit = document.elementFromPoint(box.right - parseFloat(getComputedStyle(input).paddingRight) - 5, box.top + box.height / 2);
            return hit?.closest('[aria-label="Choose date"]') !== null;
        });
        expect(covered, "Firefox's picker icon shows beside ours");
    });
}

await check('number input: the buttons step the value', async () => {
    await visit('number-input');
    const input = page.locator('article input[type=number]').first();
    const before = Number(await input.inputValue());
    await page.locator('article [aria-label=Increase]').first().click();
    expect(Number(await input.inputValue()) === before + 1, 'Increase did not add one');
});

await check('slider: the shown value follows the keyboard', async () => {
    await visit('slider');
    const slider = page.locator('article input[type=range]').first();
    const output = page.locator(`article output[for="${await slider.getAttribute('id')}"]`);
    const before = await output.textContent();
    await slider.focus();
    await page.keyboard.press('ArrowRight');
    expect((await output.textContent()) !== before, 'the output did not update');
});

await check('textarea: the counter follows typing', async () => {
    await visit('textarea');
    const counter = page.locator('article [x-text=length]').first();
    const before = Number(await counter.textContent());
    await page.locator('article textarea[maxlength]').first().focus();
    await page.keyboard.press('End');
    await page.keyboard.type('abc');
    expect(Number(await counter.textContent()) === before + 3, 'the counter did not change');
});

await check('toasts: show from GiraffeUI.toast and from $dispatch', async () => {
    await visit('toasts');
    await page.getByRole('button', { name: 'Success' }).click();
    const toasts = page.locator('[x-text="toast.message"]');
    await toasts.filter({ hasText: 'Your changes were saved.' }).waitFor();
    await page.getByRole('button', { name: 'From Alpine' }).click();
    await toasts.filter({ hasText: /Sent with \$dispatch/ }).waitFor();
});

await check('command palette: filters and runs a command', async () => {
    await visit('command-palette');
    await page.getByRole('button', { name: 'Open palette' }).click();
    const palette = page.locator('#demo-palette');
    expect(await palette.evaluate((dialog) => dialog.open), 'the palette did not open');
    await page.keyboard.type('sign out');
    await page.waitForFunction(() => [...document.querySelectorAll('#demo-palette [role=option]')].filter((option) => option.offsetParent).length === 1, null, { timeout: 2000 })
        .catch(() => expect(false, 'the list was not filtered'));
    await page.keyboard.press('Enter');
    expect(! (await palette.evaluate((dialog) => dialog.open)), 'the palette did not close');
});

await check('file upload: lists the chosen files', async () => {
    await visit('file-upload');
    await page.locator('article input[type=file]').first().setInputFiles({ name: 'giraffe.txt', mimeType: 'text/plain', buffer: Buffer.from('hello') });
    await page.locator('article [x-text="file.name"]', { hasText: 'giraffe.txt' }).first().waitFor();
});

await check('table: a scroller that overflows joins the tab order', async () => {
    await page.setViewportSize({ width: 390, height: 900 });
    await visit('table');
    const states = await page.$$eval('[x-init*=ResizeObserver]', (scrollers) => scrollers.map((el) => (el.scrollWidth > el.clientWidth) === (el.getAttribute('tabindex') === '0')));
    await page.setViewportSize({ width: 1280, height: 900 });
    expect(states.length && states.every(Boolean), 'tabindex does not match overflow');
});

await check('catalog playground re-renders the component', async () => {
    await visit('button');
    const preview = page.locator('[x-ref=preview]');
    const before = await preview.innerHTML();
    const select = page.locator('select[id^=control-]').first();
    const options = await select.locator('option').evaluateAll((all) => all.map((option) => option.value));
    await select.selectOption(options.find((value) => value !== '' && value !== options[0]));
    await page.waitForFunction((html) => document.querySelector('[x-ref=preview]').innerHTML !== html, before);
});

await check('flashed toast shows after a form post', async () => {
    await visit('examples/sign-in');
    await page.fill('input[name=email]', 'ada@example.com');
    await page.fill('input[name=password]', 'hunter2222');
    await page.keyboard.press('Enter');
    await page.waitForURL(/examples\/dashboard/);
    await page.locator('[x-text="toast.message"]', { hasText: 'Signed in as ada@example.com.' }).waitFor();
});

await browser.close();

if (failures.length) {
    console.error(`\n${failures.length} behaviour failures:\n\n${failures.join('\n')}`);
    process.exit(1);
}

console.log(`\nEvery component behaved, with no console errors or warnings on ${slugs.length} pages.`);
