// Drives the Alpine-powered components in a real browser and fails on any console error or warning.
// Runs against the catalog with or without Livewire (CI does both), to prove the components only need Alpine.
// Needs the catalog running: `composer catalog`, or set CATALOG_URL. Exits 1 on any failure.
// BROWSER=firefox or BROWSER=webkit (Safari's engine) runs it there instead of Chromium.
import { chromium, firefox, webkit } from 'playwright';

const base = (process.env.CATALOG_URL ?? 'http://127.0.0.1:8000/catalog').replace(/\/$/, '');
const browser = process.env.BROWSER === 'firefox'
    ? await firefox.launch()
    : process.env.BROWSER === 'webkit'
        ? await webkit.launch()
        : await chromium.launch(process.env.CHROME_PATH ? { executablePath: process.env.CHROME_PATH } : {});
const context = await browser.newContext({ viewport: { width: 1280, height: 900 } });
const page = await context.newPage();

// Waits that depend on the catalog server answering. testbench serve boots the app per request and handles one at
// a time, so a response can take a couple of seconds when requests queue, especially in CI.
const SERVER = { timeout: 10000 };

const failures = [];
let logs = [];

// Alpine reports expression errors as console warnings, so both count. Firefox's note that a lazy-loaded preview
// frame laid out before its styles arrived is about timing, not a fault, so it doesn't.
const harmless = /Layout was forced before the page was fully loaded/;
page.on('console', (message) => ['error', 'warning'].includes(message.type()) && ! harmless.test(message.text()) && logs.push(`${message.type()}: ${message.text()}`));
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

const describeFocus = () => page.evaluate(() => {
    const element = document.activeElement;
    return (element.closest('label') ?? element).textContent.trim().replace(/\s+/g, ' ');
});

await check('dropdown: checkbox and radio items toggle and keep the menu open', async () => {
    await visit('dropdown');
    const menu = page.locator('#view-options');
    await page.getByRole('button', { name: 'View' }).click();
    await page.waitForFunction(() => document.activeElement?.getAttribute('role') === 'menuitemcheckbox');
    const drafts = page.locator('#view-options input[value=drafts]');
    await page.keyboard.press('Space');
    expect(! (await drafts.isChecked()), 'Space did not toggle the checkbox');
    expect(await menu.evaluate((el) => el.matches(':popover-open')), 'toggling closed the menu');
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('ArrowDown');
    const sort = () => page.$eval('#view-options input[name=sort]:checked', (radio) => radio.value);
    expect((await sort()) === 'name', 'arrow keys changed the radio');
    await page.keyboard.press('Space');
    expect((await sort()) === 'updated', 'Space did not pick the radio');
    await page.keyboard.press('Escape');
});

await check('dropdown: submenus open with ArrowRight and close with ArrowLeft and Escape', async () => {
    await visit('dropdown');
    await page.getByRole('button', { name: 'File' }).click();
    await page.waitForFunction(() => document.activeElement?.getAttribute('role') === 'menuitem');
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('ArrowRight');
    await page.waitForFunction(() => document.activeElement?.textContent.trim() === 'Copy link', null, { timeout: 5000 })
        .catch(() => expect(false, 'ArrowRight did not open the submenu on its first item'));
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('Enter');
    await page.waitForFunction(() => document.activeElement?.textContent.trim() === 'PDF', null, { timeout: 5000 })
        .catch(() => expect(false, 'Enter did not open the nested submenu'));
    await page.keyboard.press('ArrowLeft');
    expect((await describeFocus()).startsWith('Export as'), 'ArrowLeft did not return to the submenu trigger');
    await page.keyboard.press('Escape');
    expect((await describeFocus()).startsWith('Share'), 'Escape did not return to the submenu trigger');
    expect(await page.$eval('#file-menu', (el) => el.matches(':popover-open')), 'Escape in a submenu closed the whole menu');
    await page.keyboard.press('Enter');
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('Enter');
    await page.locator('[x-text="toast.message"]', { hasText: 'Email drafted' }).waitFor();
    expect(! (await page.$eval('#file-menu', (el) => el.matches(':popover-open'))), 'picking an item left the menu open');
});

