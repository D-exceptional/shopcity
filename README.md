# e-Commerce Platform Demo
A fully-featured e-commerce platform built solo from scratch using PHP, Bootstrap, JavaScript, and MySQL.  
This is a clean demonstration version showcasing core functionality similar to real-world client project I delivered.

## Live Demo
https://builds.iceiy.com/shopcity

## Screenshots
![Homepage](/assets/screenshots/home.png)
![Product Listing](/assets/screenshots/product-listing.png)
![Product Details](/assets/screenshots/details.png)
![Register](/assets/screenshots/register.png)
![Login](/assets/screenshots/login.png)

## Key Features
- Responsive design with Bootstrap
- User registration and login (separate roles for admin, vendors (sellers) and customers)
- Seller verification
- Store management
- Product management
- Product listing, advanced search, and filtering
- Product status tracking
- Cart management
- Wishlist management
- In-app wallet for vendors and customers
- Wallet topup for customers
- Vendor payout via admin dashboard
- Micro blogging
- Admin panel for managing content
- Secure authentication and database relationships

## Technologies Used
- Frontend: HTML, CSS, Bootstrap, Font Awesome, JavaScript
- Backend: PHP (custom framework)
- Database: MySQL / SQL
- Mailing: PHPMailer (SMTP)
- File Storage: Cloudinary
- Push Notification: Firebase
- Payment: Flutterwave
- Caching: Redis
- Pop-ups: SweetAlert2

## Challenges Overcome
- Implemented efficient SQL queries for fast search results across large datasets
- Built role-based access control for different user types
- Ensured full responsiveness on mobile devices

## Local Setup Instructions
1. Clone the repo: git clone https://github.com/d-exceptional/shopcity.git
2. Create /.env and copy /.env.example to /.env
3. Create the databse on PHPMyAdmin or its equivalent and import the SQL file in /database/(schema.sql) to setup the database locally and configure DB in /.env
4. Sign up on Cloudinary and obtain your cloudinary account name and update in /assets/js/core/config.js on line 153
    Then, update the following values in the /.env (CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, CLOUDINARY_API_SECRET) with details from your cloudinary account.
5. Sign up on Flutterwave, obtain your public key and update in /assets/js/core/config.js on line 143 AND Update FLW_SECRET_KEY in /.env with your actual account secret key.
6. Sign up on Firebase, obtain your config and update in /assets/js/core/config.js on line 217. 
    Update the config also in /service-worker.js on line 21
    Then, obtain your firebase json secret key token (json) and update in /storage/(firebase-key.json)
    Finally, update the following values in the /.env (FIREBASE_PROJECT_ID, FIREBASE_PRIVATE_KEY_ID, FIREBASE_PRIVATE_KEY) from the corresponding values in the /storage/firebase-key.json file.
7. Update the file path for the error log file in /php.ini and /.user.ini
8. Run composer install (if PHP/Laravel)
9. Run the project (e.g., php artisan serve if Laravel, or your usual PHP server)
10. Navigate to /admin AND login with these credentials: Email = admin1@gmail.com, Password: admin1

## Why I Built This
To demonstrate end-to-end full-stack development for real-world use case of multi-vendor e-commerce platform.