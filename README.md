# Billstack Laravel

This repository contains a Laravel‑based invoicing and billing system designed for small businesses such as caterers, general stores and service firms.

## Features

- **Multi‑tenant** business accounts with support for multiple users and roles.
- Customer, product/service and recurring profile management.
- Manual and recurring invoice generation with customizable templates, including a classic detailed format.
 - Manual and recurring invoice generation with customizable templates, including a **Classic Detailed Format** matching the sample invoice layout.
- Payment tracking and outstanding balance reports.
- Simple dashboard and basic financial reports.

## Setup

This project follows a standard Laravel structure. To set it up locally:

1. Ensure you have PHP 8.1+, Composer and a database (MySQL or PostgreSQL) available.
2. Clone the repository and install dependencies:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. Configure your `.env` file with database credentials and other settings.
4. Run migrations and seeders to create the database tables:

   ```bash
   php artisan migrate
   ```

5. Serve the application locally:

   ```bash
   php artisan serve
   ```

## License

This project is released under the MIT License.
