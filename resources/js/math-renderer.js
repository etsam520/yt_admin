/**
 * Math Formula Renderer for PDF Generation
 * Converts LaTeX/MathJax formulas to SVG for inclusion in PDFs
 */

const puppeteer = require('puppeteer');
const path = require('path');
const fs = require('fs').promises;

class MathRenderer {
    constructor() {
        this.browser = null;
        this.page = null;
    }

    /**
     * Initialize the browser and page
     */
    async init() {
        this.browser = await puppeteer.launch({
            headless: 'new',
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--disable-gpu'
            ]
        });
        
        this.page = await this.browser.newPage();
        
        // Load the math renderer HTML
        const rendererPath = path.join(__dirname, '../../views/pdf/math-renderer.html');
        await this.page.goto(`file://${rendererPath}`, { waitUntil: 'networkidle0' });
        
        // Wait for MathJax to load
        await this.page.waitForFunction(() => window.MathJax !== undefined);
        await this.page.waitForTimeout(1000);
    }

    /**
     * Render math content in HTML
     * @param {string} htmlContent - HTML content containing LaTeX formulas
     * @returns {string} - HTML with rendered math formulas
     */
    async renderMath(htmlContent) {
        if (!this.page) {
            await this.init();
        }

        try {
            // Set content and render math
            const renderedHtml = await this.page.evaluate((content) => {
                return window.setAndRenderContent(content);
            }, htmlContent);

            return renderedHtml;
        } catch (error) {
            console.error('Error rendering math:', error);
            return htmlContent; // Return original content if rendering fails
        }
    }

    /**
     * Convert LaTeX to SVG
     * @param {string} latex - LaTeX formula
     * @param {boolean} displayMode - Whether to render in display mode
     * @returns {string} - SVG string
     */
    async latexToSVG(latex, displayMode = false) {
        if (!this.page) {
            await this.init();
        }

        try {
            const svg = await this.page.evaluate((formula, isDisplay) => {
                const container = document.createElement('div');
                
                if (window.katex) {
                    window.katex.render(formula, container, {
                        displayMode: isDisplay,
                        throwOnError: false,
                        output: 'html'
                    });
                    
                    // Extract the rendered content
                    return container.innerHTML;
                }
                
                return null;
            }, latex, displayMode);

            return svg;
        } catch (error) {
            console.error('Error converting LaTeX to SVG:', error);
            return `<span class="math-error">${latex}</span>`;
        }
    }

    /**
     * Process HTML content and replace all math formulas with rendered versions
     * @param {string} htmlContent - HTML content
     * @returns {string} - Processed HTML
     */
    async processHTMLWithMath(htmlContent) {
        if (!this.page) {
            await this.init();
        }

        // Inline math: $formula$
        const inlineMathRegex = /\$([^$]+)\$/g;
        // Display math: $$formula$$
        const displayMathRegex = /\$\$([^$]+)\$\$/g;
        
        let processedContent = htmlContent;

        // Process display math first (to avoid conflicts)
        const displayMatches = [...processedContent.matchAll(displayMathRegex)];
        for (const match of displayMatches) {
            const latex = match[1].trim();
            const svg = await this.latexToSVG(latex, true);
            processedContent = processedContent.replace(match[0], 
                `<div class="math-display">${svg}</div>`);
        }

        // Process inline math
        const inlineMatches = [...processedContent.matchAll(inlineMathRegex)];
        for (const match of inlineMatches) {
            const latex = match[1].trim();
            const svg = await this.latexToSVG(latex, false);
            processedContent = processedContent.replace(match[0], 
                `<span class="math-formula">${svg}</span>`);
        }

        return processedContent;
    }

    /**
     * Process a full HTML document
     * @param {string} filePath - Path to HTML file
     * @returns {string} - Processed HTML content
     */
    async processHTMLFile(filePath) {
        const htmlContent = await fs.readFile(filePath, 'utf-8');
        return await this.processHTMLWithMath(htmlContent);
    }

    /**
     * Close the browser
     */
    async close() {
        if (this.browser) {
            await this.browser.close();
            this.browser = null;
            this.page = null;
        }
    }
}

// Export for use in other modules
module.exports = MathRenderer;

// CLI usage
if (require.main === module) {
    const args = process.argv.slice(2);
    
    if (args.length === 0) {
        console.log('Usage: node math-renderer.js <input-html-file> [output-html-file]');
        process.exit(1);
    }

    const inputFile = args[0];
    const outputFile = args[1] || inputFile.replace('.html', '-rendered.html');

    (async () => {
        const renderer = new MathRenderer();
        
        try {
            console.log('Rendering math formulas...');
            const processedHTML = await renderer.processHTMLFile(inputFile);
            
            await fs.writeFile(outputFile, processedHTML);
            console.log(`Processed HTML saved to: ${outputFile}`);
        } catch (error) {
            console.error('Error:', error);
        } finally {
            await renderer.close();
        }
    })();
}
