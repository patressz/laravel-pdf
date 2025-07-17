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
    const args = parseArgs();
    const margins = args.margins ? JSON.parse(args.margins) : {};
    const options = args.options ? JSON.parse(args.options) : {};

    let browser = null;

    try {
        browser = await chromium.launch();
        
        const context = await browser.newContext();
        const page = await context.newPage();
        
        const htmlContent = fs.readFileSync(args.filePath, "utf-8");
        
        await page.setContent(htmlContent, {
            waitUntil: "networkidle",
        });
        
        const pdfBuffer = await page.pdf({
            ...options,
            margin: margins
        });

        process.stdout.write(pdfBuffer.toString("base64"));
    } catch (error) {
        console.error("Error generating PDF:", error);
        process.exit(1);
    } finally {
        if (browser) {
            await browser.close();
        }
    }
})();