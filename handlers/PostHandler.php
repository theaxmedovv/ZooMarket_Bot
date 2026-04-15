<?php

function handleWaitPostTitle($pdo, $telegram, $chatId, $text, $user)
{
    $animalText = trim((string) $text);

    if ($animalText === '') {
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "Iltimos, hayvon nomi yoki turini yozing."
        ]);
        return;
    }

    insertByAvailableColumns($pdo, 'posts', [
        'user_id' => $user['id'],
        'title' => $animalText,
        'breed' => $animalText,
    ]);

    updateStep($pdo, $chatId, 'main');

    $telegram->sendMessage([
        'chat_id' => $chatId,
        'text' => "✅ Saqlandi"
    ]);
}
