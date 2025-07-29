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
        browser = await chromium.launch({
            args: [
                "--no-sandbox",
                "--single-process",
                "--disable-gpu",
                "--disable-extensions",
                "--disable-dev-shm-usage",
                "--mute-audio",
            ],
        });

        const context = await browser.newContext();
        const page = await context.newPage();

        if (! args.isFromUrl) {
            const htmlContent = fs.readFileSync(args.filePath, "utf-8");

            if (! htmlContent) {
                throw new Error(`Failed to read HTML content from file: ${args.filePath}`);
            }

            await page.setContent(htmlContent, {
                waitUntil: "networkidle",
            });
        } else if (args.isFromUrl === 'true' && args.url) {
            await page.goto(args.url, {
                waitUntil: "networkidle",
            });
        }

        let headerFilePath = null,
            footerFilePath = null;

        if (args.headerFilePath) {
            headerFilePath = fs.readFileSync(args.headerFilePath, "utf-8");

            if (! headerFilePath) {
                throw new Error(`Failed to read header content from file: ${args.headerFilePath}`);
            }

            options.headerTemplate = headerFilePath;
        }

        if (args.footerFilePath) {
            footerFilePath = fs.readFileSync(args.footerFilePath, "utf-8");

            if (! footerFilePath) {
                throw new Error(`Failed to read footer content from file: ${args.footerFilePath}`);
            }

            options.footerTemplate = footerFilePath;
        }

        const pdfBuffer = await page.pdf({
            ...options,
            margin: margins,
        });

        await page.close();

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