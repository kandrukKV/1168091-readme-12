CREATE DATABASE readme DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

USE readme;

CREATE TABLE users
(
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    email    VARCHAR(128) NOT NULL UNIQUE,
    login    VARCHAR(128) NOT NULL UNIQUE,
    pass     CHAR(64)     NOT NULL,
    avatar   VARCHAR(128)
);

CREATE TABLE content_type
(
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type_name  VARCHAR(128) NOT NULL UNIQUE,
    class_name VARCHAR(128) NOT NULL
);

CREATE TABLE hash_tags
(
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tag_name VARCHAR(128) NOT NULL UNIQUE
);

CREATE TABLE posts
(
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    datetime         DATETIME              DEFAULT CURRENT_TIMESTAMP,
    title            VARCHAR(256) NOT NULL,
    content          TEXT,
    quote_author     VARCHAR(256),
    picture          VARCHAR(256),
    video            VARCHAR(256),
    link             VARCHAR(256),
    views_count      INT UNSIGNED NOT NULL DEFAULT '0',
    user_id          INT UNSIGNED NOT NULL,
    is_repost        BOOLEAN      NOT NULL DEFAULT '0',
    original_post_id INT UNSIGNED          DEFAULT NULL,
    content_type_id  INT UNSIGNED NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (original_post_id) REFERENCES posts (id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (content_type_id) REFERENCES content_type (id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE posts_hashtags
(
    post_id    INT UNSIGNED NOT NULL,
    hashtag_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (hashtag_id) REFERENCES hash_tags (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE comments
(
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    content  TEXT         NOT NULL,
    user_id  INT UNSIGNED NOT NULL,
    post_id  INT UNSIGNED NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE likes
(
    user_id  INT UNSIGNED NOT NULL,
    post_id  INT UNSIGNED NOT NULL,
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE subscribers
(
    author       INT UNSIGNED NOT NULL,
    subscription INT UNSIGNED NOT NULL,
    FOREIGN KEY (author) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (subscription) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE messages
(
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    datetime       DATETIME              DEFAULT CURRENT_TIMESTAMP,
    content        TEXT         NOT NULL,
    sender         INT UNSIGNED NOT NULL,
    recipient      INT UNSIGNED NOT NULL,
    is_new_message BOOLEAN      NOT NULL DEFAULT '1',
    FOREIGN KEY (sender) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (recipient) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE FULLTEXT INDEX post_search ON posts (title, content);
