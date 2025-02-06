-- Create default admin user
INSERT INTO users (username, email, password, user_type) 
VALUES (
    'admin',
    'admin@resellu.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin
    'admin'
) ON DUPLICATE KEY UPDATE user_type = 'admin';

-- Sample product categories
INSERT INTO products (seller_id, title, description, price, category, condition_status, image_path) VALUES
(1, 'Calculus Textbook', 'Calculus: Early Transcendentals 8th Edition. Like new condition, no markings.', 45.00, 'Books', 'Like New', '/uploads/sample/calc_book.jpg'),
(1, 'Scientific Calculator', 'Texas Instruments TI-84 Plus. Perfect for math and science courses.', 60.00, 'Electronics', 'Good', '/uploads/sample/calculator.jpg'),
(1, 'Desk Lamp', 'LED desk lamp with adjustable brightness. Perfect for studying.', 15.00, 'Furniture', 'Excellent', '/uploads/sample/desk_lamp.jpg'),
(1, 'Lab Coat', 'White lab coat, size M. Required for chemistry labs.', 20.00, 'Clothing', 'New', '/uploads/sample/lab_coat.jpg'),
(1, 'Tennis Racket', 'Wilson tennis racket with case. Great for beginners.', 30.00, 'Sports', 'Good', '/uploads/sample/tennis_racket.jpg'),
(1, 'Backpack', 'North Face backpack, 28L capacity. Very durable.', 35.00, 'Other', 'Good', '/uploads/sample/backpack.jpg');

-- Sample donation items
INSERT INTO donations (donor_id, title, description, image_path) VALUES
(1, 'Old Textbooks', 'Various introductory level textbooks. Free to anyone who needs them.', '/uploads/sample/textbooks.jpg'),
(1, 'Study Desk', 'Simple wooden desk. Must pick up from dorm.', '/uploads/sample/desk.jpg'),
(1, 'Winter Coat', 'Warm winter coat, size L. Perfect for cold weather.', '/uploads/sample/coat.jpg');

-- Sample requests
INSERT INTO requests (user_id, title, description, expected_price) VALUES
(1, 'Looking for Physics Textbook', 'Need Physics for Scientists and Engineers 10th Edition', 40.00),
(1, 'Need Graphing Calculator', 'Looking for TI-83 or TI-84 calculator for Calculus class', 50.00),
(1, 'Seeking Mini Fridge', 'Looking for a small fridge for dorm room', 60.00);
