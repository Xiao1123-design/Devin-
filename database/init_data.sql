-- Create default admin user
INSERT INTO users (username, email, password, user_type, nationality, age, gender, address) VALUES
('admin', 'admin@resellu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '中国', 30, 'other', '校园管理办公室'),
('student1', 'student1@resellu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '中国', 20, 'male', '学生宿舍A栋'),
('student2', 'student2@resellu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '中国', 21, 'female', '学生宿舍B栋'),
('teacher1', 'teacher1@resellu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', '中国', 35, 'female', '教师公寓'),
('alumni1', 'alumni1@resellu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', '中国', 25, 'male', '城市中心');

-- Books category
INSERT INTO products (seller_id, title, description, price, category, condition_status, image_path) VALUES
(2, '高等数学教材（第七版）', '九成新，配套习题册完整，有少量笔记，适合大一新生', 45.00, 'Books', '九成新', '/uploads/sample/math_book.jpg'),
(3, '英语四级备考资料全套', '2023年新版，含真题和模拟题，笔记整洁有效', 35.00, 'Books', '全新', '/uploads/sample/english_book.jpg'),
(4, '计算机科学导论教材', '全新未使用，2023年版，带电子资源和课后答案', 60.00, 'Books', '全新', '/uploads/sample/cs_book.jpg'),
(5, '考研政治复习资料', '包含近五年真题及解析，重点标注，配套笔记', 40.00, 'Books', '八成新', '/uploads/sample/politics_book.jpg');

-- Electronics category
INSERT INTO products (seller_id, title, description, price, category, condition_status, image_path) VALUES
(2, '科学计算器', '卡西欧FX-991CN X，95新，功能完好，带原包装', 120.00, 'Electronics', '九成新', '/uploads/sample/calculator.jpg'),
(3, '蓝牙耳机', '华为FreeBuds 4i，八成新，音质好，续航强，降噪效果好', 199.00, 'Electronics', '八成新', '/uploads/sample/headphones.jpg'),
(4, '移动电源', '小米20000mAh，支持快充，全新未拆封，送充电线', 88.00, 'Electronics', '全新', '/uploads/sample/powerbank.jpg'),
(5, '机械键盘', '红轴，87键，带RGB背光，使用3个月，手感极佳', 150.00, 'Electronics', '九成新', '/uploads/sample/keyboard.jpg');

-- Furniture category
INSERT INTO products (seller_id, title, description, price, category, condition_status, image_path) VALUES
(2, '宿舍书桌', '宜家款，可折叠，带书架，九成新，尺寸120x60cm', 150.00, 'Furniture', '九成新', '/uploads/sample/desk.jpg'),
(3, 'LED台灯', '可调光，带USB充电口，三色温调节，全新未拆封', 45.00, 'Furniture', '全新', '/uploads/sample/lamp.jpg'),
(4, '收纳柜', '三层抽屉，适合宿舍，85新，容量大实用', 78.00, 'Furniture', '八成新', '/uploads/sample/cabinet.jpg'),
(5, '单人床垫', '记忆棉材质，90x200cm，使用半年，无异味无污渍', 200.00, 'Furniture', '九成新', '/uploads/sample/mattress.jpg');

-- Clothing category
INSERT INTO products (seller_id, title, description, price, category, condition_status, image_path) VALUES
(2, '校园纪念卫衣', '学校logo，L码，穿过2-3次，保存完好', 60.00, 'Clothing', '九成新', '/uploads/sample/hoodie.jpg'),
(3, '耐克运动裤', '正品，M码，95新，带吊牌，适合运动健身', 88.00, 'Clothing', '九成新', '/uploads/sample/pants.jpg'),
(4, '毕业礼服', '只穿过一次，带学士帽，尺码可调，适合毕业照', 120.00, 'Clothing', '九成新', '/uploads/sample/gown.jpg'),
(5, '冬季校服外套', 'XL码，加绒加厚，全新未穿，原价280元', 150.00, 'Clothing', '全新', '/uploads/sample/uniform.jpg');

-- Sports category
INSERT INTO products (seller_id, title, description, price, category, condition_status, image_path) VALUES
(2, '斯伯丁篮球', '7号标准球，室内外通用，使用1个月，手感好', 85.00, 'Sports', '九成新', '/uploads/sample/basketball.jpg'),
(3, '瑜伽垫', 'NBR材质，加厚防滑，带背包，全新未拆封', 45.00, 'Sports', '全新', '/uploads/sample/yoga_mat.jpg'),
(4, '尤尼克斯羽毛球拍', '双拍套装，送羽毛球和拍套，9成新，适合初学', 99.00, 'Sports', '九成新', '/uploads/sample/badminton.jpg'),
(5, '跳绳', '可调节长度，电子计数，全新未拆封，送电池', 30.00, 'Sports', '全新', '/uploads/sample/jump_rope.jpg');

-- Other category
INSERT INTO products (seller_id, title, description, price, category, condition_status, image_path) VALUES
(2, '捷安特山地自行车', '21速变速，前后碟刹，骑行1000km，定期保养', 680.00, 'Other', '八成新', '/uploads/sample/bike.jpg'),
(3, '雅马哈民谣吉他', '41寸，送琴包和调音器，95新，音色优美', 299.00, 'Other', '九成新', '/uploads/sample/guitar.jpg'),
(4, '马利水彩颜料套装', '24色，送画笔和画本，拆封未使用，适合初学者', 120.00, 'Other', '全新', '/uploads/sample/art_set.jpg'),
(5, '帐篷', '2-3人露营帐篷，防雨防晒，使用1次，带收纳袋', 180.00, 'Other', '九成新', '/uploads/sample/tent.jpg');

-- Sample donations
INSERT INTO donations (donor_id, title, description, category, status, image_path) VALUES
(2, '考研资料合集', '包含各科复习资料和历年真题，赠送笔记', 'Books', 'available', '/uploads/sample/exam_books.jpg'),
(3, '充电台灯', '可充电LED台灯，适合宿舍使用，8成新', 'Furniture', 'available', '/uploads/sample/desk_lamp.jpg'),
(4, '大一教材全套', '包含高数、英语等基础课程教材，免费赠送', 'Books', 'available', '/uploads/sample/textbooks.jpg'),
(5, '篮球', '适合新手练习用，七成新，送打气筒', 'Sports', 'available', '/uploads/sample/basketball.jpg');

-- Sample donation requests
INSERT INTO donation_requests (requester_id, title, description, category, status) VALUES
(2, '需要计算器', '准备考试用，科学计算器即可，感谢好心人捐赠', 'Electronics', 'active'),
(3, '求冬季校服', 'M码，去年的款式都可以，感谢帮助', 'Clothing', 'active'),
(4, '需要篮球', '用于体育课，普通训练球即可，谢谢爱心人士', 'Sports', 'active'),
(5, '寻求考研资料', '需要英语和数学的复习资料，万分感谢帮助', 'Books', 'active');
