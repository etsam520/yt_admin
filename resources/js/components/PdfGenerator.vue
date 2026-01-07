<template>
  <div class="pdf-generator">
    <div class="pdf-header">
      <h2>📄 Dynamic PDF Generator</h2>
      <p>Create customized question papers with multiple template options</p>
    </div>

    <!-- Step 1: Select Questions -->
    <div class="step-section" :class="{ active: currentStep === 1 }">
      <h3>Step 1: Select Questions</h3>
      <div class="question-selector">
        <div class="search-filters">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search questions..."
            class="search-input"
          >
          <select v-model="selectedCategory" class="filter-select">
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
        
        <div class="question-list">
          <div 
            v-for="question in filteredQuestions" 
            :key="question.id"
            class="question-item"
            :class="{ selected: selectedQuestions.includes(question.id) }"
            @click="toggleQuestion(question.id)"
          >
            <div class="question-preview">
              <span class="question-number">#{{ question.id }}</span>
              <span class="question-text">{{ truncateText(question.question_en, 100) }}</span>
              <span class="question-type">{{ question.type }}</span>
            </div>
            <div class="selection-indicator">
              <i class="fas fa-check" v-if="selectedQuestions.includes(question.id)"></i>
            </div>
          </div>
        </div>
        
        <div class="selected-summary">
          <span>Selected: {{ selectedQuestions.length }} questions</span>
          <button @click="clearSelection" class="btn-secondary">Clear All</button>
        </div>
      </div>
    </div>

    <!-- Step 2: Choose Template -->
    <div class="step-section" :class="{ active: currentStep === 2 }">
      <h3>Step 2: Choose Template</h3>
      <div class="template-grid">
        <div 
          v-for="template in templates" 
          :key="template.id"
          class="template-card"
          :class="{ selected: selectedTemplate === template.id }"
          @click="selectedTemplate = template.id"
        >
          <div class="template-preview">
            <img :src="template.preview" :alt="template.name" />
          </div>
          <div class="template-info">
            <h4>{{ template.name }}</h4>
            <p>{{ template.description }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Step 3: Customize Options -->
    <div class="step-section" :class="{ active: currentStep === 3 }">
      <h3>Step 3: Customize Options</h3>
      <div class="options-grid">
        <!-- Display Options -->
        <div class="option-group">
          <h4>Display Options</h4>
          <div class="option-item">
            <label class="checkbox-label">
              <input type="checkbox" v-model="pdfOptions.show_solutions">
              <span class="checkmark"></span>
              Show Solutions
            </label>
          </div>
          <div class="option-item">
            <label class="checkbox-label">
              <input type="checkbox" v-model="pdfOptions.show_question_numbers">
              <span class="checkmark"></span>
              Show Question Numbers
            </label>
          </div>
          <div class="option-item">
            <label class="checkbox-label">
              <input type="checkbox" v-model="pdfOptions.highlight_correct_answers">
              <span class="checkmark"></span>
              Highlight Correct Answers
            </label>
          </div>
          <div class="option-item">
            <label class="checkbox-label">
              <input type="checkbox" v-model="pdfOptions.show_images">
              <span class="checkmark"></span>
              Show Images
            </label>
          </div>
        </div>

        <!-- Layout Options -->
        <div class="option-group">
          <h4>Layout Options</h4>
          <div class="option-item">
            <label>Page Size:</label>
            <select v-model="pdfOptions.page_size" class="select-input">
              <option value="A4">A4</option>
              <option value="A3">A3</option>
              <option value="Letter">Letter</option>
            </select>
          </div>
          <div class="option-item">
            <label>Orientation:</label>
            <select v-model="pdfOptions.orientation" class="select-input">
              <option value="portrait">Portrait</option>
              <option value="landscape">Landscape</option>
            </select>
          </div>
          <div class="option-item">
            <label>Font Size:</label>
            <input 
              type="range" 
              v-model="pdfOptions.font_size" 
              min="8" 
              max="20" 
              class="range-input"
            >
            <span>{{ pdfOptions.font_size }}px</span>
          </div>
        </div>

        <!-- Color Options -->
        <div class="option-group">
          <h4>Color Options</h4>
          <div class="option-item">
            <label>Background Color:</label>
            <input 
              type="color" 
              v-model="pdfOptions.background_color" 
              class="color-input"
            >
          </div>
          <div class="option-item">
            <label>Text Color:</label>
            <input 
              type="color" 
              v-model="pdfOptions.text_color" 
              class="color-input"
            >
          </div>
          <div class="option-item">
            <label>Question Background:</label>
            <input 
              type="color" 
              v-model="pdfOptions.question_background" 
              class="color-input"
            >
          </div>
        </div>

        <!-- Header/Footer Options -->
        <div class="option-group">
          <h4>Header & Footer</h4>
          <div class="option-item">
            <label>Header Text:</label>
            <input 
              type="text" 
              v-model="pdfOptions.header_text" 
              placeholder="e.g., Math Quiz - Grade 10"
              class="text-input"
            >
          </div>
          <div class="option-item">
            <label>Footer Text:</label>
            <input 
              type="text" 
              v-model="pdfOptions.footer_text" 
              placeholder="e.g., Page"
              class="text-input"
            >
          </div>
          <div class="option-item">
            <label>Watermark:</label>
            <input 
              type="text" 
              v-model="pdfOptions.watermark" 
              placeholder="e.g., CONFIDENTIAL"
              class="text-input"
            >
          </div>
        </div>
      </div>
    </div>

    <!-- Preview Section -->
    <div class="preview-section" v-if="currentStep >= 2">
      <h3>Preview</h3>
      <div class="preview-actions">
        <button @click="generatePreview" class="btn-secondary" :disabled="isGenerating">
          <i class="fas fa-eye"></i> Generate Preview
        </button>
        <button @click="generatePDF" class="btn-primary" :disabled="isGenerating || selectedQuestions.length === 0">
          <i class="fas fa-file-pdf"></i> Generate PDF
        </button>
      </div>
      
      <div v-if="previewHtml" class="preview-container">
        <iframe 
          :srcdoc="previewHtml" 
          class="preview-iframe"
          @load="onPreviewLoad"
        ></iframe>
      </div>
      
      <div v-if="isGenerating" class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
        <span>{{ loadingText }}</span>
      </div>
    </div>

    <!-- Navigation -->
    <div class="navigation">
      <button 
        @click="previousStep" 
        class="btn-secondary" 
        :disabled="currentStep === 1"
      >
        <i class="fas fa-arrow-left"></i> Previous
      </button>
      <div class="step-indicators">
        <span 
          v-for="step in 3" 
          :key="step"
          class="step-indicator"
          :class="{ active: currentStep === step, completed: currentStep > step }"
        >
          {{ step }}
        </span>
      </div>
      <button 
        @click="nextStep" 
        class="btn-primary" 
        :disabled="currentStep === 3 || (currentStep === 1 && selectedQuestions.length === 0)"
      >
        Next <i class="fas fa-arrow-right"></i>
      </button>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="modal-overlay" @click="showSuccessModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3><i class="fas fa-check-circle text-success"></i> PDF Generated Successfully!</h3>
        </div>
        <div class="modal-body">
          <p>Your PDF has been generated and is ready for download.</p>
          <div class="download-actions">
            <a :href="pdfDownloadUrl" class="btn-primary" download>
              <i class="fas fa-download"></i> Download PDF
            </a>
            <a :href="pdfViewUrl" class="btn-secondary" target="_blank">
              <i class="fas fa-external-link-alt"></i> View PDF
            </a>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showSuccessModal = false" class="btn-secondary">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'PdfGenerator',
  data() {
    return {
      currentStep: 1,
      searchQuery: '',
      selectedCategory: '',
      selectedQuestions: [],
      selectedTemplate: 'standard',
      questions: [],
      categories: [],
      templates: [],
      previewHtml: '',
      isGenerating: false,
      loadingText: 'Generating...',
      showSuccessModal: false,
      pdfDownloadUrl: '',
      pdfViewUrl: '',
      
      pdfOptions: {
        show_solutions: false,
        show_question_numbers: true,
        highlight_correct_answers: false,
        show_images: true,
        page_size: 'A4',
        orientation: 'portrait',
        font_size: 12,
        background_color: '#ffffff',
        text_color: '#333333',
        question_background: '#f9f9f9',
        header_text: 'Question Paper',
        footer_text: '',
        watermark: ''
      }
    }
  },
  
  computed: {
    filteredQuestions() {
      return this.questions.filter(question => {
        const matchesSearch = !this.searchQuery || 
          question.question_en.toLowerCase().includes(this.searchQuery.toLowerCase())
        const matchesCategory = !this.selectedCategory || 
          question.category_id === parseInt(this.selectedCategory)
        return matchesSearch && matchesCategory
      })
    }
  },
  
  mounted() {
    this.loadQuestions()
    this.loadCategories()
    this.loadTemplates()
  },
  
  methods: {
    async loadQuestions() {
      try {
        const response = await axios.get('/api/admin/questions')
        this.questions = response.data.data || []
      } catch (error) {
        console.error('Error loading questions:', error)
      }
    },
    
    async loadCategories() {
      try {
        const response = await axios.get('/api/admin/categories')
        this.categories = response.data.data || []
      } catch (error) {
        console.error('Error loading categories:', error)
      }
    },
    
    async loadTemplates() {
      try {
        const response = await axios.get('/api/admin/pdf/templates')
        this.templates = response.data.templates || []
      } catch (error) {
        console.error('Error loading templates:', error)
      }
    },
    
    toggleQuestion(questionId) {
      const index = this.selectedQuestions.indexOf(questionId)
      if (index > -1) {
        this.selectedQuestions.splice(index, 1)
      } else {
        this.selectedQuestions.push(questionId)
      }
    },
    
    clearSelection() {
      this.selectedQuestions = []
    },
    
    nextStep() {
      if (this.currentStep < 3) {
        this.currentStep++
      }
    },
    
    previousStep() {
      if (this.currentStep > 1) {
        this.currentStep--
      }
    },
    
    async generatePreview() {
      if (this.selectedQuestions.length === 0) return
      
      this.isGenerating = true
      this.loadingText = 'Generating preview...'
      
      try {
        const response = await axios.post('/api/admin/pdf/preview', {
          questions: this.selectedQuestions.slice(0, 3), // Preview first 3 questions
          template: this.selectedTemplate,
          options: this.pdfOptions
        })
        
        this.previewHtml = response.data.html_content
      } catch (error) {
        console.error('Error generating preview:', error)
        alert('Error generating preview. Please try again.')
      } finally {
        this.isGenerating = false
      }
    },
    
    async generatePDF() {
      if (this.selectedQuestions.length === 0) return
      
      this.isGenerating = true
      this.loadingText = 'Generating PDF...'
      
      try {
        const response = await axios.post('/api/admin/pdf/generate-questions', {
          questions: this.selectedQuestions,
          template: this.selectedTemplate,
          options: this.pdfOptions
        })
        
        this.pdfDownloadUrl = response.data.download_url
        this.pdfViewUrl = response.data.pdf_url
        this.showSuccessModal = true
        
      } catch (error) {
        console.error('Error generating PDF:', error)
        alert('Error generating PDF. Please try again.')
      } finally {
        this.isGenerating = false
      }
    },
    
    onPreviewLoad() {
      // Preview loaded successfully
    },
    
    truncateText(text, length) {
      return text && text.length > length ? text.substring(0, length) + '...' : text
    }
  }
}
</script>