if (slugs.includes('examples/customers')) {
    await check('dropdown: checkbox items with wire:model keep the menu open through a Livewire update', async () => {
        await visit('examples/customers');
        await page.getByRole('button', { name: 'Columns' }).click();
        await page.waitForFunction(() => document.activeElement?.getAttribute('role') === 'menuitemcheckbox');
        await page.keyboard.press('Space');
        await page.waitForFunction(() => ! [...document.querySelectorAll('thead th')].some((th) => th.textContent.trim() === 'Plan'), null, SERVER)
            .catch(() => expect(false, 'the Plan column is still showing'));
        expect(await page.$eval('#customer-columns', (el) => el.matches(':popover-open')), 'the update closed the menu');
        expect((await describeFocus()) === 'Plan', 'focus left the checkbox');
        await page.keyboard.press('Escape');
    });
}

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

await check('combobox: searches on the server with url, without Livewire', async () => {
    await visit('combobox');
    const input = page.getByPlaceholder('Search timezones');
    const root = input.locator('xpath=..');
    const requests = [];
    const track = (request) => request.url().includes('/api/timezones') && requests.push(request.url());
    page.on('request', track);

    // Typing quickly sends one request, and Enter straight after typing waits for it and picks the first result.
    await input.focus();
    // 30 ms between keys, well inside the 300 ms debounce, so only the whole word is searched.
    await page.keyboard.type('tokyo', { delay: 30 });
    await page.keyboard.press('Enter');
    await page.waitForFunction((el) => el.querySelector('input[type=hidden]').value === 'Asia/Tokyo', await root.elementHandle(), SERVER)
        .catch(() => expect(false, `Enter after typing did not pick Asia/Tokyo (requests: ${requests.join(', ')})`));
    expect(requests.length === 1 && requests[0].endsWith('q=tokyo'), `expected one request for "tokyo", got ${requests.join(', ')}`);
    expect((await input.inputValue()) === 'Asia/Tokyo', 'the input does not show the choice');

    // Reopening shows the first options again, and the choice keeps its label.
    await page.keyboard.press('ArrowDown');
    expect((await root.locator('[role=option]').count()) === 20, 'reopening did not show the first options');
    await page.keyboard.press('Escape');
    expect((await input.inputValue()) === 'Asia/Tokyo', 'the label was lost');

    // No matches says so.
    await input.fill('');
    await page.keyboard.type('nowhere-at-all');
    await root.getByRole('status').filter({ hasText: 'No results' }).waitFor(SERVER);
    await page.keyboard.press('Escape');
    page.off('request', track);
});

if (slugs.includes('livewire')) {
    await check('combobox: url search inside a Livewire form keeps its value through a save', async () => {
        await visit('livewire');
        const input = page.locator('#lw-timezone');
        // It starts on Europe/London, so clear it first, as a person would.
        await input.fill('');
        await page.keyboard.type('tokyo');
        await page.keyboard.press('Enter');
        await page.waitForFunction(() => document.querySelector('#lw-timezone').value === 'Asia/Tokyo', null, SERVER);
        await page.getByRole('button', { name: 'Save timezone' }).click();
        await page.locator('[x-text="toast.message"]', { hasText: 'Saved Asia/Tokyo.' }).waitFor();
        expect((await input.inputValue()) === 'Asia/Tokyo', 'the label was lost after the round trip');
    });
}

