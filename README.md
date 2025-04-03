# Supagrocery - Online Grocery Shopping System

An online grocery shopping system with multi-user roles (Admin, Customer, and Delivery Agent) built with PHP and MySQL.

## Project Structure

```
supagrocery/
├── assets/
│   ├── images/
│   │   └── products/    # Product images
├── config/              # Configuration files
│   ├── db.php
│   └── db_connect.php
├── includes/            # Common includes
│   ├── admin/          # Admin-specific includes
│   ├── customer/       # Customer-specific includes
│   ├── agent/         # Agent-specific includes
│   └── functions.php   # Common functions
├── public/             # Public assets
│   ├── css/           # Stylesheets
│   └── js/            # JavaScript files
├── src/
│   ├── controllers/   # PHP controllers
│   ├── models/        # Database models
│   └── views/         # View templates
│       ├── admin/     # Admin views
│       ├── customer/  # Customer views
│       └── agent/     # Agent views
├── templates/         # Common templates
├── .htaccess         # Apache configuration
└── index.php         # Application entry point
```

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled
- Composer (for dependency management)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/supagrocery.git
   cd supagrocery
   ```

2. Configure your database:
   - Create a new MySQL database
   - Copy `config/db.example.php` to `config/db.php`
   - Update database credentials in `config/db.php`

3. Set up the web server:
   - Point your web server to the project root
   - Ensure mod_rewrite is enabled
   - Make sure the web server has write permissions for uploads

4. Import the database schema:
   ```bash
   mysql -u your_username -p your_database_name < database/schema.sql
   ```

## Features

- **Admin Panel**
  - Product management
  - User management
  - Order management
  - Delivery agent assignment

- **Customer Features**
  - User registration and authentication
  - Product browsing and search
  - Shopping cart
  - Order tracking
  - Message system

- **Delivery Agent Features**
  - Order status updates
  - Delivery management
  - Communication with customers

## Security

- Input sanitization
- Prepared statements for database queries
- Password hashing
- Session management
- XSS protection
- CSRF protection

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
