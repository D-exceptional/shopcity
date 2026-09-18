# Shopcity

A full-stack multivendor e-commerce platform built with PHP, MySQL, JavaScript, and a custom PHP framework.

Shopcity was built to explore the architecture and engineering challenges involved in running a real-world marketplace where administrators, vendors, and customers interact through a single platform.

## Overview

Shopcity supports multiple vendors operating their own stores while customers can discover products, manage their carts and wishlists, place orders, manage wallets, and receive notifications.

The project has evolved through real-world development and has been used to explore practical concerns such as authentication, authorization, caching, background processing, third-party integrations, and containerized deployment.

## Live Demo
https://builds.iceiy.com/shopcity

## Screenshots
![Homepage](/public/assets/screenshots/home.png)
![Product Listing](/public/assets/screenshots/product-listing.png)
![Product Details](/public/assets/screenshots/details.png)
![Register](/public/assets/screenshots/register.png)
![Login](/public/assets/screenshots/login.png)

## Core Features

### Customer

* Registration and authentication
* Product discovery and search
* Advanced product filtering
* Shopping cart
* Wishlist
* Order management and tracking
* Product reviews
* Customer wallet
* Wallet top-up
* Notifications
* Responsive interface

### Vendor

* Vendor registration and verification
* Store management
* Product management
* Product status management
* Order management
* Vendor wallet
* Earnings and payout management
* Notifications

### Administration

* Administrative dashboard
* User and vendor management
* Vendor verification
* Product and store management
* Order management
* Vendor payout management
* Wallet management
* Content management
* System notifications

## Architecture

Shopcity is built on top of a custom PHP framework developed separately as an exploration of modern web application architecture.

The application follows a layered structure that separates responsibilities across:

```text
HTTP Request
     │
     ▼
   Router
     │
     ▼
 Middleware
     │
     ▼
 Controllers
     │
     ├── Validation
     ├── Services
     ├── Models
     └── Application Logic
     │
     ▼
 Database / External Services
     │
     ▼
 HTTP Response
```

Background work is handled separately through queue workers where appropriate, allowing resource-intensive tasks to be processed outside the main request lifecycle.

## Infrastructure

The application supports containerized development and deployment using Docker.

The repository includes:

* Application container
* Nginx
* MySQL
* Redis
* Background workers
* phpMyAdmin for development
* Separate development and production Compose configurations

This allows the application environment to remain consistent across development and deployment.

## Technologies

### Backend

* PHP
* Custom PHP framework
* MySQL / SQL
* Composer

### Frontend

* HTML
* CSS
* Bootstrap
* JavaScript
* SweetAlert2
* Font Awesome

### Infrastructure

* Docker
* Nginx
* Redis
* MySQL

### Integrations

* Flutterwave — payments
* Cloudinary — media storage
* Firebase — push notifications
* PHPMailer — email delivery

## Engineering Highlights

The project focuses on more than simply implementing e-commerce features.

Particular attention is given to:

* Role-based access control
* Authentication and authorization
* Database relationships
* Query optimization
* Redis caching
* Background processing
* External service integration
* Containerized application environments
* Separation of application responsibilities
* Maintainable backend architecture

## Local Development

Clone the repository:

```bash
git clone https://github.com/D-exceptional/shopcity.git
cd shopcity
```

Install dependencies:

```bash
composer install
```

Copy the environment configuration:

```bash
cp .env.example .env
```

Configure the required database, Redis, payment, storage, mail, and Firebase credentials in `.env`.

### Docker

The repository includes Docker Compose configurations for development and production environments.

For development:

```bash
docker compose up -d
```

Check the Compose configuration and environment variables before starting the application.

## Security

Sensitive credentials and environment-specific configuration should never be committed to the repository.

Use `.env` for local configuration and provide only safe example values through `.env.example`.

## Project Status

Shopcity is an evolving personal engineering project.

The application continues to be refined as the underlying framework, infrastructure, and application architecture improve.

## Why I Built This

I built Shopcity to go beyond simple CRUD-based e-commerce development and explore the engineering challenges involved in building a complete multivendor platform.

The project provided a practical environment for applying backend architecture, database design, authentication, caching, queues, third-party integrations, and containerization to a single real-world application.

It also serves as a practical demonstration of how the custom PHP framework can be used to build a substantial application.
