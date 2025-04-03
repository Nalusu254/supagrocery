document.addEventListener('DOMContentLoaded', () => {
    // Initialize filters and sorting
    initializeFilters();
    // Load initial products
    loadProducts();
});

let currentPage = 1;
let currentFilters = {
    categories: [],
    minPrice: null,
    maxPrice: null
};
let currentSort = 'name';

function initializeFilters() {
    // Category filters
    document.querySelectorAll('input[name="category"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            currentFilters.categories = Array.from(document.querySelectorAll('input[name="category"]:checked'))
                .map(input => input.value);
        });
    });

    // Price range
    const priceRange = document.getElementById('priceRange');
    const minPrice = document.getElementById('minPrice');
    const maxPrice = document.getElementById('maxPrice');

    if (priceRange) {
        priceRange.addEventListener('input', (e) => {
            const value = e.target.value;
            maxPrice.value = value;
        });
    }

    if (minPrice && maxPrice) {
        minPrice.addEventListener('change', () => {
            currentFilters.minPrice = minPrice.value ? parseFloat(minPrice.value) : null;
        });

        maxPrice.addEventListener('change', () => {
            currentFilters.maxPrice = maxPrice.value ? parseFloat(maxPrice.value) : null;
        });
    }

    // Apply filters button
    const applyFiltersBtn = document.getElementById('applyFilters');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', () => {
            currentPage = 1;
            loadProducts();
        });
    }

    // Sort selection
    const sortSelect = document.getElementById('sort');
    if (sortSelect) {
        sortSelect.addEventListener('change', (e) => {
            currentSort = e.target.value;
            loadProducts();
        });
    }

    // Pagination
    const prevButton = document.getElementById('prevPage');
    const nextButton = document.getElementById('nextPage');

    if (prevButton && nextButton) {
        prevButton.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                loadProducts();
            }
        });

        nextButton.addEventListener('click', () => {
            currentPage++;
            loadProducts();
        });
    }
}

async function loadProducts() {
    const productGrid = document.querySelector('.product-grid');
    if (!productGrid) return;

    try {
        productGrid.innerHTML = '<div class="loading">Loading products...</div>';

        const queryParams = new URLSearchParams({
            page: currentPage,
            sort: currentSort,
            ...currentFilters.minPrice && { minPrice: currentFilters.minPrice },
            ...currentFilters.maxPrice && { maxPrice: currentFilters.maxPrice },
            ...currentFilters.categories.length && { categories: currentFilters.categories.join(',') }
        });

        const response = await fetch(`/api/products?${queryParams}`);
        const data = await response.json();

        if (data.products && Array.isArray(data.products)) {
            productGrid.innerHTML = data.products.map(product => `
                <div class="product-card">
                    <div class="product-image">
                        <img src="${product.image}" alt="${product.name}" loading="lazy">
                    </div>
                    <div class="product-info">
                        <h4>${product.name}</h4>
                        <p class="price">${product.price}</p>
                        <p class="description">${product.description}</p>
                        <button onclick="addToCart(${product.id})">
                            Add to Cart
                        </button>
                    </div>
                </div>
            `).join('');

            // Update pagination
            const prevButton = document.getElementById('prevPage');
            const nextButton = document.getElementById('nextPage');
            const currentPageSpan = document.getElementById('currentPage');

            if (prevButton && nextButton && currentPageSpan) {
                prevButton.disabled = currentPage === 1;
                nextButton.disabled = data.products.length < 12; // Assuming 12 items per page
                currentPageSpan.textContent = `Page ${currentPage}`;
            }
        }
    } catch (error) {
        console.error('Error loading products:', error);
        productGrid.innerHTML = '<div class="error">Failed to load products. Please try again later.</div>';
    }
}

async function addToCart(productId) {
    try {
        const response = await fetch('/api/cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('authToken')}`
            },
            body: JSON.stringify({ productId })
        });

        const data = await response.json();
        
        if (data.success) {
            alert('Product added to cart!');
        } else {
            throw new Error(data.message || 'Failed to add to cart');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        if (error.message === 'Unauthorized') {
            window.location.href = '/login';
        } else {
            alert(error.message || 'Failed to add product to cart');
        }
    }
} 