CREATE DATABASE readme DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

USE readme;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(128) NOT NULL UNIQUE,
    login VARCHAR(128) NOT NULL UNIQUE,
    pass CHAR(64) NOT NULL,
    avatar VARCHAR(128) NOT NULL
);

CREATE TABLE content_type (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(128) NOT NULL UNIQUE,
    class_name VARCHAR(128) NOT NULL
);

INSERT INTO content_type (type_name, class_name) VALUES ('Текст','text');
INSERT INTO content_type (type_name, class_name) VALUES ('Цитата','quote');
INSERT INTO content_type (type_name, class_name) VALUES ('Картинка','photo');
INSERT INTO content_type (type_name, class_name) VALUES ('Видео','video');
INSERT INTO content_type (type_name, class_name) VALUES ('Ссылка','link');

CREATE TABLE hash_tags (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tag_name VARCHAR(128) NOT NULL UNIQUE
);

CREATE TABLE posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    title VARCHAR(256) NOT NULL,
    content TEXT,
    quote_author VARCHAR(256),
    picture VARCHAR(256),
    video VARCHAR(256),
    link VARCHAR(256),
    views_count INT UNSIGNED NOT NULL DEFAULT '0',
    user_id INT UNSIGNED,
    content_type_id INT UNSIGNED,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (content_type_id) REFERENCES content_type(id)
);

CREATE TABLE posts_hashtags (
    post_id INT UNSIGNED,
    hashtag_id INT UNSIGNED,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (hashtag_id) REFERENCES hash_tags(id)
);

CREATE TABLE comments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    content TEXT NOT NULL,
    user_id INT UNSIGNED,
    post_id INT UNSIGNED,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
);

CREATE TABLE likes (
    user_id INT UNSIGNED,
    post_id INT UNSIGNED,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
);

CREATE TABLE subscribers (
    author INT UNSIGNED,
    subscription INT UNSIGNED,
    FOREIGN KEY (author) REFERENCES users(id),
    FOREIGN KEY (subscription) REFERENCES users(id)
);

CREATE TABLE messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    content TEXT NOT NULL,
    sender INT UNSIGNED,
    recipient INT UNSIGNED,
    FOREIGN KEY (sender) REFERENCES users(id),
    FOREIGN KEY (recipient) REFERENCES users(id)
);
