# Enhanced PDF Generation API - Documentation

## Overview
The PDF generation system now supports advanced features including multilingual content (Hindi/English/Both), answer keys, grouping options, and multiple layout configurations.

---

## API Endpoints

### 1. Generate PDF
**Endpoint:** `POST /api/admin/pdf/generate`

**Description:** Generate a PDF with customizable options for language, template, and formatting.

**Request Body:**
```json
{
  "questions": [1, 2, 3, 4, 5],  // Array of question IDs (optional - if not provided, all questions will be used)
  "template": "bilingual",        // Template type: standard, modern, minimal, exam, bilingual
  "language": "both",             // Language option: hindi, english, both
  "options": {
    // Display Options
    "show_solutions": true,
    "show_question_numbers": true,
    "show_answer_key": false,
    "highlight_correct_answers": true,
    "show_images": true,
    "show_difficulty_level": false,
    "show_category_info": true,
    "show_ca_date": true,
    
    // Grouping Options
    "group_by_subject": false,
    "group_by_topic": false,
    
    // Layout Options
    "page_size": "A4",              // A4, A3, Letter, Legal
    "orientation": "portrait",       // portrait, landscape
    "two_column_layout": false,
    "answers_on_separate_page": false,
    
    // Styling Options
    "font_size": 12,
    "background_color": "#ffffff",
    "text_color": "#333333",
    
    // Header/Footer
    "header_text": "Sample Exam Paper",
    "footer_text": "© 2025 Your Organization",
    "watermark": "CONFIDENTIAL",
    
    // Advanced
    "include_qr_code": false
  }
}
```

**Response:**
```json
{
  "success": true,
  "pdf_url": "http://yoursite.com/storage/pdfs/2025/12/10/questions_abc123.pdf",
  "message": "PDF generated successfully"
}
```

---

### 2. Preview PDF HTML
**Endpoint:** `POST /api/admin/pdf/preview`

**Description:** Generate HTML preview of the PDF without creating the actual PDF file.

**Request Body:**
```json
{
  "questions": [1, 2, 3],
  "template": "bilingual",
  "language": "both",
  "options": {
    "show_solutions": true,
    "show_category_info": true
  }
}
```

**Response:**
```json
{
  "success": true,
  "html_content": "<html>...</html>"
}
```

---

### 3. Get Available Templates
**Endpoint:** `GET /api/admin/pdf/templates`

**Description:** Get list of available PDF templates with their features and language options.

**Response:**
```json
{
  "success": true,
  "templates": [
    {
      "id": "bilingual",
      "name": "Bilingual Template",
      "description": "Advanced template with Hindi/English language support and modern design",
      "features": ["Language Options", "Answer Key", "Two Column Layout", "Category Grouping"]
    },
    // ... more templates
  ],
  "language_options": [
    {
      "id": "hindi",
      "name": "हिंदी (Hindi Only)",
      "description": "Display questions only in Hindi"
    },
    {
      "id": "english",
      "name": "English Only",
      "description": "Display questions only in English"
    },
    {
      "id": "both",
      "name": "द्विभाषी / Bilingual",
      "description": "Display questions in both Hindi and English"
    }
  ],
  "advanced_options": {
    "show_solutions": "Show detailed solutions for each question",
    "show_answer_key": "Display answer key on separate page",
    // ... more options
  }
}
```

---

### 4. Test PDF Generation
**Endpoint:** `GET /api/admin/pdf/test`

**Description:** Generate a test PDF with sample data to verify the PDF generation system is working.

**Response:**
```json
{
  "success": true,
  "message": "Test PDF generated successfully",
  "pdf_url": "http://yoursite.com/storage/pdfs/2025/12/10/test_xyz789.pdf",
  "path": "pdfs/2025/12/10/test_xyz789.pdf"
}
```

---

## Language Options

### 1. Hindi Only (`language: "hindi"`)
- Displays all content in Hindi (Devanagari script)
- Uses Noto Sans Devanagari font for proper rendering
- Suitable for Hindi-speaking students

### 2. English Only (`language: "english"`)
- Displays all content in English
- Uses standard Noto Sans font
- Suitable for English-speaking students