await check('combobox: multiple adds chips, keeps the list open and removes with Backspace', async () => {
    await visit('combobox');
    const input = page.getByPlaceholder('Add skills');
    const root = input.locator('xpath=ancestor::div[@x-data][1]');
    const values = () => root.locator('input[type=hidden]').evaluateAll((els) => els.map((el) => el.value).join(','));
    await input.focus();
    await page.keyboard.type('liv');
    await page.keyboard.press('Enter');
    expect((await values()) === 'laravel,tailwind,livewire', `after Enter: ${await values()}`);
    expect((await input.getAttribute('aria-expanded')) === 'true', 'picking closed the list');
    await page.keyboard.press('Escape');
    await page.keyboard.press('Backspace');
    expect((await values()) === 'laravel,tailwind', `after Backspace: ${await values()}`);
    await root.getByRole('button', { name: 'Remove Laravel' }).click();
    expect((await values()) === 'tailwind', `after removing Laravel: ${await values()}`);
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

await check("date picker: disabled dates and weekdays can't be picked or typed", async () => {
    await visit('date-picker');
    const input = page.locator('input[name=collects_on]');
    const root = input.locator('xpath=..');
    await root.locator('[aria-label="Choose date"]').click();
    await page.waitForFunction(() => document.activeElement?.dataset.date === '2026-12-23');
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('Enter');
    expect((await input.inputValue()) === '2026-12-23', 'Enter picked a disabled date');
    await page.keyboard.press('Escape');
    await input.fill('2026-12-26');
    expect(! (await input.evaluate((el) => el.checkValidity())), 'a typed Saturday passed validation');
});

await check('date picker: a range picks a start, then an end, and keeps the end after the start', async () => {
    await visit('date-picker');
    const start = page.locator('input[name="stay[start]"]');
    const end = page.locator('input[name="stay[end]"]');
    const group = start.locator('xpath=ancestor::*[@role="group"]');
    await group.getByRole('button', { name: 'Choose dates' }).click();
    await page.waitForFunction(() => document.activeElement?.dataset.date === '2026-10-12');
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('Enter');
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('Enter');
    expect((await start.inputValue()) === '2026-10-14' && (await end.inputValue()) === '2026-10-17', `picked ${await start.inputValue()} to ${await end.inputValue()}`);
    await end.fill('2026-10-10');
    expect(! (await end.evaluate((el) => el.checkValidity())), 'an end before the start passed validation');
});

await check('otp input: keeps digits, takes a pasted code and submits when complete', async () => {
    await visit('otp-input');
    const code = page.locator('input[name=code]');
    await code.focus();
    await page.keyboard.type('1a2');
    expect((await code.inputValue()) === '12', `typed 1a2, got ${await code.inputValue()}`);
    await code.fill('');
    await page.keyboard.insertText('98-76-54');
    expect((await code.inputValue()) === '987654', `pasted 98-76-54, got ${await code.inputValue()}`);
    const boxes = await code.locator('xpath=..').locator('[aria-hidden=true] span:first-child').allTextContents();
    expect(boxes.join('') === '987654', `the boxes show ${boxes.join('')}`);
    await page.locator('input[name=pin]').focus();
    await page.keyboard.type('4321');
    await page.locator('[x-text="toast.message"]', { hasText: 'Checking 4321' }).waitFor();
});

await check('rating: arrow keys pick stars, and the stars up to the choice fill', async () => {
    await visit('rating');
    const group = page.locator('[role=radiogroup]').first();
    await group.locator('input').first().focus();
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('ArrowRight');
    await page.mouse.move(0, 0);
    expect((await group.locator('input:checked').getAttribute('value')) === '3', 'the arrows did not pick 3 stars');
    await page.waitForTimeout(300);
    const colours = await group.locator('label').evaluateAll((labels) => labels.map((label) => getComputedStyle(label).color));
    expect(colours[0] === colours[2] && colours[2] !== colours[3], `the fill doesn't stop at the third star: ${colours.join(' | ')}`);
});

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
    await page.waitForFunction(() => [...document.querySelectorAll('#demo-palette [role=option]')].filter((option) => option.offsetParent).length === 1, null, { timeout: 5000 })
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
    const states = await page.$$eval('[x-init*=ResizeObserver]', (scrollers) => scrollers.map((el) => (el.scrollWidth > el.clientWidth || el.scrollHeight > el.clientHeight) === (el.getAttribute('tabindex') === '0')));
    await page.setViewportSize({ width: 1280, height: 900 });
    expect(states.length && states.every(Boolean), 'tabindex does not match overflow');
});

