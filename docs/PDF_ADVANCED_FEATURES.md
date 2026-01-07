# PDF Generation System - Documentation

## Overview

This system provides a comprehensive PDF generation solution for questions with advanced features including:

- **Magazine-style multi-column layouts** with intelligent content flow
- **Mathematical formula rendering** using KaTeX/MathJax
- **Multi-language support** (Hindi and English)
- **Customizable templates** and styling options

## Features

### 1. Magazine Template

The magazine template provides a professional, multi-column layout similar to magazines and books:

**Key Features:**
- Configurable column count (1-4 columns)
- Intelligent content flow - content flows continuously from one column to another without leaving empty spaces
- Column breaks are prevented within questions to maintain readability
- Section headers span across all columns for clear organization
- Optimized for print media

**Configuration Options:**
```php
'column_count' => 2,        // Number of columns (1-4)
'column_gap' => '8mm',      // Gap between columns
'column_rule' => '1px solid #ddd',  // Vertical line between columns
'page_padding' => '15mm',   // Page margins
```

### 2. Mathematical Formula Support

The system supports rendering of mathematical formulas using LaTeX syntax:

**Supported Formats:**
- Inline math: `$formula$` or `\(formula\)`
- Display math: `$$formula$$` or `\[formula\]`

**Examples:**
```
Inline: The quadratic formula is $x = \frac{-b \pm \sqrt{b^2-4ac}}{2a}$

Display: 
$$
\int_{-\infty}^{\infty} e^{-x^2} dx = \sqrt{\pi}
$$
```

**Rendering Engines:**
- **KaTeX** (recommended): Faster, better for PDFs
- **MathJax**: More comprehensive LaTeX support

### 3. API Endpoints

#### Generate PDF
```http
POST /api/admin/pdf/generate
Content-Type: application/json

{
  "template": "magazine",
  "language": "both",
  "options": {
    "column_count": 2,
    "render_math": true,
    "math_engine": "katex",
    "show_solutions": true,
    "show_answer_key": false,
    "highlight_correct_answers": false,
    "font_size": 11,
    "column_gap": "8mm",
    "page_padding": "15mm"
  }
}
```

#### Get Available Templates
```http
GET /api/admin/pdf/templates
```

Response includes all available templates with their features and configuration options.

#### Preview PDF
```http
POST /api/admin/pdf/preview
Content-Type: application/json

{
  "questions": [1, 2, 3],
  "template": "magazine",
  "language": "both",
  "options": {
    "column_count": 2,
    "render_math": true
  }
}
```

## Installation & Setup

### 1. Install Node.js Dependencies (for Math Rendering)

```bash
# Install Puppeteer and KaTeX for math rendering
npm install puppeteer katex

# Or use the package file
cp package-math.json package.json
npm install
```

### 2. Verify System Requirements

The system requires:
- **wkhtmltopdf** or **LibreOffice** for PDF generation
- **Node.js** (optional, for advanced math rendering)

### 3. Configure PDF Settings

Edit `config/pdf.php` to customize default options:

```php
'defaults' => [
    'column_count' => 2,
    'column_gap' => '8mm',
    'render_math' => true,
    'math_engine' => 'katex',
    // ... other options
]
```

## Usage Examples

### Example 1: Magazine-Style PDF with Math

```php
$options = [
    'template' => 'magazine',
    'language' => 'both',
    'options' => [
        'column_count' => 2,
        'column_gap' => '8mm',
        'render_math' => true,
        'math_engine' => 'katex',
        'show_solutions' => true,
        'font_size' => 11,
        'header_text' => 'Mathematics Question Bank',
        'footer_text' => 'Prepared by EasyWay'
    ]
];
```

### Example 2: Three-Column Compact Layout

```php
$options = [
    'template' => 'magazine',
    'options' => [
        'column_count' => 3,
        'column_gap' => '6mm',
        'font_size' => 10,
        'show_solutions' => false,
        'show_answer_key' => true
    ]
];
```

### Example 3: Using Mathematical Formulas in Questions

When creating questions, use LaTeX syntax for formulas:

```json
{
  "question": {
    "text": {
      "en": "Find the derivative of $f(x) = x^2 + 2x + 1$",
      "hi": "$f(x) = x^2 + 2x + 1$ का अवकलज ज्ञात कीजिए"
    }
  },
  "solution": {
    "text": {
      "en": "Using the power rule: $$\\frac{d}{dx}[x^2 + 2x + 1] = 2x + 2$$",
      "hi": "शक्ति नियम का उपयोग करते हुए: $$\\frac{d}{dx}[x^2 + 2x + 1] = 2x + 2$$"
    }
  }
}
```

## Advanced Features

### Column Flow Control

The system uses CSS multi-column layout with intelligent break prevention:

- Questions never break across columns
- Section headers span all columns
- Content flows naturally to fill all available space
- No empty spaces at the end of columns

### Math Rendering Pipeline

1. **Pre-processing**: LaTeX formulas are identified in question text
2. **Rendering**: Formulas are converted to HTML/SVG using KaTeX or MathJax
3. **Integration**: Rendered formulas are embedded in the PDF-ready HTML
4. **PDF Generation**: wkhtmltopdf converts the final HTML to PDF

### Fallback Mechanism

If Node.js or math rendering libraries are not available:
- Basic LaTeX symbols are converted to Unicode
- Formulas are displayed in styled text format
- PDF generation still works without advanced math rendering

## Troubleshooting

### Math Formulas Not Rendering

1. Check if Node.js is installed: `node --version`
2. Install dependencies: `npm install puppeteer katex`
3. Verify the math renderer script: `node resources/js/math-renderer.js`

### Column Layout Issues

1. Ensure you're using the 'magazine' template
2. Check column_count is between 1-4
3. Verify page_size is appropriate for column count (A4 works best with 2-3 columns)

### PDF Generation Fails

1. Check if wkhtmltopdf or LibreOffice is installed
2. Review logs in `storage/logs/laravel.log`
3. Test with simpler template first (e.g., 'standard')

## Performance Optimization

- **Math Rendering**: KaTeX is faster than MathJax
- **Column Count**: 2-3 columns work best for A4 size
- **Image Processing**: Optimize images before including in PDFs
- **Batch Generation**: Use queue system for multiple PDFs

## Browser Compatibility

The math rendering uses Puppeteer (headless Chrome) for server-side rendering, ensuring consistent output across all platforms.

## License

This PDF generation system is part of the EasyWay project.