### 3. Bilingual (`language: "both"`)
- Displays content in both Hindi and English
- Hindi text appears first, followed by English
- Separated with visual dividers
- Best for mixed-language environments

---

## Template Options

### 1. Bilingual Template (Recommended)
**Features:**
- Full language support (Hindi/English/Both)
- Modern gradient design
- Category headers with colored sections
- Answer key support
- Two-column layout option
- Metadata display (subject, topic, CA date)

**Best for:** 
- Competitive exams
- Educational institutions with multilingual students
- Professional question papers

### 2. Standard Template
**Features:**
- Clean and simple layout
- Basic question-answer format
- Minimal styling
- Fast rendering

**Best for:**
- Quick printouts
- Practice tests
- Simple question papers

### 3. Modern Template
**Features:**
- Contemporary design
- Colored sections
- Visual appeal
- Enhanced typography

**Best for:**
- Student-friendly materials
- Engaging question papers

### 4. Minimal Template
**Features:**
- Ultra-clean design
- Maximum content focus
- No distractions
- Compact layout

**Best for:**
- High question density
- Space-efficient printing

### 5. Exam Template
**Features:**
- Formal exam paper format
- Answer bubbles
- Roll number section
- Official appearance

**Best for:**
- Formal examinations
- Board-style tests
- Official assessments

---

## Advanced Options Explained

### Display Options

#### `show_solutions` (boolean)
- **Default:** `true`
- **Description:** Shows detailed solutions below each question
- **Use case:** Practice papers, study materials

#### `show_answer_key` (boolean)
- **Default:** `false`
- **Description:** Adds a separate answer key page at the end
- **Use case:** Answer sheets, teacher editions

#### `highlight_correct_answers` (boolean)
- **Default:** `false`
- **Description:** Highlights correct answers in green with checkmark
- **Use case:** Study materials, practice tests

#### `show_category_info` (boolean)
- **Default:** `true`
- **Description:** Shows subject, chapter, and topic information
- **Use case:** Organized question papers, topic-wise practice

#### `show_ca_date` (boolean)
- **Default:** `true`
- **Description:** Displays current affairs date if available
- **Use case:** Current affairs questions

---

### Grouping Options

#### `group_by_subject` (boolean)
- **Default:** `false`
- **Description:** Groups questions under subject headers
- **Output:** 
  ```
  ========== Mathematics ==========
  10 Questions
  Q1. ...
  Q2. ...
  
  ========== Science ==========
  15 Questions
  Q1. ...
  ```

#### `group_by_topic` (boolean)
- **Default:** `false`
- **Description:** Groups questions under topic headers (within subjects)
- **Output:**
  ```
  ========== Mathematics - Algebra ==========
  5 Questions
  Q1. ...
  
  ========== Mathematics - Geometry ==========
  5 Questions
  Q1. ...
  ```

**Note:** If both are true, `group_by_subject` takes precedence.

---

### Layout Options

#### `two_column_layout` (boolean)
- **Default:** `false`
- **Description:** Uses two-column layout for space efficiency
- **Best for:** Questions without images, compact printing

#### `answers_on_separate_page` (boolean)
- **Default:** `false`
- **Description:** Moves all solutions to separate pages at the end
- **Best for:** Exam papers where solutions should be separate

#### `page_size` (string)
- **Options:** `A4`, `A3`, `Letter`, `Legal`
- **Default:** `A4`
- **Description:** PDF page size

#### `orientation` (string)
- **Options:** `portrait`, `landscape`
- **Default:** `portrait`
- **Description:** Page orientation

---

### Styling Options

#### `font_size` (integer)
- **Range:** 8-20
- **Default:** 12
- **Description:** Base font size in pixels
- **Note:** Hindi text automatically rendered 1-2px larger for readability

#### `background_color` (string)
- **Default:** `#ffffff`
- **Format:** Hex color code
- **Description:** Page background color

#### `text_color` (string)
- **Default:** `#333333`
- **Format:** Hex color code
- **Description:** Main text color

#### `watermark` (string)
- **Default:** `null`
- **Description:** Adds semi-transparent watermark text
- **Examples:** "CONFIDENTIAL", "SAMPLE", "DRAFT"

---

