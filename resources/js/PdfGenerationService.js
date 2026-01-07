// PDF Generation Helper - Frontend Integration Example
// Use this in your React/Vue/Angular application

/**
 * PDF Generation Service
 */
class PdfGenerationService {
  constructor(baseUrl = '/api/admin/pdf') {
    this.baseUrl = baseUrl;
  }

  /**
   * Generate PDF with custom options
   * @param {Object} config - PDF configuration
   * @returns {Promise<Object>} Response with PDF URL
   */
  async generatePdf(config = {}) {
    const {
      questionIds = [],
      template = 'bilingual',
      language = 'both',
      options = {}
    } = config;

    const payload = {
      questions: questionIds.length > 0 ? questionIds : undefined,
      template,
      language,
      options: {
        // Default options
        show_solutions: true,
        show_question_numbers: true,
        show_category_info: true,
        page_size: 'A4',
        orientation: 'portrait',
        font_size: 12,
        ...options
      }
    };

    try {
      const response = await fetch(`${this.baseUrl}/generate`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': this.getCsrfToken()
        },
        body: JSON.stringify(payload)
      });

      if (!response.ok) {
        throw new Error('PDF generation failed');
      }

      return await response.json();
    } catch (error) {
      console.error('PDF Generation Error:', error);
      throw error;
    }
  }

  /**
   * Preview PDF HTML
   * @param {Object} config - Preview configuration
   * @returns {Promise<String>} HTML content
   */
  async previewPdf(config = {}) {
    const {
      questionIds = [],
      template = 'bilingual',
      language = 'both',
      options = {}
    } = config;

    const payload = {
      questions: questionIds,
      template,
      language,
      options
    };

    try {
      const response = await fetch(`${this.baseUrl}/preview`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': this.getCsrfToken()
        },
        body: JSON.stringify(payload)
      });

      if (!response.ok) {
        throw new Error('Preview generation failed');
      }

      const data = await response.json();
      return data.html_content;
    } catch (error) {
      console.error('Preview Error:', error);
      throw error;
    }
  }

  /**
   * Get available templates and options
   * @returns {Promise<Object>} Templates and options
   */
  async getTemplates() {
    try {
      const response = await fetch(`${this.baseUrl}/templates`, {
        headers: {
          'Accept': 'application/json'
        }
      });

      if (!response.ok) {
        throw new Error('Failed to fetch templates');
      }

      return await response.json();
    } catch (error) {
      console.error('Get Templates Error:', error);
      throw error;
    }
  }

  /**
   * Test PDF generation
   * @returns {Promise<Object>} Test PDF response
   */
  async testGeneration() {
    try {
      const response = await fetch(`${this.baseUrl}/test`, {
        headers: {
          'Accept': 'application/json'
        }
      });

      if (!response.ok) {
        throw new Error('Test generation failed');
      }

      return await response.json();
    } catch (error) {
      console.error('Test Error:', error);
      throw error;
    }
  }

  /**
   * Get CSRF token from meta tag
   * @returns {String} CSRF token
   */
  getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
  }
}

// Export for use
export default PdfGenerationService;

// ========================================
// USAGE EXAMPLES
// ========================================

// Example 1: Basic Usage
const pdfService = new PdfGenerationService();

async function generateBasicPdf() {
  try {
    const result = await pdfService.generatePdf({
      questionIds: [1, 2, 3, 4, 5],
      template: 'bilingual',
      language: 'both'
    });
    
    console.log('PDF URL:', result.pdf_url);
    window.open(result.pdf_url, '_blank');
  } catch (error) {
    alert('Failed to generate PDF');
  }
}

// Example 2: Hindi-Only Exam Paper
async function generateHindiExam() {
  const result = await pdfService.generatePdf({
    template: 'exam',
    language: 'hindi',
    options: {
      show_solutions: false,
      header_text: 'प्रतियोगी परीक्षा 2025',
      watermark: 'गोपनीय'
    }
  });
  
  return result.pdf_url;
}

