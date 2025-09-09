-- SQL to create cafe_management database and tables for Cafe Management System
CREATE DATABASE IF NOT EXISTS cafe_management;
USE cafe_management;

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table with stock_quantity for inventory management
CREATE TABLE IF NOT EXISTS products (
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
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','preparing','ready','completed','cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Create payments table
CREATE TABLE IF NOT EXISTS payments (
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
CREATE TABLE IF NOT EXISTS stock_movements (
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
CREATE TABLE IF NOT EXISTS low_stock_alerts (
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

-- Sample staff (approved)
INSERT INTO staff (name, email, password, phone, status) VALUES
('Mike Wilson', 'mike@cafe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01744444444', 'approved'),
('Sarah Brown', 'sarah@cafe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01755555555', 'approved'),
('David Lee', 'david@cafe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01766666666', 'pending');

-- Sample products with initial stock quantities
INSERT INTO products (name, description, price, category, image_url, is_available, stock_quantity) VALUES
('Espresso', 'Rich and bold espresso shot', 120.00, 'Coffee', 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=300', TRUE, 45),
('Cappuccino', 'Classic cappuccino with steamed milk foam', 180.00, 'Coffee', 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=300', TRUE, 50),
('Latte', 'Smooth latte with perfect milk art', 200.00, 'Coffee', 'https://images.unsplash.com/photo-1561047029-3000c68339ca?w=300', TRUE, 35),
('Green Tea', 'Fresh organic green tea', 80.00, 'Tea', 'https://images.unsplash.com/photo-1556881286-fc6915169721?w=300', TRUE, 25),
('Black Tea', 'Traditional black tea with spices', 70.00, 'Tea', 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=300', TRUE, 30),
('Chocolate Croissant', 'Buttery croissant with chocolate filling', 150.00, 'Snacks', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300', TRUE, 20),
('Blueberry Muffin', 'Fresh baked muffin with blueberries', 120.00, 'Snacks', 'https://images.unsplash.com/photo-1607958996333-41aef7caefaa?w=300', TRUE, 15),
('Cheesecake', 'Creamy New York style cheesecake', 250.00, 'Desserts', 'https://images.unsplash.com/photo-1508737804141-4c3b688e2546?w=300', TRUE, 8),
('Chocolate Cake', 'Rich chocolate layer cake', 220.00, 'Desserts', 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=300', TRUE, 12),
('Fresh Orange Juice', 'Freshly squeezed orange juice', 100.00, 'Beverages', 'https://images.unsplash.com/photo-1613478223719-2ab802602423?w=300', TRUE, 40),
('Iced Coffee', 'Cold brew iced coffee', 160.00, 'Beverages', 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=300', TRUE, 25),
('Pancakes', 'Stack of fluffy pancakes with syrup', 280.00, 'Breakfast', 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=300', TRUE, 18);

-- Sample orders
INSERT INTO orders (customer_id, total_amount, status, order_date) VALUES
(1, 380.00, 'completed', '2025-08-27 10:30:00'),
(2, 520.00, 'ready', '2025-08-28 09:15:00'),
(3, 200.00, 'preparing', '2025-08-28 11:45:00'),
(1, 300.00, 'pending', '2025-08-28 12:30:00');

-- Sample order items
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 2, 120.00),
(1, 6, 1, 150.00),
(1, 5, 1, 70.00),
(2, 3, 2, 200.00),
(2, 7, 1, 120.00),
(2, 8, 1, 250.00),
(3, 2, 1, 180.00),
(3, 4, 1, 80.00),
(4, 12, 1, 280.00);

-- Sample payments for staff sales reports
INSERT INTO payments (order_id, staff_id, amount, payment_method, received_amount, notes, payment_date) VALUES
(1, 1, 380.00, 'cash', 400.00, 'Customer paid cash', '2025-08-27 10:35:00'),
(2, 2, 520.00, 'card', 520.00, 'Card payment processed', '2025-08-28 09:20:00');

-- Insert default low stock alerts for all products
INSERT INTO low_stock_alerts (product_id, threshold_level) 
SELECT id, 10 FROM products;

-- Sample stock movements for inventory history
INSERT INTO stock_movements (product_id, change_amount, reason, notes, admin_id, previous_stock, new_stock, created_at) VALUES
(1, 50, 'restock', 'Initial stock setup', 1, 0, 50, '2025-08-25 09:00:00'),
(1, -5, 'sold', 'Regular sales', 1, 50, 45, '2025-08-27 10:30:00'),
(3, 40, 'restock', 'Weekly restock', 1, 0, 40, '2025-08-25 09:00:00'),
(3, -5, 'sold', 'Regular sales', 1, 40, 35, '2025-08-28 09:15:00'),
(7, 20, 'restock', 'Fresh batch delivered', 1, 0, 20, '2025-08-26 08:30:00'),
(7, -5, 'sold', 'Regular sales', 1, 20, 15, '2025-08-28 11:45:00'),
(8, 15, 'restock', 'Daily fresh desserts', 1, 0, 15, '2025-08-27 07:00:00'),
(8, -3, 'sold', 'Regular sales', 1, 15, 12, '2025-08-28 09:20:00'),
(4, 30, 'restock', 'Tea leaves restocked', 1, 0, 30, '2025-08-25 09:00:00'),
(4, -5, 'sold', 'Regular sales', 1, 30, 25, '2025-08-28 11:45:00');
