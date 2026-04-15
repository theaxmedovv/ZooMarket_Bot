<?php

function getUserByChatId($pdo, $chatId)
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE telegram_id=?");
    $stmt->execute([$chatId]);
    return $stmt->fetch();
}

function updateUser($pdo, $chatId, $data)
{
    $set = implode(',', array_map(fn($k) => "$k=?", array_keys($data)));

    $pdo->prepare("UPDATE users SET $set WHERE telegram_id=?")
        ->execute([...array_values($data), $chatId]);
}

function updateStep($pdo, $chatId, $step)
{
    $pdo->prepare("UPDATE users SET step=? WHERE telegram_id=?")
        ->execute([$step, $chatId]);
}

function saveTemp($pdo, $chatId, $data)
{
    $stmt = $pdo->prepare("SELECT temp_data FROM users WHERE telegram_id=?");
    $stmt->execute([$chatId]);

    $old = json_decode($stmt->fetchColumn() ?? '{}', true);
    $new = json_encode(array_merge($old, $data));

    $pdo->prepare("UPDATE users SET temp_data=? WHERE telegram_id=?")
        ->execute([$new, $chatId]);
}

function insertByAvailableColumns($pdo, $table, $data)
{
    $cols = array_keys($data);
    $sql = "INSERT INTO $table (" . implode(',', $cols) . ") VALUES (" . implode(',', array_fill(0, count($cols), '?')) . ")";
    $pdo->prepare($sql)->execute(array_values($data));
}