// Example 3: English Practice Test with Solutions
async function generatePracticeTest() {
  const result = await pdfService.generatePdf({
    questionIds: [10, 11, 12, 13, 14, 15],
    template: 'bilingual',
    language: 'english',
    options: {
      show_solutions: true,
      highlight_correct_answers: true,
      show_category_info: true,
      header_text: 'Practice Test - Mathematics',
      footer_text: '© 2025 EasyWay Education'
    }
  });
  
  return result.pdf_url;
}

// Example 4: Bilingual with Answer Key
async function generateWithAnswerKey() {
  const result = await pdfService.generatePdf({
    template: 'bilingual',
    language: 'both',
    options: {
      show_solutions: true,
      show_answer_key: true,
      answers_on_separate_page: true,
      group_by_subject: true
    }
  });
  
  return result.pdf_url;
}

// Example 5: Preview Before Generating
async function previewAndGenerate(questionIds) {
  // First preview
  const htmlPreview = await pdfService.previewPdf({
    questionIds,
    template: 'bilingual',
    language: 'both'
  });
  
  // Show preview in modal/iframe
  document.getElementById('preview-container').innerHTML = htmlPreview;
  
  // If user confirms, generate PDF
  const confirmed = confirm('Generate PDF?');
  if (confirmed) {
    const result = await pdfService.generatePdf({
      questionIds,
      template: 'bilingual',
      language: 'both'
    });
    
    window.open(result.pdf_url, '_blank');
  }
}

// Example 6: Get Templates for UI
async function loadTemplateOptions() {
  const data = await pdfService.getTemplates();
  
  // Populate template dropdown
  const templateSelect = document.getElementById('template-select');
  data.templates.forEach(template => {
    const option = document.createElement('option');
    option.value = template.id;
    option.textContent = template.name;
    templateSelect.appendChild(option);
  });
  
  // Populate language dropdown
  const languageSelect = document.getElementById('language-select');
  data.language_options.forEach(lang => {
    const option = document.createElement('option');
    option.value = lang.id;
    option.textContent = lang.name;
    languageSelect.appendChild(option);
  });
}

// ========================================
// REACT COMPONENT EXAMPLE
// ========================================

/*
import React, { useState, useEffect } from 'react';
import PdfGenerationService from './PdfGenerationService';

const PdfGenerator = ({ questionIds = [] }) => {
  const [templates, setTemplates] = useState([]);
  const [languages, setLanguages] = useState([]);
  const [selectedTemplate, setSelectedTemplate] = useState('bilingual');
  const [selectedLanguage, setSelectedLanguage] = useState('both');
  const [loading, setLoading] = useState(false);
  const [options, setOptions] = useState({
    show_solutions: true,
    show_question_numbers: true,
    show_answer_key: false,
    highlight_correct_answers: false,
    show_category_info: true,
    group_by_subject: false,
    header_text: '',
    footer_text: ''
  });

  const pdfService = new PdfGenerationService();

  useEffect(() => {
    loadTemplates();
  }, []);

  const loadTemplates = async () => {
    try {
      const data = await pdfService.getTemplates();
      setTemplates(data.templates);
      setLanguages(data.language_options);
    } catch (error) {
      console.error('Failed to load templates', error);
    }
  };

  const handleGenerate = async () => {
    setLoading(true);
    try {
      const result = await pdfService.generatePdf({
        questionIds,
        template: selectedTemplate,
        language: selectedLanguage,
        options
      });
      
      // Open PDF in new tab
      window.open(result.pdf_url, '_blank');
      
      // Or download directly
      // window.location.href = result.pdf_url;
      
    } catch (error) {
      alert('Failed to generate PDF');
    } finally {
      setLoading(false);
    }
  };

  const handlePreview = async () => {
    try {
      const html = await pdfService.previewPdf({
        questionIds,
        template: selectedTemplate,
        language: selectedLanguage,
        options
      });
      
      // Show preview in modal
      const modal = document.createElement('div');
      modal.innerHTML = html;
      document.body.appendChild(modal);
      
    } catch (error) {
      alert('Failed to generate preview');
    }
  };

  return (
    <div className="pdf-generator">
      <h2>Generate PDF</h2>
      
      <div className="form-group">
        <label>Template:</label>
        <select value={selectedTemplate} onChange={(e) => setSelectedTemplate(e.target.value)}>
          {templates.map(t => (
            <option key={t.id} value={t.id}>{t.name}</option>
          ))}
        </select>
      </div>

      <div className="form-group">
        <label>Language:</label>
        <select value={selectedLanguage} onChange={(e) => setSelectedLanguage(e.target.value)}>
          {languages.map(l => (
            <option key={l.id} value={l.id}>{l.name}</option>
          ))}
        </select>
      </div>

      <div className="form-group">
        <label>
          <input
            type="checkbox"
            checked={options.show_solutions}
            onChange={(e) => setOptions({...options, show_solutions: e.target.checked})}
          />
          Show Solutions
        </label>
      </div>

      <div className="form-group">
        <label>
          <input
            type="checkbox"
            checked={options.show_answer_key}
            onChange={(e) => setOptions({...options, show_answer_key: e.target.checked})}
          />
          Show Answer Key
        </label>
      </div>

      <div className="form-group">
        <label>Header Text:</label>
        <input
          type="text"
          value={options.header_text}
          onChange={(e) => setOptions({...options, header_text: e.target.value})}
          placeholder="e.g., Sample Exam Paper"
        />
      </div>

      <div className="button-group">
        <button onClick={handlePreview} disabled={loading}>
          Preview
        </button>
        <button onClick={handleGenerate} disabled={loading}>
          {loading ? 'Generating...' : 'Generate PDF'}
        </button>
      </div>
    </div>
  );
};

export default PdfGenerator;
*/

