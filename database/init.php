<?php

function initDatabase($pdo)
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            telegram_id BIGINT UNIQUE,
            name VARCHAR(255),
            phone VARCHAR(50),
            role VARCHAR(20),
            step VARCHAR(50),
            temp_data JSON
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            title VARCHAR(255),
            breed VARCHAR(100)
        )
    ");
}
