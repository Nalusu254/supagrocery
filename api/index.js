module.exports = (req, res) => {
    // Enable CORS
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    // Handle OPTIONS request for CORS
    if (req.method === 'OPTIONS') {
        res.status(200).end();
        return;
    }

    // Route handling
    const path = req.url.split('?')[0];

    switch (path) {
        case '/api/products':
            return getProducts(req, res);
        case '/api/cart':
            return handleCart(req, res);
        default:
            return res.status(404).json({
                status: 'error',
                message: 'Not found'
            });
    }
};

function getProducts(req, res) {
    // Sample product data
    const products = [
        {
            id: 1,
            name: 'Fresh Apples',
            price: '$2.99',
            image: '/assets/images/products/apples.jpeg'
        },
        {
            id: 2,
            name: 'Bananas',
            price: '$1.99',
            image: '/assets/images/products/banana.jpeg'
        },
        {
            id: 3,
            name: 'Fresh Milk',
            price: '$3.49',
            image: '/assets/images/products/milk.jpeg'
        }
    ];

    res.json({
        status: 'ok',
        products
    });
}

function handleCart(req, res) {
    if (req.method !== 'POST') {
        return res.status(405).json({
            status: 'error',
            message: 'Method not allowed'
        });
    }

    // Here you would normally handle cart operations with a database
    res.json({
        status: 'ok',
        success: true,
        message: 'Product added to cart'
    });
} 