-- References For Tables
/*
users table

id (PRIMARY KEY)
name
email
password
created_at
-----------------------------------------------------
items table

item_id (PRIMARY KEY)
title
description
price
image_path
seller_id (FOREIGN KEY) of id in users table
status
created_at
-----------------------------------------------------
messages table

msg_id (PRIMARY KEY)
sender_id (FOREIGN KEY) of id in users table
receiver_id (FOREIGN KEY) of id in users table
item_id (FOREIGN KEY) of item_id in items table
message
created_at
-----------------------------------------------------
comments table

commment_id
commenter_id (FOREIGN KEY) of id in users table
item_id (FOREIGN KEY) of item_id in items table
comment
created_at
-----------------------------------------------------
cart

cart_id (PRIMARY KEY)
id (FOREIGN KEY) of id in users table
item_id (FOREIGN KEY) of item_id in items table
quantity
-----------------------------------------------------
transactions

transaction_id (PRIMARY KEY)
buyer_id (FOREIGN KEY) of id in users table
seller_id (FOREIGN KEY) of id in users table
item_id (FOREIGN KEY) of item_id in items table
status
created_at
*/

-- TABLE 1: users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    course VARCHAR(100) DEFAULT NULL,
    year_level VARCHAR(20) DEFAULT NULL,
    bio VARCHAR(255) DEFAULT NULL,
    contact_info VARCHAR(100) DEFAULT NULL,
    avatar VARCHAR(20) DEFAULT 'junimo_0',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TABLE 2: categories
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO categories (name) VALUES
('Books'),
('Electronics'),
('Supplies'),
('Clothing'),
('Food'),
('Services'),
('Other');

-- TABLE 3: items
CREATE TABLE items (
    item_id INT PRIMARY KEY AUTO_INCREMENT,
    seller_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    image_path VARCHAR(255) DEFAULT NULL,
    condition_type ENUM('New','Like New','Good','Fair','N/A') NOT NULL,
    meetup_location VARCHAR(100) DEFAULT NULL,
    contact_info VARCHAR(100) DEFAULT NULL,
    status ENUM('active','sold') DEFAULT 'active',
    views INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT
);

-- TABLE 4: cart
CREATE TABLE cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_cart_item (user_id, item_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE
);

-- TABLE 5: messages
CREATE TABLE messages (
    msg_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    item_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE
);

-- TABLE 6: comments
CREATE TABLE comments (
    comment_id INT PRIMARY KEY AUTO_INCREMENT,
    commenter_id INT NOT NULL,
    item_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commenter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE
);

-- TABLE 7: transactions
CREATE TABLE transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    item_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','completed','cancelled') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME DEFAULT NULL,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE
);

-- TABLE 8: reports
CREATE TABLE reports (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    reporter_id INT NOT NULL,
    item_id INT NOT NULL,
    reason VARCHAR(50) NOT NULL,
    details TEXT DEFAULT NULL,
    status ENUM('open','resolved','dismissed') DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE
);

-- TABLE 9: saved_items
CREATE TABLE saved_items (
    save_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    saved_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_saved (user_id, item_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE
);

-- TABLE 10: notifications
CREATE TABLE notifications (
    notif_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message VARCHAR(255) NOT NULL,
    link VARCHAR(255) DEFAULT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);