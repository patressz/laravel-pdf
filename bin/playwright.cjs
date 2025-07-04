const { chromium } = require('playwright');
const fs = require('fs');

/**
 * Parse command line arguments.
 */
function parseArgs() {
    const [, , ...args] = process.argv;
    const parsedArgs = {};

    args.forEach((arg) => {
        if (arg.startsWith("--")) {
            const [key, value] = arg.substring(2).split("=");
            parsedArgs[key] = value || true;
        }
    });

    return parsedArgs;
}

/**
 * Main function to run the Playwright script.
 */
(async function main() {
    const options = parseArgs();

    const browser = await chromium.launch();

    const context = await browser.newContext();
    const page = await context.newPage();

    const htmlContent = fs.readFileSync(options.filePath, 'utf-8');

    await page.setContent(htmlContent, {
        waitUntil: 'networkidle'
    });

    console.log(options.filePath);
    await page.pdf({
        path: options.outputPath,
        format: options.format,
    });

    browser.close();
})();