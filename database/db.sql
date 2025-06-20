CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','author') NOT NULL DEFAULT 'author',
  `status` enum('active','inactive','pending') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO users (id,username,email,password_hash,`role`,status,last_login,created_at,updated_at) VALUES
	 (20,'zefashofihatul@yopmail.com','zefashofihatul@yopmail.com','$2y$10$KAckczLa7cUhPBDDiKIelunH7fyTgDtDGzS9GG95kZFHqzdc82le2','superadmin','active','2025-06-20 12:03:21','2025-06-12 11:12:37','2025-06-20 12:03:21'),
	 (21,'userauthor1','userauthor1@yopmail.com','$2y$10$6E2NC.d5e8VsrCuYYPFGGePU9fIBkkrxokIOmW2teZwvO5PNS.g9K','author','active','2025-06-20 13:08:09','2025-06-12 11:38:34','2025-06-20 13:08:09'),
	 (22,'user2','user2@yopmail.com','$2y$10$xhqjGK4Qu088lyBpi6t5mO9SSP3d4ixEeX0x57fo/hTww.NIaKy7K','superadmin','active','2025-06-12 17:33:45','2025-06-12 17:32:54','2025-06-12 17:33:45');

CREATE TABLE `profiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `bio` text,
  `avatar_url` varchar(255) DEFAULT NULL,
  `social_links` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO profiles (user_id,full_name,bio,avatar_url,social_links,created_at,updated_at) VALUES
	 (21,'User Author','My name is User Author','uploads/avatars/avatar_21_1750387737.jpg','[]','2025-06-20 09:48:54','2025-06-20 09:48:57'),
	 (20,'Zefa Shofihatul','My name is Zefa Shofihatul','uploads/avatars/avatar_21_1750387737.jpg','[]','2025-06-20 09:51:01','2025-06-20 09:51:01');


CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


INSERT INTO categories (id,name,slug,description,created_at,updated_at) VALUES
	 (1,'Technology','technology',NULL,'2025-05-23 07:43:57','2025-05-23 07:43:57'),
	 (2,'Design','design',NULL,'2025-05-23 07:43:57','2025-05-23 07:43:57'),
	 (3,'Business','business',NULL,'2025-05-23 07:43:57','2025-05-23 07:43:57'),
	 (4,'Development','development',NULL,'2025-05-23 07:56:52','2025-05-23 07:56:52'),
	 (5,'Marketing','marketing',NULL,'2025-05-23 07:56:52','2025-05-23 07:56:52');

CREATE TABLE `editor_contents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `excerpt` text,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `word_count` int DEFAULT '0',
  `author_id` int DEFAULT '1',
  `published` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO editor_contents (id,title,slug,content,excerpt,featured_image,status,created_at,updated_at,word_count,author_id,published) VALUES
	 (52,'Pembuatan Blog Author',NULL,'It is a long established fact that a reader will be distracted by the 
readable content of a page when looking at its layout. The point of 
using Lorem Ipsum is that it has a more-or-less normal distribution of 
letters, as opposed to using ''Content here, content here'', making it 
look like readable English. Many desktop publishing packages and web 
page editors now use Lorem Ipsum as their default model text, and a 
search for ''lorem ipsum'' will uncover many web sites still in their 
infancy. Various versions have evolved over the years, sometimes by 
accident, sometimes on purpose (injected humour and the like).<h2>Heading Blog Kesekian</h2><p>It is a long established fact that a reader will be distracted by the 
readable content of a page when looking at its layout. The point of 
using Lorem Ipsum is that it has a more-or-less normal distribution of 
letters, as opposed to using ''Content here, content here'', making it 
look like readable English. Many desktop publishing packages and web 
page editors now use Lorem Ipsum as their default model text, and a 
search for ''lorem ipsum'' will uncover many web sites still in their 
infancy</p>','It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem ...','php/uploads/covers/684a5a827201b.jpg','published','2025-06-12 11:41:38','2025-06-12 11:41:38',191,21,1),
	 (53,'Blog Admin First',NULL,'It is a long established fact that a reader will be distracted by the 
readable content of a page when looking at its layout. The point of 
using Lorem Ipsum is that it has a more-or-less normal distribution of 
letters, as opposed to using ''Content here, content here'', making it 
look like readable English. Many desktop publishing packages and web 
page editors now use Lorem Ipsum as their default model text, and a 
search for ''lorem ipsum'' will uncover many web sites still in their 
infancy. Various versions have evolved over the years, sometimes by 
accident, sometimes on purpose (injected humour and the like).<h2>Heading Blog Admin First</h2><p>Many desktop publishing packages and web 
page editors now use Lorem Ipsum as their default model text, and a 
search for ''lorem ipsum'' will uncover many web sites still in their 
infancy. Various versions have evolved over the years, sometimes by 
accident, sometimes on purpose (injected humour and the like).</p>','It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem ...','php/uploads/covers/684a6938544a1.jpg','published','2025-06-12 12:44:24','2025-06-12 12:44:24',156,20,1),
	 (54,'Author Blog Second',NULL,'It is a long established fact that a reader will be distracted by the 
readable content of a page when looking at its layout. The point of 
using Lorem Ipsum is that it has a more-or-less normal distribution of 
letters, as opposed to using ''Content here, content here'', making it 
look like readable English. Many desktop publishing packages and web 
page editors now use Lorem Ipsum as their default model text, and a 
search for ''lorem ipsum'' will uncover many web sites still in their 
infancy. Various versions have evolved over the years, sometimes by 
accident, sometimes on purpose (injected humour and the like).<h2>Heading 2</h2><p>It is a long established fact that a reader will be distracted by the 
readable content of a page when looking at its layout. The point of 
using Lorem Ipsum is that it has a more-or-less normal distribution of 
letters, as opposed to using ''Content here, content here'', making it 
look like readable English. Many desktop publishing packages and web 
page editors now use Lorem Ipsum as their default model text, and a 
search for ''lorem ipsum'' will uncover many web sites still in their 
infancy. Various versions have evolved over the years, sometimes by 
accident, sometimes on purpose (injected humour and the like).</p>','It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem ...','php/uploads/covers/684a6ab5ab172.jpg','published','2025-06-12 12:50:45','2025-06-12 12:50:45',208,20,1),
	 (55,'Admin Third Blog',NULL,'It is a long established fact that a reader will be distracted by the 
readable content of a page when looking at its layout. The point of 
using Lorem Ipsum is that it has a more-or-less normal distribution of 
letters, as opposed to using ''Content here, content here'', making it 
look like readable English. Many desktop publishing packages and web 
page editors now use Lorem Ipsum as their default model text, and a 
search for ''lorem ipsum'' will uncover many web sites still in their 
infancy. Various versions have evolved over the years, sometimes by 
accident, sometimes on purpose (injected humour and the like).','It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem ...','php/uploads/covers/684a6ad6d3dd1.jpg','published','2025-06-12 12:51:18','2025-06-12 12:51:18',104,20,1);


CREATE TABLE `content_categories` (
  `content_id` int NOT NULL,
  `category_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`content_id`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `content_categories_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `editor_contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `content_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO content_categories (content_id,category_id,created_at) VALUES
	 (52,3,'2025-06-12 11:41:38'),
	 (53,4,'2025-06-12 12:44:24'),
	 (54,1,'2025-06-12 12:50:45'),
	 (55,5,'2025-06-12 12:51:18');
