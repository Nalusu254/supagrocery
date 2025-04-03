document.addEventListener('DOMContentLoaded', () => {
    // Check authentication
    const authToken = localStorage.getItem('authToken');
    const userRole = localStorage.getItem('userRole');

    if (!authToken || userRole !== 'agent') {
        window.location.href = '/login';
        return;
    }

    // Elements
    const addProductBtn = document.getElementById('addProductBtn');
    const productModal = document.getElementById('productModal');
    const closeBtn = productModal.querySelector('.close-btn');
    const productForm = document.getElementById('productForm');
    const searchInput = document.getElementById('searchProduct');
    const filterStatus = document.getElementById('filterStatus');
    const productsTableBody = document.getElementById('productsTableBody');
    const logoutBtn = document.getElementById('logoutBtn');
    const modalTitle = document.getElementById('modalTitle');

    // Event Listeners
    addProductBtn.addEventListener('click', () => openModal());
    closeBtn.addEventListener('click', closeModal);
    productForm.addEventListener('submit', handleProductSubmit);
    searchInput.addEventListener('input', debounce(filterProducts, 300));
    filterStatus.addEventListener('change', filterProducts);
    logoutBtn.addEventListener('click', handleLogout);
    document.addEventListener('click', handleTableActions);

    // Image Preview
    const productImage = document.getElementById('productImage');
    const imagePreview = document.getElementById('imagePreview');
    
    productImage.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Load initial data
    loadDashboardData();
    loadProducts();

    // Functions
    async function loadDashboardData() {
        try {
            const response = await fetch('/api/agent/dashboard', {
                headers: {
                    'Authorization': `Bearer ${authToken}`
                }
            });
            const data = await response.json();

            if (data.success) {
                document.getElementById('totalProducts').textContent = data.stats.totalProducts;
                document.getElementById('totalSales').textContent = `$${data.stats.totalSales.toFixed(2)}`;
                document.getElementById('activeOrders').textContent = data.stats.activeOrders;
            }
        } catch (error) {
            console.error('Error loading dashboard data:', error);
            showNotification('Failed to load dashboard data', 'error');
        }
    }

    async function loadProducts(filters = {}) {
        try {
            const queryParams = new URLSearchParams(filters).toString();
            const response = await fetch(`/api/agent/products?${queryParams}`, {
                headers: {
                    'Authorization': `Bearer ${authToken}`
                }
            });
            const data = await response.json();

            if (data.success) {
                renderProducts(data.products);
            }
        } catch (error) {
            console.error('Error loading products:', error);
            showNotification('Failed to load products', 'error');
        }
    }

    function renderProducts(products) {
        productsTableBody.innerHTML = products.map(product => `
            <tr data-product-id="${product.id}">
                <td><img src="${product.image}" alt="${product.name}"></td>
                <td>${product.name}</td>
                <td>$${product.price.toFixed(2)}</td>
                <td>${product.stock}</td>
                <td><span class="product-status status-${product.status.toLowerCase()}">${product.status}</span></td>
                <td>
                    <button class="btn btn-secondary btn-small" data-action="edit">Edit</button>
                    <button class="btn btn-danger btn-small" data-action="delete">Delete</button>
                </td>
            </tr>
        `).join('');
    }

    function handleTableActions(e) {
        const button = e.target.closest('button[data-action]');
        if (!button) return;

        const action = button.dataset.action;
        const productId = button.closest('tr').dataset.productId;

        if (action === 'edit') {
            openModal(productId);
        } else if (action === 'delete') {
            if (confirm('Are you sure you want to delete this product?')) {
                deleteProduct(productId);
            }
        }
    }

    async function handleProductSubmit(e) {
        e.preventDefault();
        const submitButton = productForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.classList.add('btn-loading');

        try {
            const formData = new FormData(productForm);
            const productId = productForm.dataset.productId;
            const url = productId ? 
                `/api/agent/products/${productId}` : 
                '/api/agent/products';

            const response = await fetch(url, {
                method: productId ? 'PUT' : 'POST',
                headers: {
                    'Authorization': `Bearer ${authToken}`
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showNotification(
                    productId ? 'Product updated successfully' : 'Product added successfully',
                    'success'
                );
                closeModal();
                loadProducts();
                loadDashboardData();
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error submitting product:', error);
            showNotification(error.message || 'Failed to save product', 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.classList.remove('btn-loading');
        }
    }

    async function deleteProduct(productId) {
        try {
            const response = await fetch(`/api/agent/products/${productId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${authToken}`
                }
            });

            const data = await response.json();

            if (data.success) {
                showNotification('Product deleted successfully', 'success');
                loadProducts();
                loadDashboardData();
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error deleting product:', error);
            showNotification(error.message || 'Failed to delete product', 'error');
        }
    }

    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusFilter = filterStatus.value;

        loadProducts({
            search: searchTerm,
            status: statusFilter
        });
    }

    async function openModal(productId = null) {
        modalTitle.textContent = productId ? 'Edit Product' : 'Add New Product';
        productForm.reset();
        imagePreview.innerHTML = '';
        productForm.dataset.productId = productId || '';

        if (productId) {
            try {
                const response = await fetch(`/api/agent/products/${productId}`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                const data = await response.json();

                if (data.success) {
                    const product = data.product;
                    Object.keys(product).forEach(key => {
                        const input = productForm.elements[key];
                        if (input) {
                            input.value = product[key];
                        }
                    });

                    if (product.image) {
                        imagePreview.innerHTML = `<img src="${product.image}" alt="Preview">`;
                    }
                }
            } catch (error) {
                console.error('Error loading product details:', error);
                showNotification('Failed to load product details', 'error');
                return;
            }
        }

        productModal.classList.remove('hidden');
    }

    function closeModal() {
        productModal.classList.add('hidden');
        productForm.reset();
        imagePreview.innerHTML = '';
        productForm.dataset.productId = '';
    }

    function handleLogout() {
        localStorage.removeItem('authToken');
        localStorage.removeItem('userRole');
        window.location.href = '/login';
    }

    function showNotification(message, type = 'success') {
        // You can implement a toast notification system here
        alert(message);
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}); 