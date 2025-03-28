CREATE DATABASE IF NOT EXISTS ecommerce;
       
USE ecommerce;

-- Drop existing tables if they exist (in correct order due to foreign keys)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    type ENUM('admin', 'customer') DEFAULT 'customer'
);

-- Create categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

-- Create products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    category_id INT,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Create orders table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address TEXT,
    phone VARCHAR(20),
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method ENUM('cod', 'bank_transfer') DEFAULT 'cod',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create order_items table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_order_user ON orders(user_id);
CREATE INDEX idx_order_status ON orders(status); 

-- seeding data 


-- Insert default admin user (password: admin123)
INSERT INTO users (email, password, type) 
VALUES ('admin@gmail.com', '$2y$10$ESHJQLrUySuRVfByHKne3.XePazstIdJFkAuw.Cl0J8Lo4mUh7QEu', 'admin');
-- Insert sample categories
INSERT INTO categories (name) VALUES 
('T-shirts'),
('Pants'),
('Handbags'),
('Shoes'),
('Salwar Suits'),
('Lingerie');

-- Insert sample products
-- INSERT INTO products (name, category_id, description, price, image) VALUES 
-- ('Cotton T-shirt', 1, 'Comfortable clothing cotton t-shirt', 19.99, '/assets/products/image1.jpg'),
-- ('Jeans', 2, 'Comfortable clothing jeans', 29.99, '/assets/products/image2.jpg'),
-- ('Handbag', 3, 'Stylish handbag for everyday use', 79.99, '/assets/products/image3.jpg'),
-- ('Shoes', 4, 'Comfortable and durable running shoes', 59.99, '/assets/products/image4.jpg'),
-- ('Salwar Suit', 5, 'Comfortable and elegant salwar suit', 49.99, '/assets/products/image5.jpg'),
-- ('Lingerie', 6, 'Comfortable and elegant lingerie set', 49.99, '/assets/products/image6.jpg'),
-- ('T-shirt', 1, 'Comfortable clothing cotton t-shirt', 19.99, '/assets/products/image1.jpg'),
-- ('Pants', 2, 'Comfortable clothing pants', 29.99, '/assets/products/image2.jpg'),
-- ('Handbag', 3, 'Stylish handbag for everyday use', 79.99, '/assets/products/image3.jpg'),
-- ('Shoes', 4, 'Comfortable and durable running shoes', 59.99, '/assets/products/image4.jpg'),
-- ('Salwar Suit', 5, 'Comfortable and elegant salwar suit', 49.99, '/assets/products/image5.jpg'),
-- ('Lingerie', 6, 'Comfortable and elegant lingerie set', 49.99, '/assets/products/image6.jpg');