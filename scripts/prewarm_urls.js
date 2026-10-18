const { chromium } = require('playwright');

const urls = [
    'https://cmrindia.com/?quanto_footer=main',
    'https://cmrindia.com/?quanto_tab_build=cmr-gtm',
    'https://cmrindia.com/?quanto_tab_build=cmr-connect',
    'https://cmrindia.com/?quanto_tab_build=global-cta-banner',
    'https://cmrindia.com/?quanto_tab_build=similar-reports-by-industry',
    'https://cmrindia.com/?quanto_tab_build=your-next-big-decision-deserves-better-intelligence',
    'https://cmrindia.com/?quanto_tab_build=we-worked-with-largest-global-brands',
    'https://cmrindia.com/?quanto_tab_build=testimonials',
    'https://cmrindia.com/?quanto_tab_build=fotter-card',
    'https://cmrindia.com/?quanto_tab_build=your-challenge-our-research-your-advantage',
    'https://cmrindia.com/?quanto_tab_build=who-we-serve',
    'https://cmrindia.com/?cmr_prewarm_now=1&key=cmr_secure_2026'
];

async function run() {
    console.log('Starting automated headless browser pre-warm...');
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        viewport: { width: 1440, height: 900 }
    });

    const page = await context.newPage();

    for (let i = 0; i < urls.length; i++) {
        const u = urls[i];
        console.log(`[${i + 1}/${urls.length}] Loading ${u}...`);
        try {
            const resp = await page.goto(u, { waitUntil: 'domcontentloaded', timeout: 30000 });
            await page.waitForTimeout(2000);
            console.log(`  -> Status: ${resp ? resp.status() : 'loaded'}`);
        } catch (err) {
            console.log(`  -> Notice: ${err.message}`);
        }
    }

    await browser.close();
    console.log('Automated pre-warm completed successfully!');
}

run().catch(err => {
    console.error('Pre-warm error:', err);
    process.exit(0);
});
