# SupaGrocery - Modern E-commerce Platform

A modern, responsive e-commerce platform built with vanilla JavaScript and Node.js, designed for easy deployment on Vercel.

## Features

### Customer Features
- **Product Browsing**
  - Browse products with filters and search
  - Sort by price, name, and category
  - Pagination support
  - Detailed product views

- **Shopping Cart**
  - Add/remove products
  - Update quantities
  - Real-time price calculations
  - Persistent cart storage

- **Checkout Process**
  - Secure payment processing
  - Shipping information collection
  - Order summary
  - Order confirmation

### Agent Features
- **Product Management Dashboard**
  - Add new products
  - Edit existing products
  - Delete products
  - Upload product images
  - Track inventory

- **Business Analytics**
  - Total products overview
  - Sales statistics
  - Active orders tracking

## Tech Stack

- **Frontend:**
  - Vanilla JavaScript (ES6+)
  - HTML5
  - CSS3 with Grid and Flexbox
  - Responsive design

- **Backend:**
  - Node.js
  - In-memory data storage (mock database)
  - RESTful API architecture

- **Deployment:**
  - Vercel platform
  - Static file hosting
  - Serverless functions

## Project Structure

```
supagrocery/
├── api/             # Node.js API endpoints
├── public/          # Static files
│   ├── agent/      # Agent dashboard
│   ├── assets/     # Images and other assets
│   ├── css/        # Stylesheets
│   ├── js/         # JavaScript files
│   ├── index.html  # Landing page
│   ├── login.html  # Authentication
│   ├── products.html # Product listing
│   └── cart.html   # Shopping cart
├── vercel.json     # Vercel configuration
└── package.json    # Project dependencies
```

## API Endpoints

### Public Endpoints
- `GET /api/products` - List products with filtering and pagination
- `POST /api/auth/login` - User authentication
- `GET /api/cart` - Get user's cart
- `POST /api/cart` - Add item to cart
- `PUT /api/cart/update` - Update cart item quantity
- `DELETE /api/cart/remove` - Remove item from cart
- `POST /api/checkout` - Process checkout

### Agent Endpoints
- `GET /api/agent/dashboard` - Get dashboard statistics
- `GET /api/agent/products` - List agent's products
- `POST /api/agent/products` - Add new product
- `PUT /api/agent/products/:id` - Update product
- `DELETE /api/agent/products/:id` - Delete product

## Setup and Development

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/supagrocery.git
   cd supagrocery
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Run development server:**
   ```bash
   vercel dev
   ```

4. **Build for production:**
   ```bash
   vercel build
   ```

5. **Deploy:**
   ```bash
   vercel deploy
   ```

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
