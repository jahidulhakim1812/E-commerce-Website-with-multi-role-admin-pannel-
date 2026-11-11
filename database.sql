-- DryNutri E-commerce Suite - database schema + starter data (MySQL 5.7+/MariaDB 10.3+)
SET NAMES utf8mb4;
CREATE TABLE IF NOT EXISTS settings (skey VARCHAR(100) PRIMARY KEY, sval MEDIUMTEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(160) NOT NULL UNIQUE, phone VARCHAR(30), password VARCHAR(255) NOT NULL, role ENUM('admin','manager','customer') NOT NULL DEFAULT 'customer', perms VARCHAR(255) DEFAULT '', address TEXT, city VARCHAR(80), avatar VARCHAR(255), is_guest TINYINT(1) NOT NULL DEFAULT 0, status TINYINT(1) NOT NULL DEFAULT 1, last_login DATETIME NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS password_reset_requests (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id INT NULL, token_hash CHAR(64) NULL, request_ip VARCHAR(45) NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, expires_at DATETIME NULL, KEY token_hash (token_hash), KEY request_ip_created (request_ip, created_at), KEY user_id (user_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS categories (id INT AUTO_INCREMENT PRIMARY KEY, parent_id INT NOT NULL DEFAULT 0, name_en VARCHAR(120) NOT NULL, name_bn VARCHAR(120), slug VARCHAR(140) NOT NULL UNIQUE, image VARCHAR(255), icon VARCHAR(12), sort_order INT DEFAULT 0, show_home TINYINT(1) DEFAULT 1, status TINYINT(1) DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS products (id INT AUTO_INCREMENT PRIMARY KEY, category_id INT NOT NULL DEFAULT 0, name_en VARCHAR(200) NOT NULL, name_bn VARCHAR(200), slug VARCHAR(220) NOT NULL UNIQUE, short_en VARCHAR(300), short_bn VARCHAR(300), desc_en TEXT, desc_bn TEXT, price DECIMAL(10,2) NOT NULL DEFAULT 0, old_price DECIMAL(10,2) DEFAULT 0, cost_price DECIMAL(10,2) DEFAULT 0, stock INT DEFAULT 0, unit VARCHAR(30), brand VARCHAR(80), flag VARCHAR(20) DEFAULT '', is_featured TINYINT(1) DEFAULT 0, image VARCHAR(255), gallery TEXT, status TINYINT(1) DEFAULT 1, rating DECIMAL(3,2) DEFAULT 0, rating_count INT DEFAULT 0, sold INT DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, KEY(category_id), KEY(status)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS reviews (id INT AUTO_INCREMENT PRIMARY KEY, product_id INT NOT NULL, user_id INT DEFAULT 0, name VARCHAR(120), rating TINYINT DEFAULT 5, body TEXT, status TINYINT(1) DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, KEY(product_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS sliders (id INT AUTO_INCREMENT PRIMARY KEY, title_en VARCHAR(200), title_bn VARCHAR(200), subtitle_en VARCHAR(300), subtitle_bn VARCHAR(300), badge_en VARCHAR(80), badge_bn VARCHAR(80), btn_en VARCHAR(60), btn_bn VARCHAR(60), link VARCHAR(255), btn2_en VARCHAR(60), btn2_bn VARCHAR(60), link2 VARCHAR(255), image_mode VARCHAR(8) DEFAULT 'side', image VARCHAR(255), sort_order INT DEFAULT 0, status TINYINT(1) DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS sections (id INT AUTO_INCREMENT PRIMARY KEY, type VARCHAR(30) NOT NULL, title_en VARCHAR(200), title_bn VARCHAR(200), subtitle_en VARCHAR(300), subtitle_bn VARCHAR(300), source VARCHAR(40) DEFAULT 'featured', limit_n INT DEFAULT 8, layout VARCHAR(10) DEFAULT 'grid', badge_en VARCHAR(100), badge_bn VARCHAR(100), image VARCHAR(255), image2 VARCHAR(255), link VARCHAR(255), btn_en VARCHAR(60), btn_bn VARCHAR(60), sort_order INT DEFAULT 0, status TINYINT(1) DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS features (id INT AUTO_INCREMENT PRIMARY KEY, icon VARCHAR(12), title_en VARCHAR(80), title_bn VARCHAR(80), sub_en VARCHAR(80), sub_bn VARCHAR(80), sort_order INT DEFAULT 0, status TINYINT(1) DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS testimonials (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), body_en TEXT, body_bn TEXT, rating TINYINT DEFAULT 5, image VARCHAR(255), sort_order INT DEFAULT 0, status TINYINT(1) DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS menu_items (id INT AUTO_INCREMENT PRIMARY KEY, location VARCHAR(20) NOT NULL DEFAULT 'header', item_type VARCHAR(20) NOT NULL DEFAULT 'link', ref_id INT DEFAULT 0, label_en VARCHAR(100), label_bn VARCHAR(100), badge VARCHAR(20), url VARCHAR(255), parent_id INT DEFAULT 0, auto_children TINYINT(1) DEFAULT 0, sort_order INT DEFAULT 0, status TINYINT(1) DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS pages (id INT AUTO_INCREMENT PRIMARY KEY, slug VARCHAR(100) NOT NULL UNIQUE, title_en VARCHAR(200), title_bn VARCHAR(200), body_en MEDIUMTEXT, body_bn MEDIUMTEXT, status TINYINT(1) DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS coupons (id INT AUTO_INCREMENT PRIMARY KEY, code VARCHAR(40) NOT NULL UNIQUE, type VARCHAR(10) DEFAULT 'percent', value DECIMAL(10,2) DEFAULT 0, min_order DECIMAL(10,2) DEFAULT 0, max_uses INT DEFAULT 0, used INT DEFAULT 0, expires_at DATE NULL, status TINYINT(1) DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS orders (id INT AUTO_INCREMENT PRIMARY KEY, order_no VARCHAR(30) NOT NULL UNIQUE, user_id INT DEFAULT 0, name VARCHAR(120), phone VARCHAR(30), email VARCHAR(160), address TEXT, city VARCHAR(80), subtotal DECIMAL(10,2) DEFAULT 0, shipping DECIMAL(10,2) DEFAULT 0, discount DECIMAL(10,2) DEFAULT 0, total DECIMAL(10,2) DEFAULT 0, coupon VARCHAR(40), delivery_zone VARCHAR(10), payment_method VARCHAR(30) DEFAULT 'cod', payment_status VARCHAR(10) DEFAULT 'unpaid', status VARCHAR(20) DEFAULT 'pending', note TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, KEY(user_id), KEY(status), KEY(created_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS order_items (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, product_id INT DEFAULT 0, variant_id INT DEFAULT 0, variant VARCHAR(80), name VARCHAR(200), price DECIMAL(10,2), cost DECIMAL(10,2) DEFAULT 0, qty INT DEFAULT 1, image VARCHAR(255), KEY(order_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS order_log (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, status VARCHAR(20), note VARCHAR(255), created_at DATETIME DEFAULT CURRENT_TIMESTAMP, KEY(order_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS visitors (id BIGINT AUTO_INCREMENT PRIMARY KEY, ip VARCHAR(45), page VARCHAR(255), referrer VARCHAR(255), user_agent VARCHAR(255), device VARCHAR(12), visit_date DATE, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, KEY(ip), KEY(visit_date)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS expenses (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(200) NOT NULL, category VARCHAR(60), amount DECIMAL(12,2) NOT NULL DEFAULT 0, expense_date DATE NOT NULL, note VARCHAR(255)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS newsletter (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(160) NOT NULL UNIQUE, token VARCHAR(40), status TINYINT(1) DEFAULT 1, source VARCHAR(20) DEFAULT 'site', created_at DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS contact_messages (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120), email VARCHAR(160), phone VARCHAR(30), message TEXT, is_read TINYINT(1) DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS admin_notification_reads (staff_id INT NOT NULL, item_type VARCHAR(20) NOT NULL, item_id INT NOT NULL, read_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (staff_id, item_type, item_id), KEY (read_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS mail_log (id INT AUTO_INCREMENT PRIMARY KEY, to_email VARCHAR(160), subject VARCHAR(255), status VARCHAR(10), error VARCHAR(255), created_at DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS product_variants (id INT AUTO_INCREMENT PRIMARY KEY, product_id INT NOT NULL, label_en VARCHAR(60) NOT NULL, label_bn VARCHAR(60), grams INT DEFAULT 0, price DECIMAL(10,2) NOT NULL DEFAULT 0, old_price DECIMAL(10,2) DEFAULT 0, cost_price DECIMAL(10,2) DEFAULT 0, stock INT DEFAULT 0, sort_order INT DEFAULT 0, status TINYINT(1) DEFAULT 1, KEY(product_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS checkout_visits (id INT AUTO_INCREMENT PRIMARY KEY, sid VARCHAR(64) NOT NULL, user_id INT DEFAULT 0, name VARCHAR(120), phone VARCHAR(30), email VARCHAR(160), address VARCHAR(500), ip VARCHAR(45), device VARCHAR(12), cart_json TEXT, cart_total DECIMAL(10,2) DEFAULT 0, items_count INT DEFAULT 0, status VARCHAR(12) DEFAULT 'visited', order_id INT DEFAULT 0, contacted TINYINT(1) DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, UNIQUE KEY(sid), KEY(status), KEY(phone)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS login_attempts (ip VARCHAR(45) PRIMARY KEY, tries INT DEFAULT 0, locked_until DATETIME NULL, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS notifications (id INT AUTO_INCREMENT PRIMARY KEY, type VARCHAR(12) DEFAULT 'message', title_en VARCHAR(200), title_bn VARCHAR(200), body_en VARCHAR(500), body_bn VARCHAR(500), link VARCHAR(255), image VARCHAR(255), emailed INT DEFAULT 0, status TINYINT(1) DEFAULT 1, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, KEY(created_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS push_subscriptions (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, endpoint_hash CHAR(64) NOT NULL UNIQUE, endpoint TEXT NOT NULL, public_key VARCHAR(255) NOT NULL, auth_token VARCHAR(255) NOT NULL, content_encoding VARCHAR(12) NOT NULL DEFAULT 'aes128gcm', created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, KEY(updated_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (name,email,phone,password,role,perms) VALUES ('Super Admin','admin@drynutri.com','','$2y$10$FL.nLMxQh/wBVjMFvin0juvWDbFalWbMY.zfJPhYdiNl1fAnyFpf.','admin','');

INSERT INTO settings (skey,sval) VALUES
('site_name','DryNutri'),('tagline_en','Pure Nutrition in Every Bite'),('tagline_bn','প্রতিটি কামড়ে খাঁটি পুষ্টি'),
('topbar_en','Free Shipping on Orders Over ৳1,000|100% Natural Dried Fruits|24/7 Customer Support'),('topbar_bn','৳১,০০০ এর উপরে ফ্রি ডেলিভারি|১০০% প্রাকৃতিক শুকনো ফল|২৪/৭ গ্রাহক সহায়তা'),
('logo',''),('favicon',''),('primary_color','#2f7d32'),('accent_color','#7ed957'),('currency','৳'),
('shipping_fee','80'),('free_shipping_min','1000'),('low_stock','10'),
('phone','+880 1234 567890'),('email','support@drynutri.com'),('admin_email','admin@drynutri.com'),
('address_en','123 Healthy Street, Dhaka, Bangladesh'),('address_bn','১২৩ হেলদি স্ট্রিট, ঢাকা, বাংলাদেশ'),
('about_en','DryNutri brings you premium sun-dried mango, orange, banana and more - naturally sweet, with no added sugar.'),('about_bn','ড্রাইনিউট্রি নিয়ে এসেছে প্রিমিয়াম শুকনো আম, কমলা, কলা ও আরও অনেক ফল - প্রাকৃতিক মিষ্টি, চিনি ছাড়া।'),
('facebook','#'),('youtube','#'),('instagram','#'),('linkedin','#'),('pinterest','#'),
('float_enabled','1'),('float_whatsapp','8801234567890'),('float_phone','+8801234567890'),('float_email','support@drynutri.com'),('float_messenger',''),
('payment_methods','cod,bkash,nagad'),('payment_note_en','Send payment to our bKash/Nagad number and enter the transaction ID in the note.'),('payment_note_bn','আমাদের বিকাশ/নগদ নম্বরে টাকা পাঠিয়ে নোটে ট্রানজেকশন আইডি দিন।'),
('mail_enabled','0'),('smtp_host','smtp.gmail.com'),('smtp_port','587'),('smtp_secure','tls'),('smtp_user',''),('smtp_pass',''),('mail_from',''),('mail_from_name','DryNutri'),
('sms_enabled','0'),('sms_api_url','https://sms.sslwireless.com/pushapi/dynamic/server.php'),('sms_api_user',''),('sms_api_pass',''),('sms_sender_id',''),('order_sms_body','Hello {name}, we received order {order_no} ({status}). Total: {total}. Thank you for shopping with {site}.'),
('push_enabled','0'),('push_public_key',''),('push_private_key',''),
('order_mail_subject','Your DryNutri order {order_no} is {status}'),
('order_mail_body','Hello {name},<br><br>Thank you for shopping with {site}! Your order <b>{order_no}</b> is now <b>{status}</b>.<br><br>{items}<br><b>Total: {total}</b><br><br>Track your order anytime: <a href="{track_url}">{track_url}</a><br><br>With care,<br>{site} Team'),
('copyright','© DryNutri. All rights reserved. | Live Healthy. Live Natural.'),
('whatsapp_order','1'),('visitor_tracking','1'),('notify_products','1'),('notify_offers','1'),('notify_email_auto','0'),('logo_dark',''),('shipping_inside','60'),('shipping_outside','120'),('logo_tagline','1');

INSERT INTO pages (slug,title_en,title_bn,body_en,body_bn) VALUES
('about','About Us','আমাদের সম্পর্কে','<p>DryNutri is a healthy-food brand delivering premium nuts, dried fruits, seeds and superfoods across Bangladesh. Edit this page from Admin → Pages.</p>','<p>ড্রাইনিউট্রি একটি স্বাস্থ্যকর খাদ্য ব্র্যান্ড। অ্যাডমিন প্যানেল থেকে এই পেজ এডিট করুন।</p>'),
('return-policy','Return Policy','রিটার্ন পলিসি','<p>If you receive a damaged or incorrect item, contact us within 48 hours for a replacement or refund.</p>','<p>ক্ষতিগ্রস্ত বা ভুল পণ্য পেলে ৪৮ ঘণ্টার মধ্যে যোগাযোগ করুন।</p>'),
('shipping','Shipping Info','শিপিং তথ্য','<p>We deliver all over Bangladesh. Dhaka: 1-2 days, outside Dhaka: 2-4 days.</p>','<p>সারা বাংলাদেশে ডেলিভারি। ঢাকায় ১-২ দিন, ঢাকার বাইরে ২-৪ দিন।</p>'),
('faq','FAQ','সাধারণ জিজ্ঞাসা','<p><b>How can I pay?</b> Cash on delivery, bKash and Nagad.</p>','<p><b>কীভাবে পেমেন্ট করব?</b> ক্যাশ অন ডেলিভারি, বিকাশ ও নগদ।</p>'),
('terms','Terms & Conditions','শর্তাবলী','<p>By using this website you agree to our terms. Edit from Admin → Pages.</p>','<p>এই ওয়েবসাইট ব্যবহার করে আপনি আমাদের শর্তাবলীতে সম্মত হচ্ছেন।</p>'),
('privacy','Privacy Policy','গোপনীয়তা নীতি','<p>We respect your privacy and never sell your personal data.</p>','<p>আমরা আপনার গোপনীয়তাকে সম্মান করি।</p>');

INSERT INTO coupons (code,type,value,min_order,expires_at) VALUES ('WELCOME10','percent',10,500,NULL);

INSERT INTO expenses (title,category,amount,expense_date,note) VALUES ('Website hosting','Technology',3000,CURDATE(),'Sample expense - delete anytime');

INSERT INTO notifications (type,title_en,title_bn,body_en,body_bn,link,image) VALUES
('offer','Welcome to DryNutri! 🥭','ড্রাইনিউট্রিতে স্বাগতম! 🥭','Use code WELCOME10 for 10% off your first order.','প্রথম অর্ডারে ১০% ছাড় পেতে কোড WELCOME10 ব্যবহার করুন।','shop.php','');

INSERT INTO notifications (type,title_en,title_bn,body_en,body_bn,link,image) VALUES
('offer','Welcome to DryNutri!','ড্রাইনিউট্রিতে স্বাগতম!','Use code WELCOME10 for 10% off your first order.','প্রথম অর্ডারে ১০% ছাড় পেতে কোড WELCOME10 ব্যবহার করুন।','shop.php','');

INSERT INTO categories (id,parent_id,name_en,name_bn,slug,icon,sort_order,show_home) VALUES
(1,0,'Dried Mango','শুকনো আম','dried-mango','🥭',1,1),
(2,0,'Dried Orange','শুকনো কমলা','dried-orange','🍊',2,1),
(3,0,'Dried Banana','শুকনো কলা','dried-banana','🍌',3,1),
(4,0,'Dried Berries','শুকনো বেরি','dried-berries','🍓',4,1),
(5,0,'Pineapple & Tropical','আনারস ও ট্রপিক্যাল','pineapple-tropical','🍍',5,1),
(6,0,'Dates & Figs','খেজুর ও ডুমুর','dates-figs','🌴',6,1),
(7,0,'Nuts & Seeds','বাদাম ও বীজ','nuts-seeds','🥜',7,1),
(8,0,'Mixed Dried Fruit','মিক্সড শুকনো ফল','mixed-dried-fruit','🥣',8,1),
(9,1,'Mango Slices','ম্যাঙ্গো স্লাইস','mango-slices','🥭',1,0),
(10,1,'Mango Bars','ম্যাঙ্গো বার','mango-bars','🥭',2,0),
(11,2,'Orange Slices','কমলার স্লাইস','orange-slices','🍊',1,0),
(12,3,'Banana Chips','কলার চিপস','banana-chips','🍌',1,0),
(13,7,'Cashew Nuts','কাজুবাদাম','cashew-nuts','🥜',1,0),
(14,7,'Almonds','কাঠবাদাম','almonds','🌰',2,0);

INSERT INTO products (category_id,name_en,name_bn,slug,short_en,short_bn,desc_en,desc_bn,price,old_price,cost_price,stock,unit,brand,flag,is_featured,rating,rating_count,sold) VALUES
(9,'Dried Mango Slices 250g','শুকনো আমের স্লাইস ২৫০গ্রাম','dried-mango-slices-250g','Golden, chewy and naturally sweet mango.','সোনালি, নরম ও প্রাকৃতিক মিষ্টি আম।','Golden, chewy and naturally sweet mango. Naturally dried, no preservatives, packed fresh.','সোনালি, নরম ও প্রাকৃতিক মিষ্টি আম। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',105,125,73,150,'50 g','DryNutri','best',1,4.9,42,320),
(10,'Dried Mango Bar 200g','ম্যাঙ্গো বার ২০০গ্রাম','dried-mango-bar-200g','Pure mango pulp bar - a sweet healthy snack.','খাঁটি আমের পাল্পে তৈরি মিষ্টি স্ন্যাক।','Pure mango pulp bar - a sweet healthy snack. Naturally dried, no preservatives, packed fresh.','খাঁটি আমের পাল্পে তৈরি মিষ্টি স্ন্যাক। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',80,95,53,120,'40 g','DryNutri','new',0,4.7,18,110),
(11,'Dried Orange Slices 150g','শুকনো কমলার স্লাইস ১৫০গ্রাম','dried-orange-slices-150g','Tangy citrus slices full of vitamin C.','ভিটামিন সি সমৃদ্ধ টক-মিষ্টি কমলার স্লাইস।','Tangy citrus slices full of vitamin C. Naturally dried, no preservatives, packed fresh.','ভিটামিন সি সমৃদ্ধ টক-মিষ্টি কমলার স্লাইস। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',90,105,59,99,'30 g','DryNutri','new',1,4.8,25,140),
(12,'Dried Banana Chips 250g','শুকনো কলার চিপস ২৫০গ্রাম','dried-banana-chips-250g','Crunchy banana chips, no added sugar.','মচমচে কলার চিপস, চিনি ছাড়া।','Crunchy banana chips, no added sugar. Naturally dried, no preservatives, packed fresh.','মচমচে কলার চিপস, চিনি ছাড়া। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',80,90,50,180,'50 g','DryNutri','best',1,4.8,36,290),
(4,'Dried Strawberry 100g','শুকনো স্ট্রবেরি ১০০গ্রাম','dried-strawberry-100g','Sweet-tart strawberries, freeze-dried crisp.','মিষ্টি-টক ক্রিস্পি স্ট্রবেরি।','Sweet-tart strawberries, freeze-dried crisp. Naturally dried, no preservatives, packed fresh.','মিষ্টি-টক ক্রিস্পি স্ট্রবেরি। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',125,145,87,69,'20 g','DryNutri','new',1,4.7,15,95),
(4,'Dried Blueberry 150g','শুকনো ব্লুবেরি ১৫০গ্রাম','dried-blueberry-150g','Antioxidant-rich blueberries.','অ্যান্টিঅক্সিডেন্ট সমৃদ্ধ ব্লুবেরি।','Antioxidant-rich blueberries. Naturally dried, no preservatives, packed fresh.','অ্যান্টিঅক্সিডেন্ট সমৃদ্ধ ব্লুবেরি। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',155,180,109,60,'30 g','DryNutri','organic',1,4.8,21,80),
(5,'Dried Pineapple Rings 250g','শুকনো আনারস রিং ২৫০গ্রাম','dried-pineapple-rings-250g','Tropical pineapple rings, naturally sweet.','প্রাকৃতিক মিষ্টি ট্রপিক্যাল আনারস রিং।','Tropical pineapple rings, naturally sweet. Naturally dried, no preservatives, packed fresh.','প্রাকৃতিক মিষ্টি ট্রপিক্যাল আনারস রিং। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',120,135,81,108,'50 g','DryNutri','best',1,4.8,28,200),
(5,'Dried Papaya Cubes 250g','শুকনো পেঁপে কিউব ২৫০গ্রাম','dried-papaya-cubes-250g','Soft papaya cubes, great for digestion.','হজমে সহায়ক নরম পেঁপে কিউব।','Soft papaya cubes, great for digestion. Naturally dried, no preservatives, packed fresh.','হজমে সহায়ক নরম পেঁপে কিউব। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',100,120,67,90,'50 g','DryNutri','',1,4.6,12,85),
(5,'Dried Kiwi Slices 150g','শুকনো কিউই স্লাইস ১৫০গ্রাম','dried-kiwi-slices-150g','Tangy green kiwi slices.','টক-মিষ্টি সবুজ কিউই স্লাইস।','Tangy green kiwi slices. Naturally dried, no preservatives, packed fresh.','টক-মিষ্টি সবুজ কিউই স্লাইস। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',110,125,76,63,'30 g','DryNutri','new',0,4.6,9,60),
(6,'Premium Dates 1kg','প্রিমিয়াম খেজুর ১কেজি','premium-dates-1kg','Soft, juicy and naturally sweet dates.','নরম, রসালো ও প্রাকৃতিক মিষ্টি খেজুর।','Soft, juicy and naturally sweet dates. Naturally dried, no preservatives, packed fresh.','নরম, রসালো ও প্রাকৃতিক মিষ্টি খেজুর। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',125,155,90,198,'200 g','DryNutri','best',1,4.9,31,310),
(6,'Dried Figs (Anjeer) 250g','শুকনো ডুমুর (আঞ্জির) ২৫০গ্রাম','dried-figs-anjeer-250g','Honey-sweet figs rich in fibre.','ফাইবার সমৃদ্ধ মধুর মতো মিষ্টি ডুমুর।','Honey-sweet figs rich in fibre. Naturally dried, no preservatives, packed fresh.','ফাইবার সমৃদ্ধ মধুর মতো মিষ্টি ডুমুর। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',145,170,101,78,'50 g','DryNutri','organic',0,4.7,14,70),
(8,'Organic Raisins 500g','অর্গানিক কিশমিশ ৫০০গ্রাম','organic-raisins-500g','Seedless organic raisins.','বীজহীন অর্গানিক কিশমিশ।','Seedless organic raisins. Naturally dried, no preservatives, packed fresh.','বীজহীন অর্গানিক কিশমিশ। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',120,135,84,24,'100 g','DryNutri','organic',0,4.4,5,40),
(8,'Mixed Dried Fruit Pack 400g','মিক্সড শুকনো ফল ৪০০গ্রাম','mixed-dried-fruit-400g','Mango, orange, banana, pineapple & more.','আম, কমলা, কলা, আনারসসহ আরও অনেক ফল।','Mango, orange, banana, pineapple & more. Naturally dried, no preservatives, packed fresh.','আম, কমলা, কলা, আনারসসহ আরও অনেক ফল। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',155,180,112,90,'80 g','DryNutri','best',1,4.9,33,260),
(8,'Dried Apricot 250g','শুকনো এপ্রিকট ২৫০গ্রাম','dried-apricot-250g','Sun-dried apricots with no added sugar.','চিনি ছাড়া রোদে শুকানো এপ্রিকট।','Sun-dried apricots with no added sugar. Naturally dried, no preservatives, packed fresh.','চিনি ছাড়া রোদে শুকানো এপ্রিকট। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',105,125,76,78,'50 g','DryNutri','',0,4.5,6,50),
(13,'Premium Cashew Nuts 500g','প্রিমিয়াম কাজুবাদাম ৫০০গ্রাম','premium-cashew-nuts-500g','Crunchy, creamy and naturally sweet.','মচমচে, ক্রিমি ও প্রাকৃতিক মিষ্টি।','Crunchy, creamy and naturally sweet. Naturally dried, no preservatives, packed fresh.','মচমচে, ক্রিমি ও প্রাকৃতিক মিষ্টি। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',180,210,134,120,'100 g','DryNutri','best',1,4.8,25,250),
(14,'Premium Almonds 500g','প্রিমিয়াম কাঠবাদাম ৫০০গ্রাম','premium-almonds-500g','Fresh almonds rich in vitamin E.','ভিটামিন ই সমৃদ্ধ তাজা কাঠবাদাম।','Fresh almonds rich in vitamin E. Naturally dried, no preservatives, packed fresh.','ভিটামিন ই সমৃদ্ধ তাজা কাঠবাদাম। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',175,195,126,90,'100 g','DryNutri','',1,4.8,18,180),
(4,'Dried Cranberries 200g','শুকনো ক্র্যানবেরি ২০০গ্রাম','dried-cranberries-200g','Ruby-red cranberries for snacking and baking.','স্ন্যাক ও বেকিংয়ের জন্য লাল ক্র্যানবেরি।','Ruby-red cranberries for snacking and baking. Naturally dried, no preservatives, packed fresh.','স্ন্যাক ও বেকিংয়ের জন্য লাল ক্র্যানবেরি। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',95,110,64,75,'40 g','DryNutri','',0,4.5,8,55),
(8,'Tropical Trail Mix 300g','ট্রপিক্যাল ট্রেইল মিক্স ৩০০গ্রাম','tropical-trail-mix-300g','Dried fruit, nuts and seeds energy mix.','শুকনো ফল, বাদাম ও বীজের এনার্জি মিক্স।','Dried fruit, nuts and seeds energy mix. Naturally dried, no preservatives, packed fresh.','শুকনো ফল, বাদাম ও বীজের এনার্জি মিক্স। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।',145,170,101,54,'60 g','DryNutri','best',0,4.8,16,130);

INSERT INTO product_variants (product_id,label_en,label_bn,grams,price,old_price,cost_price,stock,sort_order) VALUES
(1,'250 g','২৫০ গ্রাম',250,380,450,260,50,1),
(1,'125 g','১২৫ গ্রাম',125,210,250,143,50,2),
(1,'50 g','৫০ গ্রাম',50,105,125,73,50,3),
(2,'200 g','২০০ গ্রাম',200,290,340,190,40,1),
(2,'100 g','১০০ গ্রাম',100,160,185,105,40,2),
(2,'40 g','৪০ গ্রাম',40,80,95,53,40,3),
(3,'150 g','১৫০ গ্রাম',150,320,380,210,33,1),
(3,'75 g','৭৫ গ্রাম',75,175,210,116,33,2),
(3,'30 g','৩০ গ্রাম',30,90,105,59,33,3),
(4,'250 g','২৫০ গ্রাম',250,280,330,180,60,1),
(4,'125 g','১২৫ গ্রাম',125,155,180,99,60,2),
(4,'50 g','৫০ গ্রাম',50,80,90,50,60,3),
(5,'100 g','১০০ গ্রাম',100,450,520,310,23,1),
(5,'50 g','৫০ গ্রাম',50,250,285,170,23,2),
(5,'20 g','২০ গ্রাম',20,125,145,87,23,3),
(6,'150 g','১৫০ গ্রাম',150,560,650,390,20,1),
(6,'75 g','৭৫ গ্রাম',75,310,360,215,20,2),
(6,'30 g','৩০ গ্রাম',30,155,180,109,20,3),
(7,'250 g','২৫০ গ্রাম',250,420,480,290,36,1),
(7,'125 g','১২৫ গ্রাম',125,230,265,160,36,2),
(7,'50 g','৫০ গ্রাম',50,120,135,81,36,3),
(8,'250 g','২৫০ গ্রাম',250,360,420,240,30,1),
(8,'125 g','১২৫ গ্রাম',125,200,230,132,30,2),
(8,'50 g','৫০ গ্রাম',50,100,120,67,30,3),
(9,'150 g','১৫০ গ্রাম',150,390,450,270,21,1),
(9,'75 g','৭৫ গ্রাম',75,215,250,148,21,2),
(9,'30 g','৩০ গ্রাম',30,110,125,76,21,3),
(10,'1 kg','১ কেজি',1000,450,550,320,66,1),
(10,'500 g','৫০০ গ্রাম',500,250,300,176,66,2),
(10,'200 g','২০০ গ্রাম',200,125,155,90,66,3),
(11,'250 g','২৫০ গ্রাম',250,520,600,360,26,1),
(11,'125 g','১২৫ গ্রাম',125,285,330,198,26,2),
(11,'50 g','৫০ গ্রাম',50,145,170,101,26,3),
(12,'500 g','৫০০ গ্রাম',500,420,480,300,8,1),
(12,'250 g','২৫০ গ্রাম',250,230,265,165,8,2),
(12,'100 g','১০০ গ্রাম',100,120,135,84,8,3),
(13,'400 g','৪০০ গ্রাম',400,560,650,400,30,1),
(13,'200 g','২০০ গ্রাম',200,310,360,220,30,2),
(13,'80 g','৮০ গ্রাম',80,155,180,112,30,3),
(14,'250 g','২৫০ গ্রাম',250,380,450,270,26,1),
(14,'125 g','১২৫ গ্রাম',125,210,250,148,26,2),
(14,'50 g','৫০ গ্রাম',50,105,125,76,26,3),
(15,'500 g','৫০০ গ্রাম',500,650,750,480,40,1),
(15,'250 g','২৫০ গ্রাম',250,360,415,264,40,2),
(15,'100 g','১০০ গ্রাম',100,180,210,134,40,3),
(16,'500 g','৫০০ গ্রাম',500,620,700,450,30,1),
(16,'250 g','২৫০ গ্রাম',250,340,385,248,30,2),
(16,'100 g','১০০ গ্রাম',100,175,195,126,30,3),
(17,'200 g','২০০ গ্রাম',200,340,400,230,25,1),
(17,'100 g','১০০ গ্রাম',100,185,220,127,25,2),
(17,'40 g','৪০ গ্রাম',40,95,110,64,25,3),
(18,'300 g','৩০০ গ্রাম',300,520,600,360,18,1),
(18,'150 g','১৫০ গ্রাম',150,285,330,198,18,2),
(18,'60 g','৬০ গ্রাম',60,145,170,101,18,3);

INSERT INTO sliders (title_en,title_bn,subtitle_en,subtitle_bn,badge_en,badge_bn,btn_en,btn_bn,link,btn2_en,btn2_bn,link2,sort_order) VALUES
('Taste the Sunshine.
Naturally Dried.','সূর্যের স্বাদ নিন।
প্রাকৃতিকভাবে শুকানো।','Premium dried mango, orange, banana and more - no added sugar, 100% natural.','প্রিমিয়াম শুকনো আম, কমলা, কলা ও আরও অনেক কিছু - চিনি ছাড়া, ১০০% প্রাকৃতিক।','Sun-Dried Goodness in Every Bite','প্রতিটি কামড়ে রোদে শুকানো স্বাদ','Shop Now','এখনই কিনুন','shop.php','Explore More','আরও দেখুন','shop.php?featured=1',1),
('Mango, Mango,
Mango!','আম, আম,
আর আম!','Juicy golden slices dried to perfection - sweet, chewy and irresistible.','রসালো সোনালি স্লাইস নিখুঁতভাবে শুকানো - মিষ্টি, নরম আর অপ্রতিরোধ্য।','Our Best Seller','আমাদের সেরা বিক্রিত','Shop Mango','আম কিনুন','shop.php?cat=dried-mango','All Products','সব পণ্য','shop.php',2),
('Citrus & Tropical
Delights','সাইট্রাস ও
ট্রপিক্যাল স্বাদ','Orange, pineapple, kiwi and papaya - a sunny taste of the tropics.','কমলা, আনারস, কিউই ও পেঁপে - ট্রপিকসের রোদেলা স্বাদ।','New Arrivals','নতুন আগমন','Shop Tropical','ট্রপিক্যাল কিনুন','shop.php?cat=pineapple-tropical','Orange Slices','কমলার স্লাইস','shop.php?cat=dried-orange',3);

INSERT INTO sections (type,title_en,title_bn,subtitle_en,subtitle_bn,source,limit_n,layout,badge_en,badge_bn,image,link,btn_en,btn_bn,sort_order) VALUES
('hero','Hero Slider','হিরো স্লাইডার','','','',0,'grid','','','','','','',1),
('categories','Shop by Category','ক্যাটাগরি অনুযায়ী কিনুন','Choose from our wide range of dried fruits','আমাদের শুকনো ফলের বিশাল সংগ্রহ থেকে বেছে নিন','',12,'slider','','','','shop.php','','',2),
('products','Fan Favorites','ভক্তদের পছন্দ','Loved by thousands of happy customers','হাজারো খুশি গ্রাহকের প্রিয়','featured',8,'grid','','','','shop.php?featured=1','View All','সব দেখুন',3),
('banner','Healthy Choices for a Healthier Life','সুস্থ জীবনের জন্য স্বাস্থ্যকর পছন্দ','Get up to 30% OFF on selected products!','বাছাই করা পণ্যে ৩০% পর্যন্ত ছাড় পান!','',0,'grid','UP TO|30%|OFF','ছাড়|৩০%|পর্যন্ত','','shop.php?offer=1','Shop the Offer','অফারটি দেখুন',4),
('products','Best Selling Products','বেস্ট সেলিং পণ্য','Customer favourites you will love','গ্রাহকদের সবচেয়ে প্রিয় পণ্য','best',8,'slider','','','','shop.php?flag=best','View All','সব দেখুন',5),
('products','Recent Uploads','সদ্য যোগ হওয়া','Fresh arrivals just added to our store','আমাদের দোকানে নতুন যোগ হয়েছে','recent',4,'grid','','','','shop.php?sort=new','View All','সব দেখুন',6),
('products','All Products','সকল পণ্য','Fresh, natural and healthy','তাজা, প্রাকৃতিক ও স্বাস্থ্যকর','all',8,'grid','','','','shop.php','View All Products','সব পণ্য দেখুন',7),
('features','Why Choose DryNutri?','কেন ড্রাইনিউট্রি বেছে নেবেন?','','','',0,'grid','','','','','','',8),
('testimonials','What Our Customers Say','আমাদের গ্রাহকরা কী বলেন','','','',6,'slider','','','','','','',9),
('newsletter','Subscribe to our Newsletter','আমাদের নিউজলেটারে সাবস্ক্রাইব করুন','Get new arrivals, offers and health tips - right in your inbox and on the site.','নতুন পণ্য, অফার ও স্বাস্থ্য টিপস পান - ইনবক্সে ও সাইটে।','',0,'grid','','','','','Subscribe','সাবস্ক্রাইব',10);

INSERT INTO features (icon,title_en,title_bn,sub_en,sub_bn,sort_order) VALUES
('sprout','No Added Sugar','চিনি ছাড়া','100% Natural','১০০% প্রাকৃতিক',1),
('award','Premium Quality','প্রিমিয়াম মান','Hand-picked Fruit','হাতে বাছাই ফল',2),
('shield','Safe & Hygienic','নিরাপদ ও স্বাস্থ্যসম্মত','Sealed Packs','সিল করা প্যাক',3),
('truck','Fast & Reliable','দ্রুত ও নির্ভরযোগ্য','Delivery','ডেলিভারি',4),
('headphones','24/7 Customer','২৪/৭ গ্রাহক','Support','সহায়তা',5);

INSERT INTO testimonials (name,body_en,body_bn,rating,sort_order) VALUES
('Sabrina Rahman','The dried mango is unbelievably good - sweet, soft and no added sugar!','শুকনো আমটা অসাধারণ - মিষ্টি, নরম আর চিনি ছাড়া!',5,1),
('Ahmed Karim','Fresh, natural and healthy. Fast delivery too. Highly recommended!','তাজা, প্রাকৃতিক ও স্বাস্থ্যকর। ডেলিভারিও দ্রুত। অত্যন্ত সুপারিশযোগ্য!',5,2),
('Nusrat Jahan','My kids love the banana chips and orange slices. Great service!','আমার বাচ্চারা কলার চিপস আর কমলার স্লাইস খুব পছন্দ করে। দারুণ সার্ভিস!',5,3),
('Rakib Hasan','Excellent quality and super fast delivery! DryNutri is now my favorite store.','চমৎকার মান এবং অসম্ভব দ্রুত ডেলিভারি! ড্রাইনিউট্রি এখন আমার প্রিয় দোকান।',5,4),
('Sumaiya Akter','The mixed fruit pack is perfect for gifting.','মিক্সড ফ্রুট প্যাকটা উপহারের জন্য একদম পারফেক্ট।',5,5),
('Tariq Rahman','Great products and amazing service.','দারুণ পণ্য এবং অসাধারণ সেবা।',5,6);

INSERT INTO menu_items (location,item_type,ref_id,label_en,label_bn,badge,url,parent_id,auto_children,sort_order) VALUES
('header','link',0,'Home','হোম','','index.php',0,0,1),
('header','link',0,'All Products','সকল পণ্য','','shop.php',0,0,2),
('header','category',1,'Dried Mango','','','',0,1,3),
('header','category',2,'Dried Orange','','','',0,1,4),
('header','category',3,'Dried Banana','','','',0,1,5),
('header','category',6,'Dates & Figs','','','',0,0,6),
('header','link',0,'Offers','অফার','Hot','shop.php?offer=1',0,0,7),
('header','link',0,'About Us','আমাদের সম্পর্কে','','page.php?slug=about',0,0,8),
('footer_quick','link',0,'Home','হোম','','index.php',0,0,1),
('footer_quick','link',0,'All Products','সকল পণ্য','','shop.php',0,0,2),
('footer_quick','link',0,'Offers','অফার','','shop.php?offer=1',0,0,3),
('footer_quick','link',0,'About Us','আমাদের সম্পর্কে','','page.php?slug=about',0,0,4),
('footer_quick','link',0,'Contact','যোগাযোগ','','contact.php',0,0,5),
('footer_care','link',0,'Track Order','অর্ডার ট্র্যাক','','track.php',0,0,1),
('footer_care','link',0,'Return Policy','রিটার্ন পলিসি','','page.php?slug=return-policy',0,0,2),
('footer_care','link',0,'Shipping Info','শিপিং তথ্য','','page.php?slug=shipping',0,0,3),
('footer_care','link',0,'FAQ','সাধারণ জিজ্ঞাসা','','page.php?slug=faq',0,0,4),
('footer_care','link',0,'Terms & Conditions','শর্তাবলী','','page.php?slug=terms',0,0,5);

UPDATE menu_items SET badge=NULL WHERE badge='';

INSERT INTO notifications (type,title_en,title_bn,body_en,body_bn,link,image) VALUES
('offer','Welcome to DryNutri!','ড্রাইনিউট্রিতে স্বাগতম!','Use code WELCOME10 for 10% off your first order.','প্রথম অর্ডারে ১০% ছাড় পেতে কোড WELCOME10 ব্যবহার করুন।','shop.php','');