## Usage Examples

### Example 1: Bilingual Question Paper with Solutions
```javascript
const response = await fetch('/api/admin/pdf/generate', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    template: 'bilingual',
    language: 'both',
    options: {
      show_solutions: true,
      show_question_numbers: true,
      show_category_info: true,
      header_text: 'SSC CGL Tier 2 Practice Test',
      footer_text: '© 2025 EasyWay Education'
    }
  })
});
```

### Example 2: Hindi-Only Exam Paper (No Solutions)
```javascript
const response = await fetch('/api/admin/pdf/generate', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    questions: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
    template: 'exam',
    language: 'hindi',
    options: {
      show_solutions: false,
      show_answer_key: false,
      header_text: 'प्रतियोगी परीक्षा - 2025',
      watermark: 'परीक्षा'
    }
  })
});
```

### Example 3: English Answer Key
```javascript
const response = await fetch('/api/admin/pdf/generate', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    template: 'bilingual',
    language: 'english',
    options: {
      show_solutions: true,
      show_answer_key: true,
      highlight_correct_answers: true,
      answers_on_separate_page: true,
      header_text: 'Answer Key - Practice Test 1'
    }
  })
});
```

### Example 4: Subject-wise Grouped Question Paper
```javascript
const response = await fetch('/api/admin/pdf/generate', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    template: 'bilingual',
    language: 'both',
    options: {
      group_by_subject: true,
      show_category_info: true,
      show_solutions: false,
      header_text: 'Comprehensive Test - All Subjects'
    }
  })
});
```

### Example 5: Compact Two-Column Layout
```javascript
const response = await fetch('/api/admin/pdf/generate', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    template: 'minimal',
    language: 'english',
    options: {
      two_column_layout: true,
      show_solutions: false,
      show_images: false,
      font_size: 10,
      page_size: 'A4'
    }
  })
});
```

---

## Best Practices

### For Exam Papers
```json
{
  "template": "exam",
  "language": "both",
  "options": {
    "show_solutions": false,
    "show_answer_key": false,
    "show_question_numbers": true,
    "watermark": "CONFIDENTIAL"
  }
}
```

### For Practice Tests
```json
{
  "template": "bilingual",
  "language": "both",
  "options": {
    "show_solutions": true,
    "highlight_correct_answers": true,
    "show_category_info": true
  }
}
```

### For Answer Sheets
```json
{
  "template": "standard",
  "language": "both",
  "options": {
    "show_solutions": true,
    "show_answer_key": true,
    "answers_on_separate_page": true,
    "highlight_correct_answers": true
  }
}
```

---

## Troubleshooting

### PDF Generation Failed
- Check if wkhtmltopdf or LibreOffice is installed
- Verify storage directory permissions
- Check server logs for detailed errors

### Images Not Showing
- Ensure images exist in the specified paths
- Check file permissions on upload directories
- Verify `show_images` option is true

### Hindi Text Not Rendering
- Ensure Noto Sans Devanagari font is available
- Check UTF-8 encoding in the database
- Verify language option is set correctly

---

## System Requirements

### Server Requirements
- PHP 8.1+
- Laravel 10+
- wkhtmltopdf 0.12.6+ OR LibreOffice 7.0+
- Storage write permissions

### Font Requirements
- Noto Sans (for English)
- Noto Sans Devanagari (for Hindi)

### Browser Support (for preview)
- Chrome/Edge 90+
- Firefox 88+
- Safari 14+

---

## Performance Notes

- Average generation time: 2-5 seconds for 50 questions
- File size: ~500KB - 5MB depending on images
- Concurrent generation: Up to 10 simultaneous requests
- Cache: HTML templates are cached for performance

---

## Future Enhancements

1. **PDF Encryption** - Password-protected PDFs
2. **QR Code Integration** - Scannable codes for digital tracking
3. **Custom Fonts** - Upload and use custom fonts
4. **Advanced Layouts** - More template variations
5. **Batch Generation** - Generate multiple PDFs at once
6. **PDF Merging** - Combine multiple question sets

---

## Support

For issues or feature requests, please contact:
- Email: support@easyway.com
- Documentation: /docs/pdf-generation
- API Status: /api/status
