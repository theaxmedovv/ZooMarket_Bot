<?php

require_once __DIR__ . '/../helpers/keyboard.php';

function sellerKeyboard()
{
    return json_encode([
        'keyboard' => [
            [['text' => "➕ Yangi e'lon"]],
            [['text' => '🏠 Bosh sahifa']]
        ],
        'resize_keyboard' => true
    ]);
}

function userKeyboard()
{
    return json_encode([
        'keyboard' => [
            [['text' => '🏠 Bosh sahifa']]
        ],
        'resize_keyboard' => true
    ]);
}

function handleHomeMenu($pdo, $telegram, $chatId, $role)
{
    updateStep($pdo, $chatId, 'main');

    $keyboard = $role === 'seller' ? sellerKeyboard() : userKeyboard();

    $telegram->sendMessage([
        'chat_id' => $chatId,
        'text' => "Asosiy menyu",
        'reply_markup' => $keyboard
    ]);
}

function handleMainMenu($pdo, $telegram, $chatId, $user, $text, $role)
{
    if ($text === "➕ Yangi e'lon") {
        updateStep($pdo, $chatId, 'wait_animal_type');

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "Qanday hayvon?",
            'reply_markup' => backKeyboard()
        ]);
    }
}
