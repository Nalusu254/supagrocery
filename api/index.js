// Mock database
const users = [];
const carts = new Map();
let orders = [];

module.exports = (req, res) => {
    // Enable CORS
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

    // Handle OPTIONS request for CORS
    if (req.method === 'OPTIONS') {
        res.status(200).end();
        return;
    }

    // Route handling
    const path = req.url.split('?')[0];

    try {
        switch (path) {
            case '/api/products':
                return handleProducts(req, res);
            case '/api/cart':
                return handleCart(req, res);
            case '/api/cart/update':
                return handleCartUpdate(req, res);
            case '/api/cart/remove':
                return handleCartRemove(req, res);
            case '/api/auth/login':
                return handleLogin(req, res);
            case '/api/checkout':
                return handleCheckout(req, res);
            default:
                return res.status(404).json({
                    status: 'error',
                    message: 'Not found'
                });
        }
    } catch (error) {
        console.error('API Error:', error);
        return res.status(500).json({
            status: 'error',
            message: 'Internal server error'
        });
    }
};

function handleProducts(req, res) {
    const { page = 1, sort = 'name', categories, minPrice, maxPrice } = parseQueryParams(req.url);
    let products = getProducts();

    // Apply filters
    if (categories) {
        const categoryList = categories.split(',');
        products = products.filter(p => categoryList.includes(p.category));
    }

    if (minPrice) {
        products = products.filter(p => parseFloat(p.price.slice(1)) >= minPrice);
    }

    if (maxPrice) {
        products = products.filter(p => parseFloat(p.price.slice(1)) <= maxPrice);
    }

    // Apply sorting
    products.sort((a, b) => {
        switch (sort) {
            case 'price-low':
                return parseFloat(a.price.slice(1)) - parseFloat(b.price.slice(1));
            case 'price-high':
                return parseFloat(b.price.slice(1)) - parseFloat(a.price.slice(1));
            default:
                return a.name.localeCompare(b.name);
        }
    });

    // Apply pagination
    const itemsPerPage = 12;
    const start = (page - 1) * itemsPerPage;
    const paginatedProducts = products.slice(start, start + itemsPerPage);

    res.json({
        status: 'ok',
        products: paginatedProducts,
        total: products.length,
        page: parseInt(page),
        totalPages: Math.ceil(products.length / itemsPerPage)
    });
}

function handleCart(req, res) {
    const userId = authenticateRequest(req);
    if (!userId) {
        return res.status(401).json({ status: 'error', message: 'Unauthorized' });
    }

    if (req.method === 'POST') {
        const { productId } = JSON.parse(req.body || '{}');
        const userCart = carts.get(userId) || [];
        const product = getProducts().find(p => p.id === productId);

        if (!product) {
            return res.status(404).json({ status: 'error', message: 'Product not found' });
        }

        const existingItem = userCart.find(item => item.id === productId);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            userCart.push({ ...product, quantity: 1 });
        }

        carts.set(userId, userCart);
        return res.json({ status: 'ok', success: true });
    }

    // GET request - return cart contents
    const userCart = carts.get(userId) || [];
    const summary = calculateCartSummary(userCart);

    res.json({
        status: 'ok',
        items: userCart,
        summary
    });
}

function handleCartUpdate(req, res) {
    const userId = authenticateRequest(req);
    if (!userId) {
        return res.status(401).json({ status: 'error', message: 'Unauthorized' });
    }

    const { itemId, quantity } = JSON.parse(req.body || '{}');
    const userCart = carts.get(userId) || [];
    const itemIndex = userCart.findIndex(item => item.id === itemId);

    if (itemIndex === -1) {
        return res.status(404).json({ status: 'error', message: 'Item not found in cart' });
    }

    if (quantity < 1) {
        userCart.splice(itemIndex, 1);
    } else {
        userCart[itemIndex].quantity = quantity;
    }

    carts.set(userId, userCart);
    res.json({ status: 'ok', success: true });
}

function handleCartRemove(req, res) {
    const userId = authenticateRequest(req);
    if (!userId) {
        return res.status(401).json({ status: 'error', message: 'Unauthorized' });
    }

    const { itemId } = JSON.parse(req.body || '{}');
    const userCart = carts.get(userId) || [];
    const itemIndex = userCart.findIndex(item => item.id === itemId);

    if (itemIndex === -1) {
        return res.status(404).json({ status: 'error', message: 'Item not found in cart' });
    }

    userCart.splice(itemIndex, 1);
    carts.set(userId, userCart);
    res.json({ status: 'ok', success: true });
}

