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

        let headerFilePath = null,
            footerFilePath = null;

        if (args.headerFilePath) {
            headerFilePath = fs.readFileSync(args.headerFilePath, "utf-8");

            options.headerTemplate = headerFilePath;
        }

        if (args.footerFilePath) {
            footerFilePath = fs.readFileSync(args.footerFilePath, "utf-8");

            options.footerTemplate = footerFilePath;
        }

        await page.setContent(htmlContent, {
            waitUntil: "networkidle",
        });
        
        const pdfBuffer = await page.pdf({
            ...options,
            margin: margins,
            displayHeaderFooter: true,
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