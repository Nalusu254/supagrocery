document.addEventListener('DOMContentLoaded', () => {
    // Load featured products
    loadFeaturedProducts();

    // Update active navigation
    updateActiveNav();
});

async function loadFeaturedProducts() {
    try {
        const response = await fetch('/api/products');
        const data = await response.json();
        
        const productGrid = document.querySelector('.product-grid');
        if (!productGrid) return;

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
        }
    } catch (error) {
        console.error('Error loading products:', error);
        const productGrid = document.querySelector('.product-grid');
        if (productGrid) {
            productGrid.innerHTML = '<p class="error">Failed to load products. Please try again later.</p>';
        }
    }
}

function updateActiveNav() {
    const currentPath = window.location.pathname;
    document.querySelectorAll('nav a').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
}

async function addToCart(productId) {
    try {
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Adding...';
        button.disabled = true;

        const response = await fetch('/api/cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ productId })
        });
        const data = await response.json();
        
        if (data.success) {
            button.textContent = '✓ Added';
            setTimeout(() => {
                button.textContent = originalText;
                button.disabled = false;
            }, 2000);
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        alert('Failed to add product to cart');
        button.textContent = originalText;
        button.disabled = false;
    }
} 