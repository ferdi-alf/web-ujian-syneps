-- Drop existing tables if they exist
DROP TABLE IF EXISTS post_likes;
DROP TABLE IF EXISTS post_comments;
DROP TABLE IF EXISTS posts;

-- Create posts table with proper structure
CREATE TABLE posts (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    user_id bigint unsigned NOT NULL,
    content text NOT NULL,
    media_path varchar(255) DEFAULT NULL,
    media_type enum('image','video') DEFAULT NULL,
    likes_count int NOT NULL DEFAULT '0',
    comments_count int NOT NULL DEFAULT '0',
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY posts_user_id_foreign (user_id),
    CONSTRAINT posts_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

-- Create post_comments table
CREATE TABLE post_comments (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    user_id bigint unsigned NOT NULL,
    post_id bigint unsigned NOT NULL,
    content text NOT NULL,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY post_comments_user_id_foreign (user_id),
    KEY post_comments_post_id_foreign (post_id),
    CONSTRAINT post_comments_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT post_comments_post_id_foreign FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE
);

-- Create post_likes table
CREATE TABLE post_likes (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    user_id bigint unsigned NOT NULL,
    post_id bigint unsigned NOT NULL,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY post_likes_user_id_post_id_unique (user_id,post_id),
    KEY post_likes_post_id_foreign (post_id),
    CONSTRAINT post_likes_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT post_likes_post_id_foreign FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE
);
