-- /docker/mariadb/init.sql
-- USE lod;

-- Création de la table users
-- CREATE TABLE IF NOT EXISTS users (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     name VARCHAR(255) NOT NULL,
--     email VARCHAR(255) NOT NULL UNIQUE,
--     password VARCHAR(255) NOT NULL,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création d'un utilisateur de test
-- Email: nicso@nicso.fr
-- Mot de passe: pass
-- INSERT INTO users (name, email, password) 
-- VALUES (
--     'nicso', 
--     'nicso@nicso.fr', 
--     '$2y$10$8xlm.ZTxIZ40o5q0Tedap.bTVdepSXtOybWverNBdJcFgvKfBTl7u'
-- ) ON DUPLICATE KEY UPDATE name = name;



---------------------------------------------------------------------------------


CREATE DATABASE if NOT EXISTS lod;
USE lod;

-- users
CREATE TABLE if NOT EXISTS `current_role` (
	id INT PRIMARY KEY AUTO_INCREMENT,
	role_name VARCHAR(20) NOT NULL
);

CREATE TABLE if NOT EXISTS `user`(
	id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
	userName VARCHAR(64) NOT NULL UNIQUE,
	firstName VARCHAR(64),
	lastName VARCHAR(64),
	profile_picture TEXT,
	email VARCHAR(100) NOT NULL UNIQUE,
	country VARCHAR(100),
	town VARCHAR(100),
	title VARCHAR(64),
	bio TEXT,
	timezone VARCHAR(64),
	join_date TIMESTAMP NOT NULL,
	passwordHash VARCHAR(255) NOT NULL,
	id_role INT DEFAULT 1,
	INDEX idx_username (username),
	FOREIGN KEY (id_role) REFERENCES `current_role`(id)
);

-- Création des rôles
INSERT INTO `current_role` (role_name) VALUES ('ROLE_USER');
INSERT INTO `current_role` (role_name) VALUES ('ROLE_ADMIN');

-- Création d'un utilisateur de test
-- Email: nicso@nicso.fr
-- Mot de passe: pass
INSERT INTO user (userName, email, join_date, passwordHash, profile_picture, id_role) 
VALUES (
    'nicso', 
    'nicso@nicso.fr', 
    '2023-01-01 00:00:00',
    '$2y$10$8xlm.ZTxIZ40o5q0Tedap.bTVdepSXtOybWverNBdJcFgvKfBTl7u',
    'https://avatars.githubusercontent.com/u/3064303?v=4',
    2
) ON DUPLICATE KEY UPDATE userName = userName;


CREATE TABLE if NOT EXISTS `user_current_role`(
	id_user INT,
	id_role INT ,
	PRIMARY KEY (id_user, id_role),
	CONSTRAINT fk_user FOREIGN KEY (id_user) REFERENCES `user`(id),
	CONSTRAINT fk_role FOREIGN KEY (id_role) REFERENCES `current_role`(id)
);

-- messages
CREATE TABLE if NOT EXISTS message(
	id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
	message_title VARCHAR(64) NOT NULL,
	message_content LONGTEXT,
	message_date TIMESTAMP NOT NULL,
	message_sender INT NOT NULL,
	message_receiver INT NOT NULL,
	CONSTRAINT fk_user_sender FOREIGN KEY (message_sender) REFERENCES user(id),
	CONSTRAINT fk_user_receiver FOREIGN KEY (message_receiver) REFERENCES user(id)
);

-- projects
CREATE TABLE if NOT EXISTS tag(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	tag_name VARCHAR(32) NOT NULL
);

CREATE TABLE if NOT EXISTS category(
	id_category INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	category_name VARCHAR(32) NOT NULL
);

CREATE TABLE if NOT EXISTS project(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	title VARCHAR(64) NOT NULL,
	content LONGTEXT,
	thumbnail VARCHAR(150),
	project_date TIMESTAMP NOT NULL,
	last_modification_date TIMESTAMP NOT NULL,
	viewcount INT NOT NULL DEFAULT 0,
	is_featured BOOLEAN DEFAULT FALSE,
	id_category INT NOT NULL,
	`status` INT DEFAULT 1,
	CONSTRAINT fk_project_category FOREIGN KEY (id_category) REFERENCES category(id_category)
);

CREATE TABLE if NOT EXISTS commentary(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	content LONGTEXT NOT NULL,
	comment_date TIMESTAMP NOT NULL,
	id_project INT NOT NULL,
	CONSTRAINT fk_project_commentary FOREIGN KEY (id_project) REFERENCES project(id)
);

CREATE TABLE if NOT EXISTS project_user(
	id_project INT NOT NULL,
	id_user INT NOT NULL,
	PRIMARY KEY (id_project, id_user),
	CONSTRAINT fk_project_user FOREIGN KEY (id_project) REFERENCES project(id),
	CONSTRAINT fk_user_project FOREIGN KEY (id_user) REFERENCES user(id)
);

CREATE TABLE if NOT EXISTS bookmark(
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	bookmark_date TIMESTAMP NOT NULL,
	id_project INT NOT NULL,
	id_user INT NOT NULL,
	CONSTRAINT fk_project_bookmark FOREIGN KEY (id_project) REFERENCES project(id),
	CONSTRAINT fk_user_bookmark FOREIGN KEY (id_user) REFERENCES user(id)
);

CREATE TABLE if NOT EXISTS like_project(
	id_user INT NOT NULL,
	id_project INT NOT NULL,
	like_date TIMESTAMP NOT NULL,
	PRIMARY KEY (id_project, id_user),
	CONSTRAINT fk_project_like FOREIGN KEY (id_project) REFERENCES project(id),
	CONSTRAINT fk_user_lik FOREIGN KEY (id_user) REFERENCES user(id),
	UNIQUE KEY unique_like (id_user, id_project),
	INDEX idx_user (id_user),
	INDEX idx_date (like_date)
);

CREATE TABLE if NOT EXISTS follow(
	id INT PRIMARY KEY AUTO_INCREMENT,
	follower_id INT NOT NULL,
	following_id INT NOT NULL,
	follow_date TIMESTAMP NOT NULL,
	notification_enabled BOOLEAN DEFAULT TRUE,
	FOREIGN KEY (follower_id) REFERENCES user(id),
	FOREIGN KEY (following_id) REFERENCES user(id),
	UNIQUE KEY (follower_id, following_id),
	INDEX idx_follower (follower_id),
	INDEX idx_following (following_id)
);

CREATE TABLE if NOT EXISTS project_tags(
	id_tag INT NOT NULL,
	id_project INT NOT NULL,
	PRIMARY KEY (id_tag, id_project),
	UNIQUE KEY unique_tag (id_project, id_tag),
	CONSTRAINT fk_project_tag FOREIGN KEY (id_project) REFERENCES project(id),
	CONSTRAINT fk_tag_project FOREIGN KEY (id_tag) REFERENCES tag(id)
	
);
