<?php

function handleWaitAnimalType($pdo, $telegram, $chatId, $text)
{
    saveTemp($pdo, $chatId, ['animal_type' => $text]);

    updateStep($pdo, $chatId, 'wait_photo');

    $telegram->sendMessage([
        'chat_id' => $chatId,
        'text' => "Rasm yuboring"
    ]);
}

function handleWaitPhoto($pdo, $telegram, $chatId, $photo, $user)
{
    if (!$photo) return;

    $temp = json_decode($user['temp_data'], true);

    insertByAvailableColumns($pdo, 'posts', [
        'user_id' => $user['id'],
        'title' => 'Srochno sotiladi',
        'breed' => $temp['animal_type']
    ]);

    updateUser($pdo, $chatId, ['step' => 'main', 'temp_data' => '{}']);

    $telegram->sendMessage([
        'chat_id' => $chatId,
        'text' => "✅ Saqlandi"
    ]);
}
