-- Optional: run in phpMyAdmin to create the DB and tables instead of relying on auto-create
CREATE DATABASE IF NOT EXISTS phishguard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE phishguard;
CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(160) UNIQUE, password_hash VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE campaigns (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), template TEXT, channel ENUM('email','sms'), strength INT, scheduled_at DATETIME NULL, created_at DATETIME);
CREATE TABLE messages (id INT AUTO_INCREMENT PRIMARY KEY, campaign_id INT, target VARCHAR(255), token VARCHAR(64), sent_at DATETIME NULL, submitted_at DATETIME NULL, created_at DATETIME);
CREATE TABLE clicks (id INT AUTO_INCREMENT PRIMARY KEY, campaign_id INT, user VARCHAR(255), token VARCHAR(64), ip VARCHAR(100), user_agent TEXT, created_at DATETIME);
CREATE TABLE submissions (id INT AUTO_INCREMENT PRIMARY KEY, campaign_id INT, user VARCHAR(255), username VARCHAR(255), password VARCHAR(255), created_at DATETIME);