<style scoped>
.pdf-generator {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.pdf-header {
  text-align: center;
  margin-bottom: 40px;
}

.pdf-header h2 {
  color: #2c3e50;
  margin-bottom: 10px;
}

.step-section {
  background: white;
  border-radius: 12px;
  padding: 25px;
  margin-bottom: 25px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  border: 2px solid transparent;
}

.step-section.active {
  border-color: #007bff;
}

.step-section h3 {
  color: #2c3e50;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
}

/* Question Selector Styles */
.search-filters {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
}

.search-input, .filter-select {
  padding: 10px 15px;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-size: 14px;
  transition: border-color 0.3s;
}

.search-input:focus, .filter-select:focus {
  outline: none;
  border-color: #007bff;
}

.search-input {
  flex: 1;
}

.question-list {
  max-height: 400px;
  overflow-y: auto;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  margin-bottom: 15px;
}

.question-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  border-bottom: 1px solid #f8f9fa;
  cursor: pointer;
  transition: all 0.3s;
}

.question-item:hover {
  background: #f8f9fa;
}

.question-item.selected {
  background: #e3f2fd;
  border-left: 4px solid #2196f3;
}

.question-preview {
  flex: 1;
  display: flex;
  gap: 15px;
  align-items: center;
}

.question-number {
  font-weight: bold;
  color: #666;
  min-width: 50px;
}

