CREATE TABLE `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `email` VARCHAR(250) UNIQUE KEY NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `color` VARCHAR(32) NOT NULL DEFAULT '#FFFFFF',
  `admin` TINYINT NOT NULL DEFAULT 0,
  `language` VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `loginlinks` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `expires` BIGINT NOT NULL,
  `token` VARCHAR(32) NOT NULL,
  `logs_in_to` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `wishes` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `for` INT NOT NULL,
  `title` VARCHAR(250) NOT NULL,
  `description` MEDIUMTEXT NOT NULL,
  `added_by` INT NOT NULL,
  `added_timestamp` BIGINT NOT NULL,
  `edited_by` INT DEFAULT NULL,
  `edited_timestamp` BIGINT DEFAULT NULL,
  `struck` TINYINT NOT NULL DEFAULT 0,
  `personal` TINYINT NOT NULL,
  `picturetype` VARCHAR(32) DEFAULT NULL,
  `picture` mediumblob DEFAULT NULL,
  INDEX(`for`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `comments` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `for_wish` INT NOT NULL,
  `added_timestamp` BIGINT NOT NULL,
  `added_by` INT NOT NULL,
  `comment` text NOT NULL,
  INDEX(`for_wish`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

