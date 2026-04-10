// public/js/operator/dashboard.js

// Modal functions
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('open');
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('open');
    }
}

// Close modal when clicking overlay
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this && this.id) {
            closeModal(this.id);
        }
    });
});

// Open edit modal with product data
function openEditModal(id, code, name, category) {
    if (!id) {
        console.error('Product ID is required');
        return;
    }
    
    const updateUrl = `/operator/products/${id}`;
    const formEdit = document.getElementById('form-edit');
    const editCode = document.getElementById('edit-code');
    const editName = document.getElementById('edit-name');
    const editCategory = document.getElementById('edit-category');
    
    if (formEdit) formEdit.action = updateUrl;
    if (editCode) editCode.value = code || '';
    if (editName) editName.value = name || '';
    if (editCategory) editCategory.value = category || '';
    
    openModal('modal-edit');
}

// Preview image before upload
function previewImage(input, previewId, labelId) {
    const container = document.getElementById(previewId);
    const label = document.getElementById(labelId);
    const file = input?.files?.[0];
    
    if (container && file) {
        const img = container.querySelector('img');
        if (img) {
            img.src = URL.createObjectURL(file);
            container.classList.remove('hidden');
        }
    }
    
    if (label && file) {
        label.textContent = file.name;
    }
}

// Search and filter functionality
function initSearchAndFilter() {
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');
    const categoryFilter = document.getElementById('category-filter');
    const dashboardUrl = document.querySelector('meta[name="dashboard-url"]')?.content || '/operator/dashboard';

    function applySearchAndFilter() {
        const searchValue = searchInput?.value || '';
        const categoryValue = categoryFilter?.value || '';
        
        let url = dashboardUrl + '?';
        const params = [];
        
        if (searchValue) {
            params.push('search=' + encodeURIComponent(searchValue));
        }
        
        if (categoryValue && categoryValue !== '') {
            params.push('category_id=' + encodeURIComponent(categoryValue));
        }
        
        url += params.join('&');
        
        // Redirect jika ada parameter atau jika url berbeda
        if (params.length > 0 || window.location.search) {
            window.location.href = url;
        }
    }

    // Search button click
    if (searchButton) {
        searchButton.addEventListener('click', applySearchAndFilter);
    }

    // Enter key on search input
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applySearchAndFilter();
            }
        });
    }

    // Category filter change
    if (categoryFilter) {
        categoryFilter.addEventListener('change', applySearchAndFilter);
    }

    // Set value from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    if (searchParam && searchInput) {
        searchInput.value = searchParam;
    }
}

// Toast notification
function initToast() {
    const toast = document.getElementById('toast-box');
    if (toast) {
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => toast.classList.remove('show'), 3000);
    }
}

// Initialize all functions when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initSearchAndFilter();
    initToast();
});