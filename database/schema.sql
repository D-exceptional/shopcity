--
-- Table structure for table payment_method
--
CREATE TABLE IF NOT EXISTS payment_method (
  method_id INT NOT NULL AUTO_INCREMENT,
  method_type ENUM('Coin', 'Gateway', 'Manual') DEFAULT 'Coin' NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (method_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table payment_method
--
INSERT INTO payment_method (method_type, created_at, updated_at) VALUES ('Coin', '2025-09-22 16:47:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table  users
--
CREATE TABLE IF NOT EXISTS users (
  user_id INT NOT NULL AUTO_INCREMENT,
  avatar VARCHAR(1000) NOT NULL,
  firstname VARCHAR(1000) NOT NULL,
  lastname VARCHAR(1000) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  contact VARCHAR(100) NOT NULL,
  country VARCHAR(1000) NOT NULL,
  user_state VARCHAR(1000) NOT NULL,
  user_password VARCHAR(255) NOT NULL,
  user_role ENUM('Admin', 'Affiliate', 'Customer', 'Vendor', 'Worker') DEFAULT 'Customer' NOT NULL,
  user_status ENUM('Active', 'Pending', 'Deactivated') DEFAULT 'Pending' NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table users
--
INSERT INTO users (user_id, avatar, firstname, lastname, email, contact, country, user_state, user_password, user_role, user_status, created_at, updated_at) VALUES
(1, 'None', 'Admin', 'Name', 'admin1@gmail.com', '+23491111111', 'Nigeria', 'N/A', '$2y$10$XLHKnVjgofU8tcc//kahJuQKRTkBDPun5W/lgoJBPqIkGbk890j0i', 'Admin', 'Active', '2023-05-15 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table user_documents
--
CREATE TABLE IF NOT EXISTS user_documents (
  document_id INT NOT NULL AUTO_INCREMENT,
  identity_file VARCHAR(2000) NOT NULL,
  user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (document_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table user_socials
--
CREATE TABLE IF NOT EXISTS user_socials (
  social_id INT NOT NULL AUTO_INCREMENT,
  facebook VARCHAR(2000) NOT NULL,
  instagram VARCHAR(2000) NOT NULL,
  tiktok VARCHAR(2000) NOT NULL,
  twitter VARCHAR(2000) NOT NULL,
  user_id INT NOT NULL,
  PRIMARY KEY (social_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table bank_details
--
CREATE TABLE IF NOT EXISTS bank_details (
  detail_id INT NOT NULL AUTO_INCREMENT,
  account_number VARCHAR(10) NOT NULL,
  bank_name VARCHAR(2000) NOT NULL,
  bank_code VARCHAR(100) NOT NULL,
  currency_code VARCHAR(100) NOT NULL,
  user_id INT NOT NULL,
  PRIMARY KEY (detail_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table billing_details
--
CREATE TABLE IF NOT EXISTS billing_details (
  detail_id INT NOT NULL AUTO_INCREMENT,
  delivery_address VARCHAR(2000) NOT NULL,
  city VARCHAR(2000) NOT NULL,
  postcode VARCHAR(2000) NOT NULL,
  user_id INT NOT NULL,
  PRIMARY KEY (detail_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table wallet_coin
--

CREATE TABLE IF NOT EXISTS wallet_coin (
  wallet_id INT NOT NULL AUTO_INCREMENT,
  wallet_amount DECIMAL(11,2) NOT NULL,
  user_id INT NOT NULL,
  PRIMARY KEY (wallet_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table wallet_payout
--

CREATE TABLE IF NOT EXISTS wallet_payout (
  wallet_id INT NOT NULL AUTO_INCREMENT,
  wallet_amount DECIMAL(11,2) NOT NULL,
  user_id INT NOT NULL,
  PRIMARY KEY (wallet_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table wallet_payout_backup
--

CREATE TABLE IF NOT EXISTS wallet_payout_backup (
  wallet_id INT NOT NULL AUTO_INCREMENT,
  wallet_amount DECIMAL(11,2) NOT NULL,
  user_id INT NOT NULL,
  PRIMARY KEY (wallet_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table wallet_savings
--

CREATE TABLE IF NOT EXISTS wallet_savings (
  wallet_id INT NOT NULL AUTO_INCREMENT,
  wallet_amount DECIMAL(11,2) NOT NULL,
  user_id INT NOT NULL,
  PRIMARY KEY (wallet_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table withdrawals
--

CREATE TABLE IF NOT EXISTS withdrawals (
  withdrawal_id INT NOT NULL AUTO_INCREMENT,
  amount DECIMAL(15,2) NOT NULL,
  bank VARCHAR(1000) NOT NULL,
  account VARCHAR(1000) NOT NULL,
  reference VARCHAR(255) NOT NULL,
  narration VARCHAR(1000) NOT NULL,
  withdrawal_status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending' NOT NULL,
  user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (withdrawal_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table stores
--
CREATE TABLE IF NOT EXISTS stores (
  store_id INT NOT NULL AUTO_INCREMENT,
  store_name VARCHAR(500) NOT NULL UNIQUE,
  store_avatar VARCHAR(2000) NOT NULL,
  store_description VARCHAR(2000) NOT NULL,
  store_status ENUM('Pending', 'Active', 'Deactivated') DEFAULT 'Pending' NOT NULL,
  store_delivery VARCHAR(2000) NOT NULL,
  user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (store_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table store_socials
--
CREATE TABLE IF NOT EXISTS store_socials (
  social_id INT NOT NULL AUTO_INCREMENT,
  facebook VARCHAR(2000) NOT NULL,
  instagram VARCHAR(2000) NOT NULL,
  tiktok VARCHAR(2000) NOT NULL,
  twitter VARCHAR(2000) NOT NULL,
  store_id INT NOT NULL,
  PRIMARY KEY (social_id),
  FOREIGN KEY (store_id) REFERENCES stores (store_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table store_coupons
--
CREATE TABLE IF NOT EXISTS store_coupons (
  coupon_id INT NOT NULL AUTO_INCREMENT,
  coupon_code VARCHAR(100) NOT NULL,
  coupon_discount INT NOT NULL,
  coupon_status ENUM('Active', 'Deactivated') DEFAULT 'Active' NOT NULL,
  store_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (coupon_id),
  FOREIGN KEY (store_id) REFERENCES stores (store_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table product_categories
--
CREATE TABLE IF NOT EXISTS product_categories (
  category_id INT NOT NULL AUTO_INCREMENT,
  category_name VARCHAR(1000) NOT NULL,
  PRIMARY KEY (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table product_categories
--
INSERT INTO product_categories (category_name) VALUES
('Appliances'),
('Phones & Tablets'),
('Health & Beauty'),
('Home & Office'),
('Electronics'),
('Fashion'),
('Health & Fitness'),
('Love & Relationship'),
('Lifestyle'),
('Marketing & Sales'),
('Personal & Professional Development'),
('Science'),
('Software & Tech'),
('Travels'),
('Wealth & Finance');

-- --------------------------------------------------------

--
-- Table structure for table products
--
CREATE TABLE IF NOT EXISTS products (
  product_id INT NOT NULL AUTO_INCREMENT,
  product_name VARCHAR(1000) NOT NULL,
  product_description TEXT NOT NULL,
  category VARCHAR(1000) NOT NULL,
  sub_category VARCHAR(1000) NOT NULL,
  product_price DECIMAL(10, 2) NOT NULL,
  slash_price DECIMAL(10, 2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  color VARCHAR(1000) NOT NULL,
  store_id INT NOT NULL,
  reselling ENUM('Enabled', 'Disabled') DEFAULT 'Disabled' NOT NULL,
  commission INT DEFAULT 0 NOT NULL,
  visibility ENUM('Visible', 'Hidden') DEFAULT 'Visible' NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (product_id),
  FOREIGN KEY (store_id) REFERENCES stores (store_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table products
--
CREATE TABLE IF NOT EXISTS product_media (
  media_id INT NOT NULL AUTO_INCREMENT,
  media_url VARCHAR(1000) NOT NULL,
  media_type VARCHAR(100) NOT NULL,
  product_id INT NOT NULL,
  PRIMARY KEY (media_id),
  FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table product_links
--

CREATE TABLE IF NOT EXISTS product_links (
  link_id INT NOT NULL AUTO_INCREMENT,
  product_id INT NOT NULL,
  user_id INT NOT NULL,
  short_link VARCHAR(100) NOT NULL,
  long_link VARCHAR(1000) NOT NULL,
  short_code VARCHAR(100) NOT NULL,
  link_status ENUM('Active', 'Deactivated') DEFAULT 'Active' NOT NULL,
  PRIMARY KEY (link_id),
  FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table cart
--
CREATE TABLE IF NOT EXISTS cart (
  cart_id INT NOT NULL AUTO_INCREMENT,
  product_id INT NOT NULL,
  quantity INT DEFAULT 1,
  user_id INT NOT NULL,
  PRIMARY KEY (cart_id),
  FOREIGN KEY (product_id) REFERENCES products (product_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table wishlist
--
CREATE TABLE IF NOT EXISTS wishlist (
  wishlist_id INT NOT NULL AUTO_INCREMENT,
  product_id INT NOT NULL,
  user_id INT NOT NULL,
  PRIMARY KEY (wishlist_id),
  FOREIGN KEY (product_id) REFERENCES products (product_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table orders
--
CREATE TABLE IF NOT EXISTS orders (
  order_id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  subtotal_amount DECIMAL(10, 2) DEFAULT 0 NOT NULL,
  tax_amount DECIMAL(10, 2) DEFAULT 0 NOT NULL,
  discount_amount DECIMAL(10, 2) DEFAULT 0 NOT NULL,
  shipping_amount DECIMAL(10, 2) DEFAULT 0 NOT NULL,
  total_amount DECIMAL(10, 2) DEFAULT 0 NOT NULL,
  order_status ENUM('Pending', 'Cancelled', 'Completed') DEFAULT 'Pending' NOT NULL,
  shipping_address TEXT NOT NULL,
  tracking_code TEXT NOT NULL,
  facilitator_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (order_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table order_items
--
CREATE TABLE IF NOT EXISTS order_items (
  item_id INT NOT NULL AUTO_INCREMENT,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  item_status ENUM('Pending', 'Shipped', 'Delivered') DEFAULT 'Pending' NOT NULL,
  tracking_code TEXT NOT NULL,
  store_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  finalized ENUM('Yes', 'No') DEFAULT 'No' NOT NULL,
  PRIMARY KEY (item_id),
  FOREIGN KEY (order_id) REFERENCES orders (order_id),
  FOREIGN KEY (product_id) REFERENCES products (product_id),
  FOREIGN KEY (store_id) REFERENCES stores (store_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table payments
--
CREATE TABLE IF NOT EXISTS payments (
  payment_id INT AUTO_INCREMENT,
  order_id INT NOT NULL,
  user_id INT NOT NULL,
  amount DECIMAL(10, 2) NOT NULL,
  reference VARCHAR(100) NOT NULL UNIQUE,
  currency VARCHAR(10) DEFAULT 'NGN',
  status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (payment_id),
  FOREIGN KEY (order_id) REFERENCES orders(order_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table topups
--
CREATE TABLE IF NOT EXISTS topups (
  topup_id INT AUTO_INCREMENT,
  user_id INT NOT NULL,
  amount DECIMAL(10, 2) NOT NULL,
  reference VARCHAR(100) NOT NULL UNIQUE,
  currency VARCHAR(10) DEFAULT 'NGN',
  status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (topup_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table wishlist
--

CREATE TABLE IF NOT EXISTS wishlist (
  wishlist_id INT NOT NULL AUTO_INCREMENT,
  product_id INT NOT NULL,
  user_id INT NOT NULL,
  PRIMARY KEY (wishlist_id),
  FOREIGN KEY (product_id) REFERENCES products (product_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table reviews
--
CREATE TABLE IF NOT EXISTS reviews (
  review_id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  comment TEXT NOT NULL,
  rating INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (review_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id),
  FOREIGN KEY (product_id) REFERENCES products (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table general_notifications
--

CREATE TABLE IF NOT EXISTS general_notifications (
  notification_id INT NOT NULL AUTO_INCREMENT,
  notification_details VARCHAR(1000) NOT NULL,
  notification_type VARCHAR(255) NOT NULL,
  notification_receiver INT NOT NULL,
  notification_date VARCHAR(50) NOT NULL,
  notification_status VARCHAR(50) NOT NULL,
  PRIMARY KEY (notification_id),
  FOREIGN KEY (notification_receiver) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table mailbox
--

CREATE TABLE IF NOT EXISTS mailbox (
  mail_id INT NOT NULL AUTO_INCREMENT,
  mail_type VARCHAR(100) NOT NULL,
  mail_subject VARCHAR(1000) NOT NULL,
  mail_sender VARCHAR(2000) NOT NULL,
  mail_receiver VARCHAR(2000) NOT NULL,
  mail_date VARCHAR(100) NOT NULL,
  mail_time VARCHAR(100) NOT NULL,
  mail_message VARCHAR(5000) NOT NULL,
  mail_filename VARCHAR(255) NOT NULL,
  mail_extension VARCHAR(20) NOT NULL,
  PRIMARY KEY (mail_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table push_tokens
--
CREATE TABLE IF NOT EXISTS push_tokens (
  token_id INT AUTO_INCREMENT PRIMARY KEY,
  token TEXT NOT NULL,
  device_id VARCHAR(64) NOT NULL,
  user_id INT NOT NULL,
  user_type ENUM('Admin','Affiliate','Customer','Vendor','Worker') NOT NULL,
  is_active TINYINT(1) DEFAULT 1,
  last_seen DATETIME,
  UNIQUE KEY uniq_device (user_id, device_id),
  UNIQUE KEY uniq_token (token(255)),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- --------------------------------------------------------

--
-- Table structure for table jobs_log
--
CREATE TABLE jobs_log (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,

  queue VARCHAR(100) NOT NULL,

  job_class VARCHAR(255) NOT NULL,

  payload LONGTEXT NOT NULL,

  status ENUM(
    'pending',
    'processing',
    'completed',
    'failed'
  ) NOT NULL DEFAULT 'pending',

  attempts INT DEFAULT 0,

  max_attempts INT DEFAULT 3,

  error_message TEXT NULL,

  execution_time FLOAT NULL,

  available_at TIMESTAMP NULL,

  processed_at TIMESTAMP NULL,

  failed_at TIMESTAMP NULL,

  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

--
-- Committing changes to the database
--
COMMIT;