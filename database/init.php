<?php

function initDatabase($pdo)
{
    $pdo->exec("\n        CREATE TABLE IF NOT EXISTS users (\n            id INT AUTO_INCREMENT PRIMARY KEY,\n            telegram_id BIGINT UNIQUE,\n            name VARCHAR(255),\n            phone VARCHAR(50),\n            username VARCHAR(255),\n            first_name VARCHAR(255),\n            role VARCHAR(20),\n            address VARCHAR(255),\n            step VARCHAR(50),\n            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,\n            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP\n        )\n    ");

    $requiredColumns = [
        'name' => "ALTER TABLE users ADD COLUMN name VARCHAR(255) NULL",
        'phone' => "ALTER TABLE users ADD COLUMN phone VARCHAR(50) NULL",
        'username' => "ALTER TABLE users ADD COLUMN username VARCHAR(255) NULL",
        'first_name' => "ALTER TABLE users ADD COLUMN first_name VARCHAR(255) NULL",
        'role' => "ALTER TABLE users ADD COLUMN role VARCHAR(20) NULL",
        'address' => "ALTER TABLE users ADD COLUMN address VARCHAR(255) NULL",
        'step' => "ALTER TABLE users ADD COLUMN step VARCHAR(50) NULL",
        'created_at' => "ALTER TABLE users ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP",
        'updated_at' => "ALTER TABLE users ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
    ];

    foreach ($requiredColumns as $column => $sql) {
        $stmt = $pdo->prepare("\n            SELECT COUNT(*)\n            FROM information_schema.COLUMNS\n            WHERE TABLE_SCHEMA = DATABASE()\n              AND TABLE_NAME = 'users'\n              AND COLUMN_NAME = ?\n        ");
        $stmt->execute([$column]);

        if ((int) $stmt->fetchColumn() === 0) {
            $pdo->exec($sql);
        }
    }

    $pdo->exec("\n        CREATE TABLE IF NOT EXISTS posts (\n            id INT AUTO_INCREMENT PRIMARY KEY,\n            user_id INT,\n            category_id INT NULL,\n            title VARCHAR(255) NULL,\n            description TEXT NULL,\n            price DECIMAL(12,2) NULL,\n            currency VARCHAR(10) NULL DEFAULT 'UZS',\n            breed VARCHAR(100) NULL,\n            gender VARCHAR(20) NULL,\n            age VARCHAR(50) NULL,\n            location VARCHAR(255) NULL,\n            image VARCHAR(255) NULL,\n            status VARCHAR(20) NULL DEFAULT 'draft',\n            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP\n        )\n    ");

    $requiredPostColumns = [
        'category_id' => "ALTER TABLE posts ADD COLUMN category_id INT NULL",
        'title' => "ALTER TABLE posts ADD COLUMN title VARCHAR(255) NULL",
        'description' => "ALTER TABLE posts ADD COLUMN description TEXT NULL",
        'price' => "ALTER TABLE posts ADD COLUMN price DECIMAL(12,2) NULL",
        'currency' => "ALTER TABLE posts ADD COLUMN currency VARCHAR(10) NULL DEFAULT 'UZS'",
        'breed' => "ALTER TABLE posts ADD COLUMN breed VARCHAR(100) NULL",
        'gender' => "ALTER TABLE posts ADD COLUMN gender VARCHAR(20) NULL",
        'age' => "ALTER TABLE posts ADD COLUMN age VARCHAR(50) NULL",
        'location' => "ALTER TABLE posts ADD COLUMN location VARCHAR(255) NULL",
        'image' => "ALTER TABLE posts ADD COLUMN image VARCHAR(255) NULL",
        'status' => "ALTER TABLE posts ADD COLUMN status VARCHAR(20) NULL DEFAULT 'draft'",
        'created_at' => "ALTER TABLE posts ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP",
    ];

    foreach ($requiredPostColumns as $column => $sql) {
        $stmt = $pdo->prepare("\n            SELECT COUNT(*)\n            FROM information_schema.COLUMNS\n            WHERE TABLE_SCHEMA = DATABASE()\n              AND TABLE_NAME = 'posts'\n              AND COLUMN_NAME = ?\n        ");
        $stmt->execute([$column]);

        if ((int) $stmt->fetchColumn() === 0) {
            $pdo->exec($sql);
        }
    }
}
