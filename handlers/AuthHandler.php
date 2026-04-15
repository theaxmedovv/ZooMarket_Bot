<?php

function handleUnregisteredUser($pdo, $telegram, $chatId, $text)
{
    if ($text === '/start') {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE telegram_id = ?");
        $stmt->execute([$chatId]);

        if (!$stmt->fetch()) {
            $pdo->prepare("INSERT INTO users (telegram_id, step) VALUES (?, 'reg_name')")
                ->execute([$chatId]);
        } else {
            updateUser($pdo, $chatId, ['step' => 'reg_name']);
        }

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "👋 **Xush kelibsiz!**\n\nRo'yxatdan o'tishni boshlaymiz. Iltimos, ismingizni kiriting:",
            'parse_mode' => 'markdown'
        ]);
    }
}

function handleRegName($pdo, $telegram, $chatId, $text)
{
    if (strlen($text) < 3) {
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "❌ Ism juda qisqa. Iltimos, ismingizni to'liq kiriting:"
        ]);
        return;
    }

    updateUser($pdo, $chatId, ['name' => $text, 'step' => 'reg_phone']);

    // Pastki klaviatura tugmasi (Reply Keyboard)
    $telegram->sendMessage([
        'chat_id' => $chatId,
        'text' => "📞 Rahmat, **$text**. Endi telefon raqamingizni yuboring:",
        'parse_mode' => 'markdown',
        'reply_markup' => json_encode([
            'keyboard' => [
                [['text' => "📱 Raqamni yuborish", 'request_contact' => true]]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ])
    ]);
}

function handleRegPhone($pdo, $telegram, $chatId, $text, $contact)
{
    $phone = $contact ? $contact->getPhoneNumber() : $text;

    if (empty($phone)) {
        $telegram->sendMessage(['chat_id' => $chatId, 'text' => "Iltimos, pastdagi tugmani bosing yoki raqam yozing:"]);
        return;
    }

    updateUser($pdo, $chatId, ['phone' => $phone, 'step' => 'reg_role']);

    // Inline tugmalar (Xabar ostidagi tugmalar)
    $telegram->sendMessage([
        'chat_id' => $chatId,
        'text' => "👤 **Siz kimsiz?**\nO'zingizga mos rolni tanlang:",
        'parse_mode' => 'markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    ['text' => "🛍️ Sotuvchi", 'callback_data' => 'role_seller'],
                    ['text' => "👤 Xaridor", 'callback_data' => 'role_user']
                ]
            ]
        ])
    ]);
}

function handleRegRole($pdo, $telegram, $chatId, $callbackData)
{
    // Callback ma'lumotiga qarab rolni aniqlash
    $role = ($callbackData === 'role_seller') ? 'seller' : 'user';
    $roleName = ($role === 'seller') ? "Sotuvchi" : "Xaridor";

    updateUser($pdo, $chatId, ['role' => $role, 'step' => 'main']);

    $telegram->sendMessage([
        'chat_id' => $chatId,
        'text' => "✅ **Tayyor!**\nSiz tizimga **$roleName** sifatida kirdingiz.",
        'parse_mode' => 'markdown',
        'reply_markup' => json_encode(['remove_keyboard' => true])
    ]);
}
