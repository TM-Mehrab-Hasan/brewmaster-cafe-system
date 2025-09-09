-- Complete Cafe Management System Database Setup
-- This script will create a fresh database with all required tables and sample data

-- Drop existing database if it exists (for clean setup)
DROP DATABASE IF EXISTS cafe_management;

-- Create the database
CREATE DATABASE cafe_management;
USE cafe_management;

-- Create admins table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create customers table
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create staff table
CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create kitchen_staff table
CREATE TABLE kitchen_staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table with stock management
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    image_url VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    staff_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','preparing','ready','delivered','cancelled') DEFAULT 'pending',
    order_type ENUM('dine_in','takeaway','delivery') NOT NULL,
    table_number VARCHAR(10),
    delivery_address TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE SET NULL
);

-- Create order_items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    special_instructions TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Create payments table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    staff_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash','card','bkash','nagad','rocket') NOT NULL,
    received_amount DECIMAL(10,2) NOT NULL,
    change_amount DECIMAL(10,2) GENERATED ALWAYS AS (received_amount - amount) STORED,
    notes TEXT,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (staff_id) REFERENCES staff(id)
);

-- Create stock_movements table for inventory tracking
CREATE TABLE stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    change_amount INT NOT NULL,
    reason ENUM('restock', 'damaged', 'expired', 'sold', 'adjustment', 'other') NOT NULL,
    notes TEXT,
    admin_id INT,
    previous_stock INT NOT NULL,
    new_stock INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
);

-- Create low_stock_alerts table for managing alerts
CREATE TABLE low_stock_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    threshold_level INT NOT NULL DEFAULT 10,
    is_active BOOLEAN DEFAULT TRUE,
    last_alert_sent TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_alert (product_id)
);

-- Insert sample data for testing
-- Sample admin account (username: admin, password: admin123)
INSERT INTO admins (username, password, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@cafe.com');

-- Sample customers
INSERT INTO customers (name, email, password, phone) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01711111111'),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01722222222'),
('Alice Johnson', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01733333333');

-- Sample staff
INSERT INTO staff (name, email, password, phone, status) VALUES
('Mike Wilson', 'mike@cafe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01744444444', 'approved'),
('Sarah Connor', 'sarah@cafe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01755555555', 'approved');

-- Sample kitchen staff
INSERT INTO kitchen_staff (name, email, password, phone, status) VALUES
('Chef Rodriguez', 'chef@cafe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01766666666', 'approved'),
('Cook Peterson', 'cook@cafe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01777777777', 'approved');

-- Sample products with stock quantities
INSERT INTO products (name, description, price, category, image_url, is_available, stock_quantity) VALUES
('Espresso', 'Strong black coffee shot', 120.00, 'Coffee', 'images/espresso.jpg', TRUE, 45),
('Cappuccino', 'Espresso with steamed milk and foam', 180.00, 'Coffee', 'images/cappuccino.jpg', TRUE, 38),
('Latte', 'Espresso with steamed milk', 200.00, 'Coffee', 'images/latte.jpg', TRUE, 42),
('Americano', 'Espresso with hot water', 150.00, 'Coffee', 'images/americano.jpg', TRUE, 35),
('Mocha', 'Chocolate flavored coffee drink', 220.00, 'Coffee', 'images/mocha.jpg', TRUE, 28),
('Green Tea', 'Fresh green tea', 100.00, 'Tea', 'images/green_tea.jpg', TRUE, 50),
('Earl Grey', 'Classic black tea with bergamot', 120.00, 'Tea', 'images/earl_grey.jpg', TRUE, 40),
('Chocolate Croissant', 'Buttery pastry with chocolate', 150.00, 'Pastry', 'images/choc_croissant.jpg', TRUE, 25),
('Blueberry Muffin', 'Fresh baked muffin with blueberries', 180.00, 'Pastry', 'images/blueberry_muffin.jpg', TRUE, 30),
('Cheesecake', 'Rich and creamy cheesecake slice', 280.00, 'Dessert', 'images/cheesecake.jpg', TRUE, 15),
('Club Sandwich', 'Triple layer sandwich with chicken and bacon', 320.00, 'Food', 'images/club_sandwich.jpg', TRUE, 20),
('Caesar Salad', 'Fresh romaine lettuce with caesar dressing', 250.00, 'Food', 'images/caesar_salad.jpg', TRUE, 18);

-- Insert low stock alerts for all products
INSERT INTO low_stock_alerts (product_id, threshold_level) 
SELECT id, 10 FROM products;

-- Insert sample stock movements for testing
INSERT INTO stock_movements (product_id, change_amount, reason, notes, previous_stock, new_stock) VALUES
(1, 45, 'restock', 'Initial stock setup', 0, 45),
(2, 38, 'restock', 'Initial stock setup', 0, 38),
(3, 42, 'restock', 'Initial stock setup', 0, 42),
(4, 35, 'restock', 'Initial stock setup', 0, 35),
(5, 28, 'restock', 'Initial stock setup', 0, 28),
(6, 50, 'restock', 'Initial stock setup', 0, 50),
(7, 40, 'restock', 'Initial stock setup', 0, 40),
(8, 25, 'restock', 'Initial stock setup', 0, 25),
(9, 30, 'restock', 'Initial stock setup', 0, 30),
(10, 15, 'restock', 'Initial stock setup', 0, 15),
(11, 20, 'restock', 'Initial stock setup', 0, 20),
(12, 18, 'restock', 'Initial stock setup', 0, 18);
