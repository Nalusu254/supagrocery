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
    // Sample product data with placeholder images
    const products = [
        {
            id: 1,
            name: 'Organic Green Apples',
            price: '$3.99/lb',
            image: 'https://images.unsplash.com/photo-1619546813926-a78fa6372cd2?w=500',
            description: 'Fresh and crispy organic green apples'
        },
        {
            id: 2,
            name: 'Yellow Bananas',
            price: '$2.49/bunch',
            image: 'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=500',
            description: 'Sweet and ripe yellow bananas'
        },
        {
            id: 3,
            name: 'Brown Rice',
            price: '$4.99/2lb',
            image: 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=500',
            description: 'Organic whole grain brown rice'
        },
        {
            id: 4,
            name: 'Fresh Avocados',
            price: '$2.99/each',
            image: 'https://images.unsplash.com/photo-1523049673857-eb18f1d7b578?w=500',
            description: 'Ripe and ready-to-eat avocados'
        },
        {
            id: 5,
            name: 'Organic Carrots',
            price: '$1.99/lb',
            image: 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?w=500',
            description: 'Fresh organic carrots'
        },
        {
            id: 6,
            name: 'Brown Eggs',
            price: '$5.99/dozen',
            image: 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=500',
            description: 'Farm-fresh brown eggs'
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