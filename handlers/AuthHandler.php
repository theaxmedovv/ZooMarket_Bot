<?php

function handleUnregisteredUser($pdo, $telegram, $chatId, $text, $username = null, $firstName = null)
{
    if ($text === '/start') {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE telegram_id = ?");
        $stmt->execute([$chatId]);

        if (!$stmt->fetch()) {
            $pdo->prepare("INSERT INTO users (telegram_id, username, first_name, step) VALUES (?, ?, ?, 'reg_name')")
                ->execute([$chatId, $username, $firstName]);
        } else {
            updateUser($pdo, $chatId, ['username' => $username, 'first_name' => $firstName, 'step' => 'reg_name']);
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

    updateUser($pdo, $chatId, ['name' => $text, 'first_name' => $text, 'step' => 'reg_phone']);

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

    updateUser($pdo, $chatId, ['phone' => $phone, 'step' => 'reg_address']);

    $telegram->sendMessage([
        'chat_id' => $chatId,
        'text' => "🏠 Endi manzilingizni kiriting:",
        'parse_mode' => 'markdown',
    ]);
}

function handleRegAddress($pdo, $telegram, $chatId, $text)
{
    updateUser($pdo, $chatId, ['address' => $text, 'step' => 'reg_role']);

    $telegram->sendMessage([
        'chat_id' => $chatId,
        'text' => "👤 **Siz kimsiz?**\nO'zingizga mos rolni tanlang:",
        'parse_mode' => 'markdown',
        'reply_markup' => json_encode([
            'keyboard' => [[['text' => 'Sotuvchi'], ['text' => 'Xaridor']]],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ])
    ]);
}

function handleRegRole($pdo, $telegram, $chatId, $text)
{
    $normalized = mb_strtolower(trim((string) $text));

    if ($normalized !== 'sotuvchi' && $normalized !== 'xaridor') {
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "Iltimos, faqat quyidagidan birini tanlang: Sotuvchi yoki Xaridor.",
        ]);
        return;
    }

    $role = $normalized === 'sotuvchi' ? 'seller' : 'user';
    $roleName = ($role === 'seller') ? "Sotuvchi" : "Xaridor";

    updateUser($pdo, $chatId, ['role' => $role, 'step' => 'main']);

    $telegram->sendMessage([
        'chat_id' => $chatId,
        'text' => "✅ **Tayyor!**\nSiz tizimga **$roleName** sifatida kirdingiz.",
        'parse_mode' => 'markdown',
        'reply_markup' => json_encode(['remove_keyboard' => true])
    ]);
}
