document.addEventListener('DOMContentLoaded', () => {
    loadCart();
    setupCheckoutButton();
    setupPaymentForm();
});

async function loadCart() {
    const cartList = document.getElementById('cartList');
    if (!cartList) return;

    try {
        const response = await fetch('/api/cart', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('authToken')}`
            }
        });
        const data = await response.json();

        if (data.items && Array.isArray(data.items)) {
            if (data.items.length === 0) {
                cartList.innerHTML = '<p class="empty-cart">Your cart is empty</p>';
                updateSummary({ subtotal: 0, shipping: 0, tax: 0, total: 0 });
                return;
            }

            cartList.innerHTML = data.items.map(item => `
                <div class="cart-item" data-id="${item.id}">
                    <img src="${item.image}" alt="${item.name}">
                    <div class="item-details">
                        <h3>${item.name}</h3>
                        <p class="price">${item.price}</p>
                        <div class="item-quantity">
                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                            <span>${item.quantity}</span>
                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                        </div>
                    </div>
                    <div class="item-actions">
                        <button class="remove-btn" onclick="removeItem(${item.id})">Remove</button>
                    </div>
                </div>
            `).join('');

            updateSummary(data.summary);
        }
    } catch (error) {
        console.error('Error loading cart:', error);
        cartList.innerHTML = '<p class="error">Failed to load cart. Please try again later.</p>';
    }
}

function updateSummary({ subtotal, shipping, tax, total }) {
    document.getElementById('subtotal').textContent = formatPrice(subtotal);
    document.getElementById('shipping').textContent = formatPrice(shipping);
    document.getElementById('tax').textContent = formatPrice(tax);
    document.getElementById('total').textContent = formatPrice(total);
}

function formatPrice(price) {
    return `$${price.toFixed(2)}`;
}

async function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 1) return;

    try {
        const response = await fetch('/api/cart/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('authToken')}`
            },
            body: JSON.stringify({ itemId, quantity: newQuantity })
        });

        const data = await response.json();
        
        if (data.success) {
            loadCart();
        } else {
            throw new Error(data.message || 'Failed to update quantity');
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
        alert(error.message || 'Failed to update quantity');
    }
}

async function removeItem(itemId) {
    if (!confirm('Are you sure you want to remove this item?')) return;

    try {
        const response = await fetch('/api/cart/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('authToken')}`
            },
            body: JSON.stringify({ itemId })
        });

        const data = await response.json();
        
        if (data.success) {
            loadCart();
        } else {
            throw new Error(data.message || 'Failed to remove item');
        }
    } catch (error) {
        console.error('Error removing item:', error);
        alert(error.message || 'Failed to remove item');
    }
}

function setupCheckoutButton() {
    const checkoutButton = document.getElementById('checkoutButton');
    const checkoutForm = document.getElementById('checkoutForm');
    
    if (checkoutButton && checkoutForm) {
        checkoutButton.addEventListener('click', () => {
            checkoutForm.classList.remove('hidden');
            checkoutButton.disabled = true;
            window.scrollTo({
                top: checkoutForm.offsetTop - 100,
                behavior: 'smooth'
            });
        });
    }
}

function setupPaymentForm() {
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', handleCheckout);
    }
}

async function handleCheckout(event) {
    event.preventDefault();
    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');

    try {
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';

        const formData = {
            shipping: {
                fullName: form.fullName.value,
                address: form.address.value,
                city: form.city.value,
                zipCode: form.zipCode.value
            },
            payment: {
                cardNumber: form.cardNumber.value,
                expiryDate: form.expiryDate.value,
                cvv: form.cvv.value
            }
        };

        const response = await fetch('/api/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('authToken')}`
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
            alert('Order placed successfully!');
            window.location.href = '/order-confirmation';
        } else {
            throw new Error(data.message || 'Checkout failed');
        }
    } catch (error) {
        console.error('Checkout error:', error);
        alert(error.message || 'Failed to process checkout. Please try again.');
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Place Order';
    }
} 