# Multi-Warehouse & Multi-Country Inventory Management System API

This is the backend API for a Multi-Warehouse, Multi-Country Inventory Management System built with Laravel 12.

The system supports managing products, suppliers, and inventory across multiple warehouses located in different countries. It features JWT authentication, role-based access control, and a robust API for all inventory management operations.

## Features

-   **Country Management**: CRUD operations for countries.
-   **Warehouse Management**: CRUD operations for warehouses, linked to countries.
-   **Product Management**: CRUD operations for products.
-   **Supplier Management**: CRUD operations for suppliers.
-   **Inventory Transactions**: Record IN/OUT stock movements per warehouse.
-   **Inventory Transfer**: Transfer products between warehouses, including across countries.
-   **Global Inventory View**: View total stock for each product across all warehouses.
-   **Automated Low-Stock Reporting**: A daily scheduled job identifies products below their minimum quantity and sends an email report.

## Requirements

-   PHP 8.2 or higher
-   Composer
-   A database supported by Laravel (MySQL, PostgreSQL, etc.)

## Getting Started

### 1. Clone the repository

```bash
git clone https://github.com/your-username/your-repository-name.git
cd your-repository-name
```

### 2. Install dependencies

Install the PHP dependencies using Composer.

```bash
composer install
```

### 3. Set up the environment

Copy the example environment file and generate your application key.

```bash
cp .env.example .env
php artisan key:generate
```

Next, generate a JWT secret key. This command will add `JWT_SECRET` to your `.env` file.

```bash
php artisan jwt:secret
```

### 4. Configure your `.env` file

Open the `.env` file and configure your database connection details (`DB_*` variables).

You also need to set a recipient email address for the low-stock report:

```
LOW_STOCK_REPORT_EMAIL=your-email@example.com
```

### 5. Run database migrations

Run the database migrations to create the necessary tables.

```bash
php artisan migrate
```

### 6. Run the application

To start the development server, run:

```bash
php artisan serve
```

The API will be available at `http://127.0.0.1:8000`.

## API Documentation

This project uses Swagger for API documentation. Once the application is running, you can view the documentation by navigating to:

[http://127.0.0.1:8000/api/documentation](http://127.0.0.1:8000/api/documentation)

## API Endpoints

All endpoints are prefixed with `/api`. See the Swagger documentation for details on request bodies and responses.

-   `POST /register`
-   `POST /login`
-   `DELETE /logout` (Authenticated)
-   `GET|POST|PUT|DELETE /countries` (Authenticated)
-   `GET|POST|PUT|DELETE /warehouses` (Authenticated)
-   `GET|POST|PUT|DELETE /products` (Authenticated)
-   `GET|POST|PUT|DELETE /suppliers` (Authenticated)
-   `GET|POST /inventory-transactions` (Authenticated)
-   `POST /inventory-transfer` (Authenticated)
-   `GET /inventory/global-view` (Authenticated)
-   `GET /reports/low-stock` (Authenticated)

## Generating / Updating Swagger Documentation

This project ships with [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger). Any time you change controllers, requests, resources, or the consolidated docs found in `app/Swagger/ApiDocumentation.php`, regenerate the JSON that powers Swagger-UI.

```bash
# Generate the latest swagger.json & refresh UI assets
php artisan l5-swagger:generate
```

You can also simply hit the documentation route in your browser (`/api/documentation`), and L5-Swagger will compile the docs on-the-fly (if `L5_SWAGGER_GENERATE_ALWAYS=true` is set in `.env`).

Common troubleshooting tips:

1. **Missing endpoints** – clear cached JSON (`storage/api-docs`) and regenerate.
2. **Annotation errors** – run the generator with `-vvv` to get verbose output.
3. **Auth blocked** – Swagger-UI needs a **Bearer** token for protected routes. Click the green `Authorize` button and paste a valid JWT to make authenticated calls directly from the UI.

## Running the Low-Stock Check Manually

You can trigger the low-stock check manually by running the following Artisan command:

```bash
php artisan inventory:check-low-stock
```

## Running Tests

To run the feature and unit tests, execute:

```bash
php artisan test
```

A sample test suite for the Countries API is available at `tests/Feature/CountryControllerTest.php`.