await check('table: select-all, a mixed state, highlighted rows and a plain form submit', async () => {
    await visit('table');
    const table = page.locator('table', { has: page.locator('[aria-label="Select all members"]') });
    const all = table.locator('[aria-label="Select all members"]');
    const rows = table.locator('tbody [data-gui-select]');
    const header = () => all.evaluate((box) => (box.indeterminate ? 'mixed' : box.checked ? 'all' : 'none'));
    await all.check();
    expect((await rows.evaluateAll((boxes) => boxes.every((box) => box.checked))) && (await header()) === 'all', 'select-all did not tick every row');
    expect((await table.locator('tbody tr:has([data-gui-select]:checked)').evaluateAll((trs) => trs.every((tr) => getComputedStyle(tr).backgroundColor !== 'rgba(0, 0, 0, 0)'))), 'selected rows are not highlighted');
    await rows.first().uncheck();
    expect((await header()) === 'mixed', 'unticking a row did not make the header mixed');
    // A script setting checked (as wire:model or x-model would) is followed too.
    await rows.evaluateAll((boxes) => boxes.forEach((box) => { box.checked = false; }));
    expect((await header()) === 'none', 'the header did not follow checked being set by a script');
    await rows.nth(1).check();
    await table.locator('xpath=ancestor::form').getByRole('button', { name: 'Export selected' }).click();
    await page.locator('[x-text="toast.message"]', { hasText: '1 selected' }).waitFor();
});

