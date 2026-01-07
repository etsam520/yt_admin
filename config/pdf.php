<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PDF Converter
    |--------------------------------------------------------------------------
    |
    | Specify the PDF converter to use. Available options:
    | - auto (detect automatically)
    | - wkhtmltopdf
    | - libreoffice
    |
    */
    'converter' => env('PDF_CONVERTER', 'auto'),

    /*
    |--------------------------------------------------------------------------
    | PDF Storage
    |--------------------------------------------------------------------------
    |
    | Configure where generated PDFs should be stored
    |
    */
    'storage' => [
        'disk' => 'public',
        'path' => 'pdfs',
        'temp_path' => 'temp/pdf',
    ],

    /*
    |--------------------------------------------------------------------------
    | wkhtmltopdf Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for wkhtmltopdf converter
    |
    */
    'wkhtmltopdf' => [
        'binary' => env('WKHTMLTOPDF_PATH', 'wkhtmltopdf'),
        'options' => [
            'page-size' => 'A4',
            'orientation' => 'Portrait',
            'margin-top' => '15mm',
            'margin-bottom' => '15mm',
            'margin-left' => '15mm',
            'margin-right' => '15mm',
            'encoding' => 'UTF-8',
            'print-media-type' => true,
            'enable-local-file-access' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | LibreOffice Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for LibreOffice converter
    |
    */
    'libreoffice' => [
        'binary' => env('LIBREOFFICE_PATH', 'libreoffice'),
        'options' => [
            'headless' => true,
            'convert-to' => 'pdf',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Template Configuration
    |--------------------------------------------------------------------------
    |
    | Available PDF templates and their configurations
    |
    */
    'templates' => [
        'standard' => [
            'name' => 'Standard Template',
            'description' => 'Clean and simple layout with basic formatting',
            'file' => 'pdf.templates.standard',
        ],
        'modern' => [
            'name' => 'Modern Template', 
            'description' => 'Contemporary design with colored sections',
            'file' => 'pdf.templates.modern',
        ],
        'minimal' => [
            'name' => 'Minimal Template',
            'description' => 'Minimalist design with maximum content focus',
            'file' => 'pdf.templates.minimal',
        ],
        'exam' => [
            'name' => 'Exam Template',
            'description' => 'Formal exam paper layout with answer bubbles',
            'file' => 'pdf.templates.exam',
        ],
        'magazine' => [
            'name' => 'Magazine Template',
            'description' => 'Professional multi-column magazine-style layout with intelligent content flow',
            'file' => 'pdf.templates.magazine',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Options
    |--------------------------------------------------------------------------
    |
    | Default options for PDF generation
    |
    */
    'defaults' => [
        'show_solutions' => false,
        'show_question_numbers' => true,
        'highlight_correct_answers' => false,
        'show_images' => true,
        'page_size' => 'A4',
        'orientation' => 'portrait',
        'font_size' => 12,
        'font_family' => 'Arial, sans-serif',
        'background_color' => '#ffffff',
        'text_color' => '#333333',
        'question_background' => '#f9f9f9',
        
        // Magazine-style layout options
        'column_count' => 2,
        'column_gap' => '8mm',
        'column_rule' => '1px solid #ddd',
        'page_padding' => '15mm',
        
        // Math rendering options
        'render_math' => true,
        'math_engine' => 'katex', // 'katex' or 'mathjax'
        'math_font_scale' => 1.2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Limits and Restrictions
    |--------------------------------------------------------------------------
    |
    | Set limits for PDF generation to prevent abuse
    |
    */
    'limits' => [
        'max_questions_per_pdf' => 100,
        'max_pdf_size_mb' => 50,
        'timeout_seconds' => 120,
        'max_concurrent_generations' => 3,
    ],
];
