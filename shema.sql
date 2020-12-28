CREATE DATABASE readme DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

USE readme;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dt_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(128) NOT NULL UNIQUE,
    login VARCHAR(128) NOT NULL UNIQUE,
    pass CHAR(64) NOT NULL UNIQUE,
    avatar VARCHAR(128) NOT NULL UNIQUE
);

CREATE INDEX u_email ON users(email);
CREATE INDEX u_login ON users(login);

CREATE TABLE content_type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(128) NOT NULL UNIQUE,
    class_name VARCHAR(128) NOT NULL
);

INSERT INTO content_type (type_name, class_name) VALUES ('Текст','text');
INSERT INTO content_type (type_name, class_name) VALUES ('Цитата','quote');
INSERT INTO content_type (type_name, class_name) VALUES ('Картинка','photo');
INSERT INTO content_type (type_name, class_name) VALUES ('Видео','video');
INSERT INTO content_type (type_name, class_name) VALUES ('Ссылка','link');

CREATE TABLE hash_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tag_name VARCHAR(128) NOT NULL UNIQUE
);

CREATE INDEX h_tag_name ON hash_tags(tag_name);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dt_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    title VARCHAR(256) NOT NULL,
    content TEXT NOT NULL,
    picture VARCHAR(256),
    video VARCHAR(256),
    link VARCHAR(256),
    views_count INT NOT NULL DEFAULT '0',
    user_id INT,
    content_type VARCHAR(128),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (content_type) REFERENCES content_type(type_name)
);

CREATE TABLE posts_hashtags (
    post_id INT,
    hashtag_id INT,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (hashtag_id) REFERENCES hash_tags(id)
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dt_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    content TEXT NOT NULL,
    user_id INT,
    post_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
);

CREATE INDEX c_user_id ON comments(user_id);

CREATE TABLE likes (
    user_id INT,
    post_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
);

CREATE TABLE subscribers (
    author INT,
    subscription INT,
    FOREIGN KEY (author) REFERENCES users(id),
    FOREIGN KEY (subscription) REFERENCES users(id)
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dt_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    content TEXT NOT NULL,
    sender INT,
    recipient INT,
    FOREIGN KEY (sender) REFERENCES users(id),
    FOREIGN KEY (recipient) REFERENCES users(id)
);

CREATE INDEX m_sender ON messages(sender);