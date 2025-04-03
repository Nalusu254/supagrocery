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
                    <img src="${product.image}" alt="${product.name}">
                    <h4>${product.name}</h4>
                    <p>${product.price}</p>
                    <button onclick="addToCart(${product.id})">Add to Cart</button>
                </div>
            `).join('');
        }
    } catch (error) {
        console.error('Error loading products:', error);
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
        const response = await fetch('/api/cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ productId })
        });
        const data = await response.json();
        if (data.success) {
            alert('Product added to cart!');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        alert('Failed to add product to cart');
    }
} 