.question-text {
  flex: 1;
  color: #2c3e50;
}

.question-type {
  background: #e9ecef;
  color: #495057;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
}

.selection-indicator {
  color: #28a745;
  font-size: 18px;
}

.selected-summary {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 15px;
  background: #f8f9fa;
  border-radius: 8px;
}

/* Template Grid Styles */
.template-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.template-card {
  border: 2px solid #e9ecef;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s;
}

.template-card:hover {
  border-color: #007bff;
  transform: translateY(-2px);
}

.template-card.selected {
  border-color: #007bff;
  box-shadow: 0 4px 20px rgba(0,123,255,0.3);
}

.template-preview img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.template-info {
  padding: 15px;
}

.template-info h4 {
  margin: 0 0 8px 0;
  color: #2c3e50;
}

.template-info p {
  margin: 0;
  color: #666;
  font-size: 14px;
}

/* Options Grid Styles */
.options-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 25px;
}

.option-group {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 20px;
}

.option-group h4 {
  margin: 0 0 15px 0;
  color: #2c3e50;
  border-bottom: 2px solid #e9ecef;
  padding-bottom: 8px;
}

.option-item {
  margin-bottom: 15px;
}

.option-item label {
  display: block;
  margin-bottom: 5px;
  color: #495057;
  font-weight: 500;
}

