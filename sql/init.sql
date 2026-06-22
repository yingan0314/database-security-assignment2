USE fooddb;

CREATE TABLE IF NOT EXISTS users (user_id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, role VARCHAR(20) DEFAULT 'customer', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS menu (food_id INT AUTO_INCREMENT PRIMARY KEY, food_name VARCHAR(100) NOT NULL, price DECIMAL(10,2) NOT NULL, description TEXT, image VARCHAR(255), category VARCHAR(50));
CREATE TABLE IF NOT EXISTS cart (cart_id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, food_id INT NOT NULL, quantity INT NOT NULL DEFAULT 1, total_price DECIMAL(10,2) NOT NULL, FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE, FOREIGN KEY (food_id) REFERENCES menu(food_id) ON DELETE CASCADE);
CREATE TABLE IF NOT EXISTS orders (order_id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, food_id INT NOT NULL, quantity INT NOT NULL, total_price DECIMAL(10,2) NOT NULL, status VARCHAR(20) DEFAULT 'PAID', order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (user_id) REFERENCES users(user_id), FOREIGN KEY (food_id) REFERENCES menu(food_id));
CREATE TABLE IF NOT EXISTS user_cards (id INT AUTO_INCREMENT PRIMARY KEY, CustomerID INT NOT NULL, CardType VARCHAR(20), CardNumber VARCHAR(255), CardLast4 VARCHAR(4), ExpMonth INT, ExpYear INT, FOREIGN KEY (CustomerID) REFERENCES users(user_id) ON DELETE CASCADE);
CREATE TABLE IF NOT EXISTS login_logs (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50), status VARCHAR(20), ip_address VARCHAR(45), attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP);

INSERT IGNORE INTO users (username, password, role) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
INSERT IGNORE INTO users (username, password, role) VALUES ('chen', '$2y$10$rAKYlMZe.KdAJHDsz3ERzuVrNhhUCTFcb9R7dKT0lXEqrFFsPmqd.', 'customer');