// ========================================
// VUE COMPONENT EXAMPLE
// ========================================

/*
<template>
  <div class="pdf-generator">
    <h2>Generate PDF</h2>
    
    <div class="form-group">
      <label>Template:</label>
      <select v-model="selectedTemplate">
        <option v-for="t in templates" :key="t.id" :value="t.id">
          {{ t.name }}
        </option>
      </select>
    </div>

    <div class="form-group">
      <label>Language:</label>
      <select v-model="selectedLanguage">
        <option v-for="l in languages" :key="l.id" :value="l.id">
          {{ l.name }}
        </option>
      </select>
    </div>

    <div class="form-group">
      <label>
        <input type="checkbox" v-model="options.show_solutions" />
        Show Solutions
      </label>
    </div>

    <div class="form-group">
      <label>
        <input type="checkbox" v-model="options.show_answer_key" />
        Show Answer Key
      </label>
    </div>

    <div class="button-group">
      <button @click="handlePreview" :disabled="loading">Preview</button>
      <button @click="handleGenerate" :disabled="loading">
        {{ loading ? 'Generating...' : 'Generate PDF' }}
      </button>
    </div>
  </div>
</template>

<script>
import PdfGenerationService from './PdfGenerationService';

export default {
  name: 'PdfGenerator',
  props: {
    questionIds: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      pdfService: new PdfGenerationService(),
      templates: [],
      languages: [],
      selectedTemplate: 'bilingual',
      selectedLanguage: 'both',
      loading: false,
      options: {
        show_solutions: true,
        show_question_numbers: true,
        show_answer_key: false,
        highlight_correct_answers: false,
        show_category_info: true
      }
    };
  },
  mounted() {
    this.loadTemplates();
  },
  methods: {
    async loadTemplates() {
      try {
        const data = await this.pdfService.getTemplates();
        this.templates = data.templates;
        this.languages = data.language_options;
      } catch (error) {
        console.error('Failed to load templates', error);
      }
    },
    async handleGenerate() {
      this.loading = true;
      try {
        const result = await this.pdfService.generatePdf({
          questionIds: this.questionIds,
          template: this.selectedTemplate,
          language: this.selectedLanguage,
          options: this.options
        });
        
        window.open(result.pdf_url, '_blank');
      } catch (error) {
        alert('Failed to generate PDF');
      } finally {
        this.loading = false;
      }
    },
    async handlePreview() {
      try {
        const html = await this.pdfService.previewPdf({
          questionIds: this.questionIds,
          template: this.selectedTemplate,
          language: this.selectedLanguage,
          options: this.options
        });
        
        // Show preview
        this.$emit('preview', html);
      } catch (error) {
        alert('Failed to generate preview');
      }
    }
  }
};
</script>
*/