.checkbox-label {
  display: flex !important;
  align-items: center;
  cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
  margin-right: 10px;
}

.text-input, .select-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
}

.range-input {
  width: 100%;
  margin: 5px 0;
}

.color-input {
  width: 60px;
  height: 40px;
  border: 1px solid #ddd;
  border-radius: 6px;
  cursor: pointer;
}

/* Preview Styles */
.preview-section {
  background: white;
  border-radius: 12px;
  padding: 25px;
  margin-bottom: 25px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.preview-actions {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
}

.preview-container {
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
}

.preview-iframe {
  width: 100%;
  height: 600px;
  border: none;
}

.loading-spinner {
  text-align: center;
  padding: 40px;
  color: #666;
}

.loading-spinner i {
  font-size: 24px;
  margin-bottom: 10px;
  display: block;
}

/* Navigation Styles */
.navigation {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 30px;
}

.step-indicators {
  display: flex;
  gap: 10px;
}

.step-indicator {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  background: #e9ecef;
  color: #666;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  transition: all 0.3s;
}

.step-indicator.active {
  background: #007bff;
  color: white;
}

.step-indicator.completed {
  background: #28a745;
  color: white;
}

/* Button Styles */
.btn-primary, .btn-secondary {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #0056b3;
  transform: translateY(-1px);
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background: #545b62;
}

.btn-primary:disabled, .btn-secondary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  padding: 0;
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow: auto;
}

.modal-header {
  padding: 20px 25px;
  border-bottom: 1px solid #e9ecef;
}

.modal-header h3 {
  margin: 0;
  color: #2c3e50;
}

.modal-body {
  padding: 25px;
}

.modal-footer {
  padding: 15px 25px;
  border-top: 1px solid #e9ecef;
  text-align: right;
}

.download-actions {
  display: flex;
  gap: 15px;
  margin-top: 20px;
}

.text-success {
  color: #28a745;
}

/* Responsive Design */
@media (max-width: 768px) {
  .pdf-generator {
    padding: 15px;
  }
  
  .options-grid {
    grid-template-columns: 1fr;
  }
  
  .template-grid {
    grid-template-columns: 1fr;
  }
  
  .navigation {
    flex-direction: column;
    gap: 20px;
  }
  
  .preview-actions {
    flex-direction: column;
  }
  
  .download-actions {
    flex-direction: column;
  }
}
</style>