function handleLogin(req, res) {
    const { email, password } = JSON.parse(req.body || '{}');
    
    // For demo purposes, auto-create user if not exists
    let user = users.find(u => u.email === email);
    if (!user) {
        user = {
            id: users.length + 1,
            email,
            password // In production, this should be hashed
        };
        users.push(user);
    }

    // In production, verify password hash
    if (user.password !== password) {
        return res.status(401).json({
            status: 'error',
            message: 'Invalid credentials'
        });
    }

    // Generate mock token
    const token = Buffer.from(JSON.stringify({ userId: user.id, email: user.email })).toString('base64');

    res.json({
        status: 'ok',
        success: true,
        token
    });
}

function handleCheckout(req, res) {
    const userId = authenticateRequest(req);
    if (!userId) {
        return res.status(401).json({ status: 'error', message: 'Unauthorized' });
    }

    const userCart = carts.get(userId);
    if (!userCart || userCart.length === 0) {
        return res.status(400).json({ status: 'error', message: 'Cart is empty' });
    }

    const { shipping, payment } = JSON.parse(req.body || '{}');
    
    // Create order
    const order = {
        id: orders.length + 1,
        userId,
        items: userCart,
        shipping,
        payment: {
            last4: payment.cardNumber.slice(-4),
            expiryDate: payment.expiryDate
        },
        summary: calculateCartSummary(userCart),
        status: 'processing',
        createdAt: new Date().toISOString()
    };

    orders.push(order);
    
    // Clear cart
    carts.set(userId, []);

    res.json({
        status: 'ok',
        success: true,
        orderId: order.id
    });
}

// Helper functions
function getProducts() {
    return [
        {
            id: 1,
            name: 'Organic Green Apples',
            price: '$3.99',
            image: 'https://images.unsplash.com/photo-1619546813926-a78fa6372cd2?w=500',
            description: 'Fresh and crispy organic green apples',
            category: 'fruits'
        },
        {
            id: 2,
            name: 'Yellow Bananas',
            price: '$2.49',
            image: 'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=500',
            description: 'Sweet and ripe yellow bananas',
            category: 'fruits'
        },
        {
            id: 3,
            name: 'Brown Rice',
            price: '$4.99',
            image: 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=500',
            description: 'Organic whole grain brown rice',
            category: 'grains'
        },
        {
            id: 4,
            name: 'Fresh Avocados',
            price: '$2.99',
            image: 'https://images.unsplash.com/photo-1523049673857-eb18f1d7b578?w=500',
            description: 'Ripe and ready-to-eat avocados',
            category: 'vegetables'
        },
        {
            id: 5,
            name: 'Organic Carrots',
            price: '$1.99',
            image: 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?w=500',
            description: 'Fresh organic carrots',
            category: 'vegetables'
        },
        {
            id: 6,
            name: 'Brown Eggs',
            price: '$5.99',
            image: 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=500',
            description: 'Farm-fresh brown eggs',
            category: 'dairy'
        }
    ];
}

function parseQueryParams(url) {
    const [, queryString] = url.split('?');
    if (!queryString) return {};

    return queryString.split('&').reduce((params, param) => {
        const [key, value] = param.split('=');
        params[key] = decodeURIComponent(value);
        return params;
    }, {});
}

function calculateCartSummary(cart) {
    const subtotal = cart.reduce((sum, item) => {
        return sum + (parseFloat(item.price.slice(1)) * item.quantity);
    }, 0);

    const shipping = subtotal > 50 ? 0 : 5.99;
    const tax = subtotal * 0.08;
    const total = subtotal + shipping + tax;

    return {
        subtotal,
        shipping,
        tax,
        total
    };
}

function authenticateRequest(req) {
    const authHeader = req.headers.authorization;
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
        return null;
    }

    try {
        const token = authHeader.split(' ')[1];
        const decoded = JSON.parse(Buffer.from(token, 'base64').toString());
        return decoded.userId;
    } catch (error) {
        return null;
    }
} 