INSERT INTO `menu` (`food_id`, `food_name`, `price`, `description`, `image`, `category`) VALUES
(1, 'Nasi Lemak', 5.50, 'Coconut rice with sambal', 'images/nasi_lemak.jpg', 'rice'),
(2, 'Chicken Rice', 6.50, 'Hainanese chicken rice', 'images/chicken_rice.jpg', 'rice'),
(3, 'Beef Rice Bowl', 8.50, 'Beef with rice bowl', 'images/beef_rice.jpg', 'rice'),
(4, 'Fried Rice', 5.80, 'Egg fried rice', 'images/fried_rice.jpg', 'rice'),
(5, 'Seafood Rice', 9.90, 'Mixed seafood with rice', 'images/seafood_rice.jpg', 'rice'),
(6, 'Curry Rice', 7.00, 'Rice with curry sauce', 'images/curry_rice.jpg', 'rice'),
(7, 'Egg Fried Rice', 6.20, 'Simple egg fried rice', 'images/egg_fried_rice.jpg', 'rice'),
(8, 'Grilled Chicken Rice', 10.00, 'Grilled chicken with rice', 'images/grilled_chicken_rice.jpg', 'rice'),
(9, 'Spicy Rice Bowl', 7.80, 'Spicy Malaysian rice bowl', 'images/spicy_rice.jpg', 'rice'),
(10, 'Vegetable Rice', 5.00, 'Healthy veggie rice', 'images/veg_rice.jpg', 'rice'),
(11, 'Salted Egg Rice', 8.90, 'Salted egg sauce rice', 'images/salted_egg_rice.JPG', 'rice'),
(12, 'Fried Chicken Rice', 9.50, 'Crispy fried chicken rice', 'images/fried_chicken_rice.jpg', 'rice'),
(13, 'BBQ Rice', 9.00, 'BBQ meat rice bowl', 'images/bbq_rice.webp', 'rice'),
(14, 'Tomyam Rice', 8.20, 'Thai style tomyam rice', 'images/tomyam_rice.jpg', 'rice'),
(15, 'Butter Rice', 6.80, 'Butter flavored rice', 'images/butter_rice.jpg', 'rice'),
(16, 'Mee Goreng', 6.00, 'Spicy fried noodles', 'images/mee_goreng.jpg', 'noodle'),
(17, 'Kuey Teow', 6.50, 'Fried flat noodles', 'images/kuey_teow.jpg', 'noodle'),
(18, 'Maggi Goreng', 5.50, 'Instant noodle fried style', 'images/maggi_goreng.jpg', 'noodle'),
(19, 'Laksa', 7.90, 'Spicy coconut noodle soup', 'images/laksa.jpg', 'noodle'),
(20, 'Wantan Mee', 6.80, 'Egg noodles with dumpling', 'images/wantan_mee.jpg', 'noodle'),
(21, 'Mee Rebus', 6.20, 'Sweet potato gravy noodles', 'images/mee_rebus.jpg', 'noodle'),
(22, 'Hokkien Mee', 7.50, 'Dark soy fried noodles', 'images/hokkien_mee.jpg', 'noodle'),
(23, 'Bee Hoon Soup', 5.90, 'Rice noodle soup', 'images/bee_hoon.jpg', 'noodle'),
(24, 'Tomyam Noodle', 8.50, 'Spicy Thai noodles', 'images/tomyam_noodle.jpg', 'noodle'),
(25, 'Chicken Noodle', 6.70, 'Chicken soup noodles', 'images/chicken_noodle.jpg', 'noodle'),
(26, 'Seafood Noodle', 9.20, 'Seafood noodle bowl', 'images/seafood_noodle.jpg\r\n', 'noodle'),
(27, 'Dry Mee', 6.10, 'Dry style noodles', 'images/dry_mee.webp', 'noodle'),
(28, 'Beef Noodle', 8.80, 'Beef broth noodles', 'images/beef_noodle.jpg', 'noodle'),
(29, 'Vegetable Noodle', 5.80, 'Healthy veggie noodles', 'images/veg_noodle.webp', 'noodle'),
(30, 'Cheese Ramen', 9.00, 'Cheesy ramen noodles', 'images/cheese_ramen.jpg', 'noodle'),
(31, 'Chicken Burger', 8.90, 'Grilled chicken burger', 'images/chicken_burger.webp', 'western'),
(32, 'Beef Burger', 9.90, 'Juicy beef burger', 'images/beef_burger.webp', 'western'),
(33, 'Chicken Chop', 12.90, 'Fried chicken with sauce', 'images/chicken_chop.jpg', 'western'),
(34, 'Fish and Chips', 13.00, 'Crispy fish with fries', 'images/fish_chips.jpg', 'western'),
(35, 'Spaghetti Bolognese', 11.50, 'Pasta with meat sauce', 'images/spaghetti.jpg', 'western'),
(36, 'Carbonara', 12.00, 'Creamy pasta', 'images/carbonara.jpg', 'western'),
(37, 'Pizza Pepperoni', 15.90, 'Cheesy pepperoni pizza', 'images/pizza.jpg', 'western'),
(38, 'Grilled Steak', 18.90, 'Juicy beef steak', 'images/steak.jpg', 'western'),
(39, 'Chicken Wings', 10.50, 'Crispy chicken wings', 'images/wings.jpg', 'western'),
(40, 'Mac & Cheese', 9.50, 'Cheesy pasta', 'images/mac_cheese.jpg', 'western'),
(41, 'Grilled Salmon', 17.00, 'Salmon with herbs', 'images/salmon.jpg', 'western'),
(42, 'Hotdog', 6.50, 'Classic hotdog bun', 'images/hotdog.webp', 'western'),
(43, 'Cheeseburger', 10.00, 'Burger with cheese', 'images/cheeseburger.webp', 'western'),
(44, 'Chicken Nuggets', 7.50, 'Crispy nuggets', 'images/nuggets.jpg', 'western'),
(45, 'BBQ Ribs', 19.90, 'BBQ pork ribs', 'images/ribs.webp', 'western'),
(46, 'Coca Cola', 3.00, 'Cold soft drink', 'images/coke.jpg', 'drink'),
(47, 'Pepsi', 3.00, 'Pepsi soft drink', 'images/pepsi.webp', 'drink'),
(48, 'Sprite', 3.00, 'Lemon lime drink', 'images/sprite.jpg', 'drink'),
(49, 'Iced Lemon Tea', 3.50, 'Refreshing tea', 'images/lemon_tea.webp', 'drink'),
(50, 'Teh Tarik', 2.50, 'Malaysia milk tea', 'images/teh_tarik.jpg', 'drink'),
(51, 'Milo Ice', 4.00, 'Chocolate malt drink', 'images/milo.avif', 'drink'),
(52, 'Orange Juice', 4.50, 'Fresh orange juice', 'images/orange_juice.jpg', 'drink'),
(53, 'Apple Juice', 4.50, 'Fresh apple juice', 'images/apple_juice.jpeg', 'drink'),
(54, 'Mineral Water', 1.50, 'Plain water', 'images/water.jpg', 'drink'),
(55, 'Coffee Ice', 3.80, 'Cold coffee drink', 'images/coffee.jpg', 'drink'),
(56, 'Chocolate Shake', 5.50, 'Chocolate milkshake', 'images/choco_shake.jpg', 'drink'),
(57, 'Strawberry Shake', 5.50, 'Strawberry milkshake', 'images/strawberry_shake.jpg', 'drink'),
(58, 'Green Tea', 3.20, 'Cold green tea', 'images/green_tea.jpg', 'drink'),
(59, 'Bandung', 3.50, 'Rose milk drink', 'images/bandung.png', 'drink'),
(60, 'Soy Milk', 3.00, 'Healthy soy drink', 'images/soy_milk.webp', 'drink');