await check('table: max-height scrolls under a sticky header, and aligns columns', async () => {
    await visit('table');
    const scroller = page.locator('[style*="max-height: 16rem"]');
    expect((await scroller.getAttribute('tabindex')) === '0', 'the tall scroller is not keyboard focusable');
    await scroller.evaluate((el) => { el.scrollTop = 200; });
    const offset = await scroller.evaluate((el) => el.querySelector('thead').getBoundingClientRect().top - el.getBoundingClientRect().top);
    expect(Math.abs(offset) < 2, `the header scrolled away (${offset}px)`);
    const price = page.locator('table', { has: page.getByText('$1,499') }).locator('td', { hasText: '$1,499' });
    expect((await price.evaluate((td) => getComputedStyle(td).textAlign)) === 'end', 'the price column is not aligned to the end');
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

await check('onboarding: a multi-step form of plain posts, with the stepper', async () => {
    await visit('examples/onboarding');
    await page.locator('input[name=workspace]').fill('Analytical Engines');
    await page.locator('input[name=slug]').focus();
    await page.keyboard.type('Bad Slug');
    await Promise.all([page.waitForNavigation(), page.keyboard.press('Enter')]);
    await page.getByText('Use lowercase letters, numbers and single dashes.').waitFor();
    await page.locator('input[name=slug]').fill('analytical-engines');
    await Promise.all([page.waitForURL(/step=team/), page.getByRole('button', { name: 'Continue' }).click()]);
    expect((await page.locator('[aria-current=step]').innerText()).includes('Team'), 'the stepper is not on Team');
    await Promise.all([page.waitForURL(/step=workspace/), page.getByRole('link', { name: /Workspace/ }).click()]);
    expect((await page.locator('input[name=slug]').inputValue()) === 'analytical-engines', 'going back lost what was entered');
});

await check('navbar: on a small screen the links fold into a menu that Escape closes', async () => {
    await page.setViewportSize({ width: 390, height: 800 });
    await visit('examples/pricing');
    const button = page.locator('header').first().getByRole('button', { name: 'Menu' });
    await button.focus();
    await page.keyboard.press('Enter');
    const menu = page.locator('[popover][id$=-menu]');
    expect(await menu.evaluate((el) => el.matches(':popover-open')), 'the menu did not open');
    expect((await menu.locator('[aria-current=page]').textContent()).trim() === 'Pricing', 'the current page is not marked');
    await page.keyboard.press('Escape');
    expect(await button.evaluate((el) => el === document.activeElement), 'focus did not return to the menu button');
    await page.setViewportSize({ width: 1280, height: 900 });
});

await check('banner: dismissing hides it, and remember keeps it hidden after a reload', async () => {
    await visit('banner');
    await page.evaluate(() => localStorage.removeItem('gui-banner:catalog-demo'));
    await visit('banner');
    const banner = page.locator('section[aria-label=Announcement]', { hasText: 'privacy policy' });
    await banner.getByRole('button', { name: 'Dismiss' }).click();
    expect(await banner.isHidden(), 'dismissing did not hide it');
    await visit('banner');
    expect(await banner.isHidden(), 'it came back after a reload');
    await page.evaluate(() => localStorage.removeItem('gui-banner:catalog-demo'));
});

await check('copy button: copies, confirms, and goes back after two seconds', async () => {
    await visit('copy-button');
    const wrapper = page.locator('article [data-copy-target="#invite-link"]').first();
    const button = wrapper.getByRole('button');
    await button.click();
    await wrapper.getByRole('status').filter({ hasText: 'Copied' }).waitFor({ state: 'attached' });
    await page.waitForFunction((el) => el.innerText.trim() === 'Copied', await button.elementHandle(), { timeout: 1500 }).catch(() => {});
    expect((await button.innerText()).trim() === 'Copied', `the label said: ${JSON.stringify(await button.innerText())}`);
    // The copyable input's button copies that input's value.
    await page.evaluate(() => document.addEventListener('copied', (event) => { window.guiCopied = event.detail.text; }));
    await page.locator('article [data-copy-target^="[id="]').first().getByRole('button', { name: 'Copy' }).click();
    await page.waitForFunction(() => window.guiCopied !== undefined);
    const copied = await page.evaluate(() => window.guiCopied);
    expect(copied === 'https://acme.app/hooks/orders/3f9a', `copied: ${copied}`);
    await page.waitForFunction((el) => el.innerText.trim() === 'Copy invite link', await button.elementHandle(), { timeout: 4000 });
});

await check('time picker: the list starts at the chosen time, and Enter picks with the keyboard', async () => {
    await visit('time-picker');
    const input = page.locator('#gui-pickup');
    await input.fill('09:10');
    await page.locator('article').getByRole('button', { name: 'Choose time' }).nth(1).click();
    const list = page.locator('#gui-pickup-times [role=listbox]');
    await list.waitFor();
    // 09:10 isn't in the list, so it starts on the next time, 09:15, and the list is focused.
    await page.waitForFunction((el) => document.activeElement === el, await list.elementHandle(), { timeout: 2000 }).catch(() => {});
    expect(await list.evaluate((el) => document.activeElement === el), 'the list did not take focus');
    expect((await list.locator('[data-active=true]').textContent()) === '9:15 AM', `it started on ${await list.locator('[data-active=true]').textContent()}`);
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('Enter');
    expect((await input.inputValue()) === '09:30', `picked ${await input.inputValue()}`);
    expect(await input.evaluate((el) => document.activeElement === el), 'focus did not go back to the input');
    expect(await list.isHidden(), 'the list stayed open');
});

await check('scatter and heatmap: arrow keys read out a point, and a cell', async () => {
    await visit('scatter-chart');
    const scatter = page.locator('article figure').first();
    await scatter.locator('[aria-roledescription=chart]').focus();
    await page.keyboard.press('ArrowRight');
    const scatterLive = scatter.locator('[aria-live]');
    await page.waitForFunction((el) => el.textContent !== '', await scatterLive.elementHandle());
    expect((await scatterLive.textContent()) === 'Referrals, Spend $800, Signups 71', `scatter read: ${await scatterLive.textContent()}`);

    await visit('heatmap');
    const heatmap = page.locator('article figure').nth(1);
    await heatmap.locator('[aria-roledescription=chart]').focus();
    // The first arrow lands on the first cell; then two right and one down: Wk 2, Wednesday.
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('ArrowDown');
    const heatLive = heatmap.locator('[aria-live]');
    await page.waitForFunction((el) => el.textContent.startsWith('Wk 2'), await heatLive.elementHandle());
    expect((await heatLive.textContent()) === 'Wk 2, W: 7', `heatmap read: ${await heatLive.textContent()}`);
});

await check('sidebar: collapses to a rail and remembers it, and a submenu opens the sidebar from the rail', async () => {
    await visit('examples/projects');
    await page.evaluate(() => localStorage.removeItem('gui-sidebar-collapsed'));
    await visit('examples/projects');
    const layout = page.locator('[data-sidebar-layout]');
    await page.getByRole('button', { name: 'Collapse sidebar' }).click();
    expect(await layout.evaluate((el) => el.hasAttribute('data-collapsed')), 'it did not collapse');
    expect(await page.locator('aside a', { hasText: 'Calendar' }).getAttribute('href') !== null, 'the rail lost its links');
    await visit('examples/projects');
    expect(await layout.evaluate((el) => el.hasAttribute('data-collapsed')), 'it forgot after a reload');
    // In the rail, a submenu expands the sidebar and opens.
    await page.locator('aside summary', { hasText: 'Projects' }).click();
    expect(! (await layout.evaluate((el) => el.hasAttribute('data-collapsed'))), 'the submenu did not expand the sidebar');
    expect(await page.locator('aside details').first().evaluate((el) => el.open), 'the submenu did not open');
    await page.evaluate(() => localStorage.removeItem('gui-sidebar-collapsed'));
});

await check('landing page: the theming playground restyles its preview, and ⌘K finds components', async () => {
    await visit('home');
    const preview = page.locator('section[aria-labelledby=theming-heading] [x-bind\\:style]').first();
    await page.locator('label', { has: page.locator('input[name=home_color][value=emerald]') }).click();
    await page.locator('label', { hasText: 'Full' }).click();
    await page.waitForFunction((el) => el.style.getPropertyValue('--radius-gui') === '9999px', await preview.elementHandle());
    expect((await preview.evaluate((el) => el.style.getPropertyValue('--color-gui-primary'))).includes('emerald'), 'the colour did not apply');
    expect((await page.locator('section[aria-labelledby=theming-heading] code').textContent()).includes('--color-emerald-600'), 'the theme code did not follow');
    // The preview's dark mode is its own: the page keeps its theme.
    const pageDark = await page.evaluate(() => document.documentElement.classList.contains('dark'));
    await page.locator('label', { hasText: 'Dark preview' }).click();
    await page.waitForFunction((el) => el.classList.contains('dark') !== el.classList.contains('light'), await preview.elementHandle());
    expect((await page.evaluate(() => document.documentElement.classList.contains('dark'))) === pageDark, 'the switch changed the whole page');

    await page.locator('header').getByRole('button', { name: /Search components/ }).click();
    const palette = page.locator('#home-search');
    await palette.waitFor();
    await palette.locator('input').fill('date');
    await page.waitForFunction(() => [...document.querySelectorAll('#home-search [role=option]')].some((el) => el.offsetParent && el.textContent.includes('Date picker')));
    await page.keyboard.press('Escape');
});

await check('inbox: opening a conversation marks it read, replies appear, and archiving removes it', async () => {
    await visit('examples/inbox');
    const unread = page.locator('p[aria-live]').first();
    expect((await unread.textContent()).trim() === '3 unread', `started with ${await unread.textContent()}`);
    await page.locator('button', { hasText: 'Numbers for the board deck' }).click();
    await page.waitForFunction((el) => el.textContent.trim() === '2 unread', await unread.elementHandle());
    const thread = page.locator('article', { has: page.locator('#thread-4-heading') });
    await thread.locator('textarea').fill('Yes please.');
    await thread.getByRole('button', { name: /Send/ }).click();
    await thread.getByText('Yes please.').waitFor();
    await thread.getByRole('button', { name: 'Archive' }).click();
    await page.locator('button', { hasText: 'Numbers for the board deck' }).waitFor({ state: 'hidden', timeout: 3000 }).catch(() => {});
    expect(await page.locator('button', { hasText: 'Numbers for the board deck' }).isHidden(), 'the archived conversation is still listed');
    await page.locator('input[aria-label="Search conversations"]').fill('payout');
    await page.waitForFunction(() => [...document.querySelectorAll('section[aria-label=Conversations] li')].filter((li) => li.offsetParent).length === 1);
});

await check('example filters: the period and view switches change what is shown', async () => {
    await visit('examples/dashboard');
    await page.locator('label', { hasText: 'Year' }).click();
    await page.getByText('$512,900').first().waitFor();
    expect(await page.getByText('Total revenue this month').isHidden(), 'the month still shows');

    await visit('examples/projects');
    await page.locator('label', { hasText: 'List' }).click();
    await page.locator('table').first().waitFor();
    await page.locator('label', { hasText: 'Timeline' }).click();
    await page.getByText('Today, 13 October').waitFor();

    await visit('home');
    const revenue = page.locator('[x-data*="30d"]').first();
    await revenue.locator('label', { hasText: '12m' }).click();
    await revenue.getByText('$512,900').waitFor();
});

await check('theme switcher: picks a theme, saves it, and keeps every switcher in sync', async () => {
    await visit('theme-switcher');
    const state = () => page.evaluate(() => [document.documentElement.classList.contains('dark'), localStorage.getItem('theme'), document.querySelector('header [aria-label="Dark mode"]')?.getAttribute('aria-pressed')].join(','));
    await page.locator('article [role=radiogroup]').first().locator('label', { hasText: 'Dark' }).click();
    expect((await state()) === 'true,dark,true', `after Dark: ${await state()}`);
    await page.locator('header [aria-label="Dark mode"]').click();
    expect((await state()) === 'false,light,false', `after the toggle: ${await state()}`);
    expect((await page.locator('article [role=radiogroup] input:checked').first().getAttribute('value')) === 'light', 'the radios did not follow the toggle');
    await page.evaluate(() => window.GiraffeUI.setTheme('system'));
});

await check('line chart: arrow keys move the crosshair, which reads every series', async () => {
    await visit('line-chart');
    const chart = page.locator('article figure').nth(1);
    await chart.locator('[aria-roledescription=chart]').focus();
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('End');
    const live = chart.locator('[aria-live]');
    await page.waitForFunction((el) => el.textContent.startsWith('Dec:'), await live.elementHandle());
    expect((await live.textContent()) === 'Dec: Online $31.2K, In store $11.4K', `read out: ${await live.textContent()}`);
    expect(await chart.locator('.shadow-lg').isVisible(), 'no tooltip');
    await page.keyboard.press('Escape');
    expect((await live.textContent()) === '', 'Escape did not clear it');
});

await check('bar chart: hovering a column shows every series there', async () => {
    await visit('bar-chart');
    const chart = page.locator('article figure').nth(1);
    const plot = chart.locator('[aria-roledescription=chart]');
    await plot.scrollIntoViewIfNeeded();
    const box = await plot.boundingBox();
    await page.mouse.move(box.x + box.width * 0.3, box.y + box.height * 0.8);
    const tooltip = chart.locator('.shadow-lg');
    await tooltip.waitFor();
    expect((await tooltip.innerText()).replace(/\s+/g, ' ').trim() === 'Q2 $51K 2025 $58K 2026', `tooltip: ${await tooltip.innerText()}`);
    await page.mouse.move(0, 0);
});

await check('donut chart: arrow keys show each part in the centre', async () => {
    await visit('donut-chart');
    const chart = page.locator('article figure').first();
    await chart.locator('[aria-roledescription=chart]').focus();
    await page.keyboard.press('ArrowRight');
    await page.keyboard.press('ArrowRight');
    const live = chart.locator('[aria-live]');
    await page.waitForFunction((el) => el.textContent !== '', await live.elementHandle());
    expect((await live.textContent()) === 'PayPal: $2,310, 23% of the total', `read out: ${await live.textContent()}`);
    const centre = chart.locator('[x-show="index === 1"]');
    await centre.waitFor({ state: 'visible', timeout: 3000 }).catch(() => {});
    expect((await centre.isVisible()) && (await centre.innerText()).includes('23%'), 'the centre did not change');
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
