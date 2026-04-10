# Sazanami - プレイリスト共有アプリ

## 概要
本アプリは、YouTubeとニコニコ動画のURLを取り込むことで、好きな音楽や動画をプレイリスト形式で共有できるアプリです。

## テーブル作成に使用するSQL文
   ```SQL
   DROP TABLE IF EXISTS `playlist_tracks`;
   DROP TABLE IF EXISTS `tracks`;
   DROP TABLE IF EXISTS `playlists`;
   DROP TABLE IF EXISTS `users`;
   
   CREATE TABLE `users` (
     `id` INT AUTO_INCREMENT PRIMARY KEY,
     `username` VARCHAR(64) NOT NULL,
     `email` VARCHAR(255) NOT NULL UNIQUE,
     `password_hash` VARCHAR(255) NOT NULL,
     `icon` VARCHAR(512) DEFAULT NULL,
     `bio` TEXT DEFAULT NULL,
     `created_at` DATETIME DEFAULT NULL
   );
   
   CREATE TABLE `playlists` (
     `id` INT AUTO_INCREMENT PRIMARY KEY,
     `user_id` INT NOT NULL,
     `title` VARCHAR(128) NOT NULL,
     `description` TEXT DEFAULT NULL,
     `cover_image` VARCHAR(512) DEFAULT NULL,
     `created_at` DATETIME DEFAULT NULL,
     `updated_at` DATETIME DEFAULT NULL,
     FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
   );
   
   CREATE TABLE `tracks` (
     `id` INT AUTO_INCREMENT PRIMARY KEY,
     `url` VARCHAR(512) NOT NULL UNIQUE, 
     `platform` VARCHAR(16) NOT NULL,
     `title` VARCHAR(255) NOT NULL,
     `artist` VARCHAR(255) DEFAULT NULL,
     `thumbnail_url` VARCHAR(512) DEFAULT NULL,
     `created_at` DATETIME DEFAULT NULL
   );
   
   CREATE TABLE `playlist_tracks` (
     `id` INT AUTO_INCREMENT PRIMARY KEY,
     `playlist_id` INT NOT NULL,
     `track_id` INT NOT NULL,
     `sort_order` INT DEFAULT 0,
     `created_at` DATETIME DEFAULT NULL,
     FOREIGN KEY (`playlist_id`) REFERENCES `playlists`(`id`) ON DELETE CASCADE,
     FOREIGN KEY (`track_id`) REFERENCES `tracks`(`id`) ON DELETE CASCADE
   );
   ```
