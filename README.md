# Multi-Warehouse & Multi-Country Inventory Management System

A comprehensive backend API system built with Laravel for managing products across multiple warehouses in different countries. The system provides robust inventory tracking, automated stock monitoring, and seamless inter-warehouse transfers.

## Features

- **Multi-Country & Multi-Warehouse Management**: Support for managing inventory across different countries and warehouses
- **Inventory Tracking**: Track stock levels with automatic updates on transactions
- **Inter-Warehouse Transfers**: Seamlessly transfer products between warehouses across countries
- **Automated Low Stock Alerts**: Daily scheduled reports for products reached minimum quantity
- **Global Inventory View**: Comprehensive overview of stock levels across all warehouses
- **JWT Authentication**: Secure API access with token-based authentication
- **Email & Slack Notifications**: Automated alerts for low stock situations
- **Comprehensive API Documentation**: Auto-generated Swagger documentation
- **Caching System**: Optimized performance for frequently accessed products by using Redis
- **Testing Suite**: Unit tests for major functionalities to ensure reliability

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Authentication](#authentication)
- [API Endpoints](#api-endpoints)
- [Scheduled Jobs](#scheduled-jobs)
- [Testing](#testing)
- [Usage Examples](#usage-examples)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [License](#license)

## Requirements

- PHP >= 8.4
- Composer >= 2.0
- Laravel >= 11.0
- MySQL >= 8.0
- Redis (for caching)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/RezdarNajeeb/Multi-Warehouse-and-Multi-Country-Inventory-Management-System-API.git
cd Multi-Warehouse-and-Multi-Country-Inventory-Management-System-API
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install
```

### 3. Set Up Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret key
php artisan jwt:secret
```

## Configuration

### Environment Variables

Update your `.env` file with the following configurations:

```env
# Application
APP_NAME="Multi-Warehouse & Multi-Country Inventory System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_TEST_DATABASE=your_test_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Low Stock Report Email
LOW_STOCK_REPORT_EMAIL=admin@yourcompany.com

# Slack Webhook
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK

# Cache
CACHE_STORE=redis
CACHE_PREFIX=your_cache_prefix
QUEUE_CONNECTION=redis

PRODUCT_CACHE_TTL=500 # Cache time in minutes

# JWT Configuration
JWT_SECRET=your_jwt_secret
```

### Database Configuration

Create a new database for the project:

```sql
CREATE DATABASE your_database_name;
```

## Database Setup

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Seed the Database (Optional)

```bash
php artisan db:seed
```

This will create sample data including:
- Countries (US, UK, Canada, Germany)
- Warehouses in different countries
- Sample products and suppliers
- Initial inventory data

## 🖥 Development Server
Run the following command to start the Laravel development server:

```bash
php artisan serve
```
This will start the server at `http://localhost:8000`.

## Authentication

The system uses JWT (JSON Web Tokens) for authentication.

### Register a User

```bash
POST /api/register
Accept: application/json
Content-Type: application/json

{
    "name": "Name Example",
    "email": "name@example.com",
    "password": "password123",
}
    
```
Response:
```json
{
    "message": "User registered successfully."
}
```

### Login to Get Token

```bash
POST /api/auth/login
Accept: application/json
Content-Type: application/json

{
    "email": "name@example.com",
    "password": "password123"
}
```

Response:
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600
}
```

## API Endpoints & Documentation
The API endpoints are documented using Swagger. You can access the documentation at:

```
http://localhost:8000/api/documentation
```

## Scheduled Jobs

### Low Stock Report Job

The system automatically runs a daily job at 12:00 AM to check for low stock items.

```bash
# Run manually
php artisan inventory:check-low-stock
```
This command scheduled in `bootstrap/app.php` dispatch a job to check for products that have reached their minimum quantity and send notifications via email and Slack.

### Queue Workers

Start queue workers for background jobs:

```bash
php artisan queue:work
```

## Testing

### Test Environment

Ensure you have a separate test database configured in your `.env` file:

```env
DB_TEST_DATABASE=your_test_database_name
```

### How to Run Tests

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test class
php artisan test --filter=ProductTest
```

## Troubleshooting

### Common Issues

1. **JWT Token Issues:**
   ```bash
   php artisan jwt:secret
   php artisan config:clear
   ```

2. **Migration Issues:**
   ```bash
   php artisan migrate:fresh --seed
   ```

3. **Cache Issues:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   ```
   
4. **Redis Connection Issues:**
   Ensure Redis is running and configured correctly in your `.env` file.

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [JWT Auth Documentation](https://jwt-auth.readthedocs.io/)
- [Swagger Documentation](https://swagger.io/)
- [Redis Documentation](https://redis.io/documentation)
- [Pest Documentation](https://pestphp.com/docs/)

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support and questions:
- Email: rezdar.00166214@gmail.com
- Issues: [GitHub Issues](https://github.com/RezdarNajeeb/Multi-Warehouse-and-Multi-Country-Inventory-Management-System-API/issues)

---

**Built with ❤️ using Laravel Framework**
