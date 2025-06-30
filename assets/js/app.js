/**
 * Enhanced Student Record Management System
 * JavaScript for interactive features, AJAX requests, and UI enhancements
 */

class StudentManager {
    constructor() {
        this.currentPage = 1;
        this.recordsPerPage = 10;
        this.currentSort = { column: 'ID', direction: 'ASC' };
        this.searchTerm = '';
        this.isLoading = false;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadStudents();
    }
    
    bindEvents() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.searchTerm = e.target.value;
                    this.currentPage = 1;
                    this.loadStudents();
                }, 300);
            });
        }
        
        // Records per page selector
        const recordsSelect = document.getElementById('recordsPerPage');
        if (recordsSelect) {
            recordsSelect.addEventListener('change', (e) => {
                this.recordsPerPage = parseInt(e.target.value);
                this.currentPage = 1;
                this.loadStudents();
            });
        }
        
        // Table header sorting
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('sortable')) {
                this.handleSort(e.target);
            }
        });
        
        // Pagination clicks
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('page-link')) {
                e.preventDefault();
                const page = parseInt(e.target.dataset.page);
                if (page && page !== this.currentPage) {
                    this.currentPage = page;
                    this.loadStudents();
                }
            }
        });
        
        // Delete confirmation
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-btn')) {
                e.preventDefault();
                this.confirmDelete(e.target.dataset.id, e.target.dataset.name);
            }
        });
    }
    
    handleSort(header) {
        const column = header.dataset.column;
        
        // Toggle direction if same column, otherwise default to ASC
        if (this.currentSort.column === column) {
            this.currentSort.direction = this.currentSort.direction === 'ASC' ? 'DESC' : 'ASC';
        } else {
            this.currentSort.column = column;
            this.currentSort.direction = 'ASC';
        }
        
        // Update UI
        document.querySelectorAll('.sortable').forEach(th => {
            th.classList.remove('sort-asc', 'sort-desc');
        });
        
        header.classList.add(this.currentSort.direction === 'ASC' ? 'sort-asc' : 'sort-desc');
        
        // Reload data
        this.currentPage = 1;
        this.loadStudents();
    }
    
    async loadStudents() {
        if (this.isLoading) return;
        
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                action: 'list',
                page: this.currentPage,
                limit: this.recordsPerPage,
                search: this.searchTerm,
                sort: this.currentSort.column,
                direction: this.currentSort.direction
            });
            
            const response = await fetch(`api/students.php?${params}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderTable(data.data.records);
                this.renderPagination(data.data.pagination);
                this.updateRecordsInfo(data.data.pagination);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Error loading students:', error);
            this.showError('Failed to load student records. Please try again.');
        } finally {
            this.hideLoading();
        }
    }
    
    renderTable(records) {
        const tbody = document.getElementById('studentsTableBody');
        if (!tbody) return;
        
        if (records.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="13" class="text-center no-data">
                        <i class="fas fa-search"></i>
                        <p>No records found</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = records.map(record => `
            <tr class="fade-in">
                <td>${record.ID}</td>
                <td>${this.escapeHtml(record.NAME)}</td>
                <td>${record.ENG}</td>
                <td>${record.URDU}</td>
                <td>${record.MATHS}</td>
                <td>${record.PHYSICS}</td>
                <td>${record.CHEMISTRY}</td>
                <td>${record.TOTAL}</td>
                <td>500</td>
                <td>${record.PERCENT}%</td>
                <td><span class="grade-badge grade-${record.GRADE.toLowerCase().replace('+', '-plus')}">${record.GRADE}</span></td>
                <td>${record.REMARKS}</td>
                <td>
                    <div class="btn-group-vertical" role="group">
                        <a href="update.php?ID=${record.ID}" class="btn btn-warning btn-sm mb-1">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button class="btn btn-danger btn-sm delete-btn" 
                                data-id="${record.ID}" 
                                data-name="${this.escapeHtml(record.NAME)}">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
    
    renderPagination(pagination) {
        const paginationContainer = document.getElementById('pagination');
        if (!paginationContainer) return;
        
        if (pagination.totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        
        let paginationHTML = '<ul class="pagination justify-content-center">';
        
        // Previous button
        if (pagination.hasPrev) {
            paginationHTML += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.currentPage - 1}">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                </li>
            `;
        }
        
        // Page numbers
        const startPage = Math.max(1, pagination.currentPage - 2);
        const endPage = Math.min(pagination.totalPages, pagination.currentPage + 2);
        
        if (startPage > 1) {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === pagination.currentPage ? 'active' : '';
            paginationHTML += `
                <li class="page-item ${activeClass}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        if (endPage < pagination.totalPages) {
            if (endPage < pagination.totalPages - 1) {
                paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.totalPages}">${pagination.totalPages}</a></li>`;
        }
        
        // Next button
        if (pagination.hasNext) {
            paginationHTML += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.currentPage + 1}">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            `;
        }
        
        paginationHTML += '</ul>';
        paginationContainer.innerHTML = paginationHTML;
    }
    
    updateRecordsInfo(pagination) {
        const infoElement = document.getElementById('recordsInfo');
        if (!infoElement) return;
        
        const start = (pagination.currentPage - 1) * pagination.recordsPerPage + 1;
        const end = Math.min(start + pagination.recordsPerPage - 1, pagination.totalRecords);
        
        infoElement.textContent = `Showing ${start} to ${end} of ${pagination.totalRecords} records`;
    }
    
    confirmDelete(id, name) {
        if (confirm(`Are you sure you want to delete the record for "${name}"? This action cannot be undone.`)) {
            this.deleteStudent(id);
        }
    }
    
    async deleteStudent(id) {
        this.showLoading();
        
        try {
            const response = await fetch(`api/students.php?action=delete&id=${id}`, {
                method: 'DELETE'
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess('Student record deleted successfully');
                this.loadStudents();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Error deleting student:', error);
            this.showError('Failed to delete student record. Please try again.');
        } finally {
            this.hideLoading();
        }
    }
    
    showLoading() {
        this.isLoading = true;
        const tableContainer = document.querySelector('.table-container');
        if (tableContainer) {
            tableContainer.classList.add('table-loading');
        }
    }
    
    hideLoading() {
        this.isLoading = false;
        const tableContainer = document.querySelector('.table-container');
        if (tableContainer) {
            tableContainer.classList.remove('table-loading');
        }
    }
    
    showSuccess(message) {
        this.showAlert(message, 'success');
    }
    
    showError(message) {
        this.showAlert(message, 'danger');
    }
    
    showAlert(message, type) {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) return;
        
        const alertHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        alertContainer.innerHTML = alertHTML;
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Form validation and enhancement
class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        if (this.form) {
            this.init();
        }
    }
    
    init() {
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        this.form.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', () => this.validateMarks(input));
        });
        
        this.form.querySelectorAll('input[required]').forEach(input => {
            input.addEventListener('blur', () => this.validateRequired(input));
        });
    }
    
    validateForm() {
        let isValid = true;
        
        // Validate required fields
        this.form.querySelectorAll('input[required]').forEach(input => {
            if (!this.validateRequired(input)) {
                isValid = false;
            }
        });
        
        // Validate marks
        this.form.querySelectorAll('input[type="number"]').forEach(input => {
            if (!this.validateMarks(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    validateRequired(input) {
        const isValid = input.value.trim() !== '';
        this.setFieldState(input, isValid, isValid ? '' : 'This field is required');
        return isValid;
    }
    
    validateMarks(input) {
        const value = parseInt(input.value);
        const isValid = !isNaN(value) && value >= 0 && value <= 100;
        this.setFieldState(input, isValid, isValid ? '' : 'Marks must be between 0 and 100');
        return isValid;
    }
    
    setFieldState(input, isValid, message) {
        const feedback = input.parentNode.querySelector('.invalid-feedback') || 
                        this.createFeedbackElement(input);
        
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            feedback.textContent = '';
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            feedback.textContent = message;
        }
    }
    
    createFeedbackElement(input) {
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        input.parentNode.appendChild(feedback);
        return feedback;
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize student manager on index page
    if (document.getElementById('studentsTableBody')) {
        new StudentManager();
    }
    
    // Initialize form validator on create/update pages
    if (document.getElementById('studentForm')) {
        new FormValidator('studentForm');
    }
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
});
