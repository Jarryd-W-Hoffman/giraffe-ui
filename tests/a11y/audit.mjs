// Runs axe-core (WCAG 2.2 A and AA rules) on every catalog page, in light and dark mode.
// Needs the catalog running: `composer catalog`, or set CATALOG_URL. Exits 1 on any violation.
import { readFileSync } from 'node:fs';
import { createRequire } from 'node:module';
// BROWSER=firefox or BROWSER=webkit audits in that engine instead of Chromium.
import { chromium, firefox, webkit } from 'playwright';

const require = createRequire(import.meta.url);
const axe = readFileSync(require.resolve('axe-core/axe.min.js'), 'utf8');
const base = (process.env.CATALOG_URL ?? 'http://127.0.0.1:8000/catalog').replace(/\/$/, '');
const tags = ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'];

const browser = process.env.BROWSER === 'firefox'
    ? await firefox.launch()
    : process.env.BROWSER === 'webkit'
        ? await webkit.launch()
        : await chromium.launch(process.env.CHROME_PATH ? { executablePath: process.env.CHROME_PATH } : {});
const page = await browser.newPage({ viewport: { width: 1280, height: 900 } });

// Every page linked from the sidebar.
await page.goto(`${base}/introduction`);
const slugs = await page.$$eval('#sidebar a[href^="/catalog/"]', (links) => links.map((link) => link.getAttribute('href').replace(/^\/catalog\//, '')));

const failures = [];

for (const mode of ['light', 'dark']) {
    for (const slug of slugs) {
        await page.goto(`${base}/${slug}`);
        await page.waitForFunction(() => window.Alpine);
        // Measure final colours, not a transition caught halfway after switching theme.
        await page.addStyleTag({ content: '*, *::before, *::after { transition: none !important; animation: none !important; }' });
        await page.evaluate((theme) => window.GiraffeUI.setTheme(theme), mode);
        await page.addScriptTag({ content: axe });

        const violations = await page.evaluate(async (tags) => (await window.axe.run(document, { runOnly: { type: 'tag', values: tags } })).violations, tags);

        for (const violation of violations) {
            for (const node of violation.nodes) {
                failures.push(`[${mode}] ${slug}: ${violation.id} (${violation.impact}) ${node.target.join(' ')}\n    ${node.failureSummary.split('\n').slice(1).join(' ').trim()}`);
            }
        }
    }
}

await page.evaluate(() => window.GiraffeUI.setTheme('system'));
await browser.close();

if (failures.length) {
    console.error(`${failures.length} accessibility violations:\n\n${failures.join('\n')}`);
    process.exit(1);
}

console.log(`No WCAG A/AA violations on ${slugs.length} pages, in light and dark mode.`);
