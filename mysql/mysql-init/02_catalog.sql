CREATE TABLE category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE brand (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    brand_id INT,
    FOREIGN KEY (category_id) REFERENCES category(id),
    FOREIGN KEY (brand_id) REFERENCES brand(id)
);

INSERT INTO category (name) VALUES
('Electronics'),
('Clothing'),
('Books');

INSERT INTO brand (name) VALUES
('Apple'),
('Nike'),
('Penguin');

INSERT INTO product (name, description, price, category_id, brand_id) VALUES
('iPhone', 'A smartphone by Apple', 999.99, 1, 1),
('T-Shirt', 'A comfortable cotton t-shirt', 19.99, 2, 2),
('Novel', 'A bestselling novel', 9.99, 3, 3);