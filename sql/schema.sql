CREATE DATABASE IF NOT EXISTS newsdb;
USE newsdb;

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    content LONGTEXT,
    image VARCHAR(500),
    url VARCHAR(500),
    source_name VARCHAR(100),
    published_at DATETIME,
    category ENUM('general', 'business', 'sports',  'technology', 'health'),
    UNIQUE(title)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'author', 'user') NOT NULL DEFAULT 'user',
    g_auth INT(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

DROP SCHEMA newsdb;