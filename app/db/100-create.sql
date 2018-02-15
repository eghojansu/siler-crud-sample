-- base tables

DROP TABLE IF EXISTS post;
CREATE TABLE post (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    hit_counter BIGINT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NULL DEFAULT NULL,
    updated_at DATETIME NULL DEFAULT NULL,
    user_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`slug`)
) ENGINE = MyISAM;

DROP TABLE IF EXISTS category;
CREATE TABLE category (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    category VARCHAR(200) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`category`)
) ENGINE = MyISAM;

DROP TABLE IF EXISTS post_category;
CREATE TABLE post_category (
    post_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (`post_id`, `category_id`)
) ENGINE = MyISAM;

DROP TABLE IF EXISTS user;
CREATE TABLE user (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(32) NOT NULL,
    password VARCHAR(128) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    coolname VARCHAR(50) NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`username`)
) ENGINE = MyISAM;


-- views

DROP VIEW IF EXISTS view_post;
CREATE VIEW view_post AS
SELECT
    p.id,
    p.title,
    p.slug,
    p.content,
    p.hit_counter,
    p.created_at,
    p.updated_at,
    p.user_id,
    u.username as author_username,
    u.fullname as author_fullname,
    u.coolname as author_coolname,
    group_concat(c.category) as categories
FROM post p
JOIN user u ON u.id = p.user_id
LEFT JOIN post_category pc ON pc.post_id = p.id
LEFT JOIN category c ON c.id = pc.category_id
GROUP BY p.id;


-- triggers

DROP TRIGGER IF EXISTS post_after_delete;
DELIMITER $$
CREATE TRIGGER post_after_delete
AFTER DELETE ON post
FOR EACH ROW
BEGIN
    DELETE FROM post_category WHERE post_id = OLD.id;
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS category_after_delete;
DELIMITER $$
CREATE TRIGGER category_after_delete
AFTER DELETE ON category
FOR EACH ROW
BEGIN
    DELETE FROM post_category WHERE category_id = OLD.id;
END$$
DELIMITER ;