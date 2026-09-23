--
-- Table structure for table  users
--
CREATE TABLE IF NOT EXISTS users (
  user_id INT NOT NULL AUTO_INCREMENT,
  avatar VARCHAR(100) NOT NULL,
  firstname VARCHAR(255) NOT NULL,
  lastname VARCHAR(255) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  contact VARCHAR(100) NOT NULL,
  country VARCHAR(100) NOT NULL,
  user_state VARCHAR(100) NOT NULL,
  user_password VARCHAR(255) NOT NULL,
  user_role ENUM('Admin', 'Affiliate', 'Customer', 'Vendor', 'Worker') DEFAULT 'Customer' NOT NULL,
  user_status ENUM('Active', 'Pending', 'Deactivated') DEFAULT 'Pending' NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table user_documents
--
CREATE TABLE IF NOT EXISTS user_documents (
  document_id INT NOT NULL AUTO_INCREMENT,
  identity_file VARCHAR(255) NOT NULL,
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
  facebook VARCHAR(255) NOT NULL,
  instagram VARCHAR(255) NOT NULL,
  tiktok VARCHAR(255) NOT NULL,
  twitter VARCHAR(255) NOT NULL,
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
  bank_name VARCHAR(100) NOT NULL,
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
  delivery_address VARCHAR(255) NOT NULL,
  city VARCHAR(255) NOT NULL,
  postcode VARCHAR(255) NOT NULL,
  user_id INT NOT NULL,
  PRIMARY KEY (detail_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table wallet_shopping
--

CREATE TABLE IF NOT EXISTS wallet_shopping (
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
-- Table structure for table withdrawals
--

CREATE TABLE IF NOT EXISTS withdrawals (
  withdrawal_id INT NOT NULL AUTO_INCREMENT,
  amount DECIMAL(15,2) NOT NULL,
  bank VARCHAR(255) NOT NULL,
  account VARCHAR(20) NOT NULL,
  reference VARCHAR(255) NOT NULL,
  narration VARCHAR(255) NOT NULL,
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
  store_avatar VARCHAR(255) NOT NULL,
  store_description VARCHAR(1000) NOT NULL,
  store_status ENUM('Pending', 'Active', 'Deactivated') DEFAULT 'Pending' NOT NULL,
  store_delivery VARCHAR(100) NOT NULL,
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
  facebook VARCHAR(255) NOT NULL,
  instagram VARCHAR(255) NOT NULL,
  tiktok VARCHAR(255) NOT NULL,
  twitter VARCHAR(255) NOT NULL,
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
  category_name VARCHAR(100) NOT NULL,
  PRIMARY KEY (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table product_subcategories
--
CREATE TABLE IF NOT EXISTS product_subcategories (
  subcategory_id INT NOT NULL AUTO_INCREMENT,
  subcategory_name VARCHAR(100) NOT NULL,
  category_id INT NOT NULL,
  PRIMARY KEY (subcategory_id),
  FOREIGN KEY (category_id) REFERENCES product_categories (category_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table products
--
CREATE TABLE IF NOT EXISTS products (
  product_id INT NOT NULL AUTO_INCREMENT,
  product_name VARCHAR(255) NOT NULL,
  product_description TEXT NOT NULL,
  category VARCHAR(100) NOT NULL,
  sub_category VARCHAR(100) NOT NULL,
  product_price DECIMAL(10, 2) NOT NULL,
  slash_price DECIMAL(10, 2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  color VARCHAR(100) NOT NULL,
  store_id INT NOT NULL,
  reselling ENUM('Enabled', 'Disabled') DEFAULT 'Disabled' NOT NULL,
  commission INT DEFAULT 0 NOT NULL,
  visibility ENUM('Visible', 'Hidden') DEFAULT 'Visible' NOT NULL,
  is_featured BOOLEAN DEFAULT FALSE NOT NULL,
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
  media_url VARCHAR(255) NOT NULL,
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
  long_link VARCHAR(255) NOT NULL,
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
  notification_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  notification_status VARCHAR(50) DEFAULT 'Unread' NOT NULL,
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
  mail_subject VARCHAR(255) NOT NULL,
  mail_sender VARCHAR(255) NOT NULL,
  mail_receiver VARCHAR(255) NOT NULL,
  mail_date VARCHAR(100) NOT NULL,
  mail_time VARCHAR(100) NOT NULL,
  mail_message VARCHAR(1000) NOT NULL,
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

-- =========================================================
-- USERS
-- =========================================================

CREATE INDEX idx_users_role
  ON users (user_role);

CREATE INDEX idx_users_status
  ON users (user_status);

CREATE INDEX idx_users_role_status
  ON users (user_role, user_status);

CREATE INDEX idx_users_created_at
  ON users (created_at);


-- =========================================================
-- USER DOCUMENTS
-- =========================================================

CREATE INDEX idx_user_documents_user_id
  ON user_documents (user_id);


-- =========================================================
-- USER SOCIALS
-- =========================================================

CREATE INDEX idx_user_socials_user_id
  ON user_socials (user_id);


-- =========================================================
-- BANK DETAILS
-- =========================================================

CREATE INDEX idx_bank_details_user_id
  ON bank_details (user_id);


-- =========================================================
-- BILLING DETAILS
-- =========================================================

CREATE INDEX idx_billing_details_user_id
  ON billing_details (user_id);


-- =========================================================
-- WALLETS
-- =========================================================

CREATE INDEX idx_wallet_shopping_user_id
  ON wallet_shopping (user_id);

CREATE INDEX idx_wallet_payout_user_id
  ON wallet_payout (user_id);

CREATE INDEX idx_wallet_payout_backup_user_id
  ON wallet_payout_backup (user_id);


-- =========================================================
-- WITHDRAWALS
-- =========================================================

CREATE INDEX idx_withdrawals_user_id
  ON withdrawals (user_id);

CREATE INDEX idx_withdrawals_status
  ON withdrawals (withdrawal_status);

CREATE INDEX idx_withdrawals_user_status
  ON withdrawals (user_id, withdrawal_status);

CREATE INDEX idx_withdrawals_created_at
  ON withdrawals (created_at);

CREATE INDEX idx_withdrawals_reference
  ON withdrawals (reference);


-- =========================================================
-- STORES
-- =========================================================

CREATE INDEX idx_stores_user_id
  ON stores (user_id);

CREATE INDEX idx_stores_status
  ON stores (store_status);

CREATE INDEX idx_stores_user_status
  ON stores (user_id, store_status);

CREATE INDEX idx_stores_created_at
  ON stores (created_at);


-- =========================================================
-- STORE SOCIALS
-- =========================================================

CREATE INDEX idx_store_socials_store_id
  ON store_socials (store_id);


-- =========================================================
-- STORE COUPONS
-- =========================================================

CREATE INDEX idx_store_coupons_store_id
  ON store_coupons (store_id);

CREATE INDEX idx_store_coupons_status
  ON store_coupons (coupon_status);

CREATE INDEX idx_store_coupons_store_status
  ON store_coupons (store_id, coupon_status);

CREATE INDEX idx_store_coupons_code
  ON store_coupons (coupon_code);


-- =========================================================
-- PRODUCT CATEGORIES
-- =========================================================

CREATE INDEX idx_product_categories_name
  ON product_categories (category_name);


-- =========================================================
-- PRODUCT SUBCATEGORIES
-- =========================================================

CREATE INDEX idx_product_subcategories_category_id
  ON product_subcategories (category_id);

CREATE INDEX idx_product_subcategories_name
  ON product_subcategories (subcategory_name);


-- =========================================================
-- PRODUCTS
-- =========================================================

CREATE INDEX idx_products_store_id
  ON products (store_id);

CREATE INDEX idx_products_category
  ON products (category);

CREATE INDEX idx_products_sub_category
  ON products (sub_category);

CREATE INDEX idx_products_store_visibility
  ON products (store_id, visibility);

CREATE INDEX idx_products_visibility
  ON products (visibility);

CREATE INDEX idx_products_featured
  ON products (is_featured);

CREATE INDEX idx_products_store_featured
  ON products (store_id, is_featured);

CREATE INDEX idx_products_reselling
  ON products (reselling);

CREATE INDEX idx_products_created_at
  ON products (created_at);


-- =========================================================
-- PRODUCT MEDIA
-- =========================================================

CREATE INDEX idx_product_media_product_id
  ON product_media (product_id);


-- =========================================================
-- PRODUCT LINKS
-- =========================================================

CREATE INDEX idx_product_links_product_id
  ON product_links (product_id);

CREATE INDEX idx_product_links_user_id
  ON product_links (user_id);

CREATE INDEX idx_product_links_short_code
  ON product_links (short_code);

CREATE INDEX idx_product_links_status
  ON product_links (link_status);

CREATE INDEX idx_product_links_user_status
  ON product_links (user_id, link_status);


-- =========================================================
-- CART
-- =========================================================

CREATE INDEX idx_cart_user_id
  ON cart (user_id);

CREATE INDEX idx_cart_product_id
  ON cart (product_id);

CREATE INDEX idx_cart_user_product
  ON cart (user_id, product_id);


-- =========================================================
-- WISHLIST
-- =========================================================

CREATE INDEX idx_wishlist_user_id
  ON wishlist (user_id);

CREATE INDEX idx_wishlist_product_id
  ON wishlist (product_id);

CREATE INDEX idx_wishlist_user_product
  ON wishlist (user_id, product_id);


-- =========================================================
-- ORDERS
-- =========================================================

CREATE INDEX idx_orders_user_id
  ON orders (user_id);

CREATE INDEX idx_orders_status
  ON orders (order_status);

CREATE INDEX idx_orders_user_status
  ON orders (user_id, order_status);

CREATE INDEX idx_orders_created_at
  ON orders (created_at);

CREATE INDEX idx_orders_facilitator_id
  ON orders (facilitator_id);


-- =========================================================
-- ORDER ITEMS
-- =========================================================

CREATE INDEX idx_order_items_order_id
  ON order_items (order_id);

CREATE INDEX idx_order_items_product_id
  ON order_items (product_id);

CREATE INDEX idx_order_items_store_id
  ON order_items (store_id);

CREATE INDEX idx_order_items_status
  ON order_items (item_status);

CREATE INDEX idx_order_items_store_status
  ON order_items (store_id, item_status);

CREATE INDEX idx_order_items_order_status
  ON order_items (order_id, item_status);


-- =========================================================
-- PAYMENTS
-- =========================================================

CREATE INDEX idx_payments_order_id
  ON payments (order_id);

CREATE INDEX idx_payments_user_id
  ON payments (user_id);

CREATE INDEX idx_payments_status
  ON payments (status);

CREATE INDEX idx_payments_user_status
  ON payments (user_id, status);

CREATE INDEX idx_payments_created_at
  ON payments (created_at);


-- =========================================================
-- TOPUPS
-- =========================================================

CREATE INDEX idx_topups_user_id
  ON topups (user_id);

CREATE INDEX idx_topups_status
  ON topups (status);

CREATE INDEX idx_topups_user_status
  ON topups (user_id, status);

CREATE INDEX idx_topups_created_at
  ON topups (created_at);


-- =========================================================
-- REVIEWS
-- =========================================================

CREATE INDEX idx_reviews_user_id
  ON reviews (user_id);

CREATE INDEX idx_reviews_product_id
  ON reviews (product_id);

CREATE INDEX idx_reviews_product_rating
  ON reviews (product_id, rating);

CREATE INDEX idx_reviews_created_at
  ON reviews (created_at);


-- =========================================================
-- NOTIFICATIONS
-- =========================================================

CREATE INDEX idx_notifications_receiver
  ON general_notifications (notification_receiver);

CREATE INDEX idx_notifications_receiver_status
  ON general_notifications (
        notification_receiver,
        notification_status
    );

CREATE INDEX idx_notifications_type
  ON general_notifications (notification_type);

CREATE INDEX idx_notifications_date
  ON general_notifications (notification_date);


-- =========================================================
-- MAILBOX
-- =========================================================

CREATE INDEX idx_mailbox_receiver
  ON mailbox (mail_receiver);

CREATE INDEX idx_mailbox_sender
  ON mailbox (mail_sender);

CREATE INDEX idx_mailbox_type
  ON mailbox (mail_type);

CREATE INDEX idx_mailbox_date
  ON mailbox (mail_date);


-- =========================================================
-- PUSH TOKENS
-- =========================================================

CREATE INDEX idx_push_tokens_user_id
  ON push_tokens (user_id);

CREATE INDEX idx_push_tokens_active
  ON push_tokens (is_active);

CREATE INDEX idx_push_tokens_user_active
  ON push_tokens (user_id, is_active);

CREATE INDEX idx_push_tokens_user_type
  ON push_tokens (user_id, user_type);

CREATE INDEX idx_push_tokens_last_seen
  ON push_tokens (last_seen);


-- =========================================================
-- JOBS
-- =========================================================

CREATE INDEX idx_jobs_log_queue_status
  ON jobs_log (queue, status);

CREATE INDEX idx_jobs_log_status
  ON jobs_log (status);

CREATE INDEX idx_jobs_log_available_at
  ON jobs_log (available_at);

CREATE INDEX idx_jobs_log_processing
  ON jobs_log (status, available_at);

CREATE INDEX idx_jobs_log_created_at
  ON jobs_log (created_at);

CREATE INDEX idx_jobs_log_job_class
  ON jobs_log (job_class);

-- --------------------------------------------------------

--
-- Committing changes to the database
--
COMMIT;