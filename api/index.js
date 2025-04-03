// Mock database
const users = [];
const carts = new Map();
const products = new Map();
let orders = [];

// Admin configuration
const ADMIN_CODE = 'ADMIN123'; // In production, this should be stored securely
const AGENT_DEPARTMENTS = ['Sales', 'Support', 'Inventory'];

module.exports = (req, res) => {
    // Enable CORS
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
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
            case '/api/auth/register':
                return handleRegister(req, res);
            case '/api/checkout':
                return handleCheckout(req, res);
            case '/api/agent/dashboard':
                return handleAgentDashboard(req, res);
            case '/api/agent/products':
                return handleAgentProducts(req, res);
            default:
                // Check if it's a product detail request
                if (path.match(/^\/api\/agent\/products\/[\w-]+$/)) {
                    return handleAgentProductDetail(req, res);
                }
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
    
    const user = users.find(u => u.email === email);
    if (!user || user.password !== password) { // In production, verify password hash
        return res.status(401).json({
            status: 'error',
            message: 'Invalid credentials'
        });
    }

    // Generate token with role
    const token = Buffer.from(JSON.stringify({
        userId: user.id,
        email: user.email,
        role: user.role
    })).toString('base64');

    res.json({
        status: 'ok',
        success: true,
        token,
        role: user.role
    });
}

function handleRegister(req, res) {
    const { fullName, email, password, role, adminCode, agentId, department } = JSON.parse(req.body || '{}');

    // Basic validation
    if (!fullName || !email || !password || !role) {
        return res.status(400).json({
            status: 'error',
            message: 'Missing required fields'
        });
    }

    // Check if email already exists
    if (users.some(u => u.email === email)) {
        return res.status(400).json({
            status: 'error',
            message: 'Email already registered'
        });
    }

    // Role-specific validation
    if (role === 'admin') {
        if (adminCode !== ADMIN_CODE) {
            return res.status(403).json({
                status: 'error',
                message: 'Invalid admin code'
            });
        }
    } else if (role === 'agent') {
        if (!agentId || !department) {
            return res.status(400).json({
                status: 'error',
                message: 'Agent ID and department are required'
            });
        }
        if (!AGENT_DEPARTMENTS.includes(department)) {
            return res.status(400).json({
                status: 'error',
                message: 'Invalid department'
            });
        }
    }

    // Create new user
    const user = {
        id: users.length + 1,
        fullName,
        email,
        password, // In production, this should be hashed
        role,
        createdAt: new Date().toISOString()
    };

    // Add role-specific data
    if (role === 'agent') {
        user.agentId = agentId;
        user.department = department;
    }

    users.push(user);

    // Generate token
    const token = Buffer.from(JSON.stringify({
        userId: user.id,
        email: user.email,
        role: user.role
    })).toString('base64');

    res.json({
        status: 'ok',
        success: true,
        token,
        role: user.role
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

async function handleAgentDashboard(req, res) {
    const userId = authenticateRequest(req);
    if (!userId) {
        return res.status(401).json({
            status: 'error',
            message: 'Unauthorized'
        });
    }

    const user = users.find(u => u.id === userId);
    if (!user || user.role !== 'agent') {
        return res.status(403).json({
            status: 'error',
            message: 'Access denied'
        });
    }

    // Get agent's products
    const agentProducts = Array.from(products.values()).filter(p => p.agentId === userId);
    
    // Calculate statistics
    const stats = {
        totalProducts: agentProducts.length,
        totalSales: agentProducts.reduce((sum, product) => {
            const productOrders = orders.filter(o => o.items.some(i => i.productId === product.id));
            return sum + productOrders.reduce((orderSum, order) => {
                const item = order.items.find(i => i.productId === product.id);
                return orderSum + (item.price * item.quantity);
            }, 0);
        }, 0),
        activeOrders: orders.filter(o => 
            o.status === 'pending' && 
            o.items.some(i => {
                const product = products.get(i.productId);
                return product && product.agentId === userId;
            })
        ).length
    };

    res.json({
        status: 'ok',
        success: true,
        stats
    });
}

async function handleAgentProducts(req, res) {
    const userId = authenticateRequest(req);
    if (!userId) {
        return res.status(401).json({
            status: 'error',
            message: 'Unauthorized'
        });
    }

    const user = users.find(u => u.id === userId);
    if (!user || user.role !== 'agent') {
        return res.status(403).json({
            status: 'error',
            message: 'Access denied'
        });
    }

    if (req.method === 'GET') {
        // Get agent's products with filters
        const { search, status } = parseQueryParams(req.url);
        let agentProducts = Array.from(products.values())
            .filter(p => p.agentId === userId);

        if (search) {
            agentProducts = agentProducts.filter(p => 
                p.name.toLowerCase().includes(search.toLowerCase()) ||
                p.description.toLowerCase().includes(search.toLowerCase())
            );
        }

        if (status && status !== 'all') {
            agentProducts = agentProducts.filter(p => p.status.toLowerCase() === status);
        }

        res.json({
            status: 'ok',
            success: true,
            products: agentProducts
        });
    } else if (req.method === 'POST') {
        // Add new product
        const formData = await parseFormData(req);
        const productData = {
            id: generateId(),
            agentId: userId,
            name: formData.name,
            description: formData.description,
            price: parseFloat(formData.price),
            stock: parseInt(formData.stock),
            category: formData.category,
            image: formData.image || '/assets/images/products/default.jpg',
            status: 'active',
            createdAt: new Date().toISOString()
        };

        products.set(productData.id, productData);

        res.json({
            status: 'ok',
            success: true,
            product: productData
        });
    }
}

async function handleAgentProductDetail(req, res) {
    const userId = authenticateRequest(req);
    if (!userId) {
        return res.status(401).json({
            status: 'error',
            message: 'Unauthorized'
        });
    }

    const user = users.find(u => u.id === userId);
    if (!user || user.role !== 'agent') {
        return res.status(403).json({
            status: 'error',
            message: 'Access denied'
        });
    }

    const productId = req.url.split('/').pop();
    const product = products.get(productId);

    if (!product || product.agentId !== userId) {
        return res.status(404).json({
            status: 'error',
            message: 'Product not found'
        });
    }

    if (req.method === 'GET') {
        res.json({
            status: 'ok',
            success: true,
            product
        });
    } else if (req.method === 'PUT') {
        const formData = await parseFormData(req);
        const updatedProduct = {
            ...product,
            name: formData.name,
            description: formData.description,
            price: parseFloat(formData.price),
            stock: parseInt(formData.stock),
            category: formData.category,
            image: formData.image || product.image,
            updatedAt: new Date().toISOString()
        };

        products.set(productId, updatedProduct);

        res.json({
            status: 'ok',
            success: true,
            product: updatedProduct
        });
    } else if (req.method === 'DELETE') {
        products.delete(productId);
        res.json({
            status: 'ok',
            success: true,
            message: 'Product deleted successfully'
        });
    }
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

    return Object.fromEntries(
        queryString.split('&').map(param => {
            const [key, value] = param.split('=');
            return [key, decodeURIComponent(value)];
        })
    );
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

    const token = authHeader.split(' ')[1];
    try {
        const decoded = JSON.parse(Buffer.from(token, 'base64').toString());
        return decoded.userId;
    } catch {
        return null;
    }
}

async function parseFormData(req) {
    // In a real implementation, you would use a library like formidable
    // For this example, we'll assume JSON data
    const chunks = [];
    for await (const chunk of req) {
        chunks.push(chunk);
    }
    return JSON.parse(Buffer.concat(chunks).toString());
}

function generateId() {
    return Math.random().toString(36).substr(2, 9);
} 