<?php

require __DIR__ . '/vendor/autoload.php';

use Telegram\Bot\Api;
use Dotenv\Dotenv;

// 1. ENV Yuklash
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// $token = $_ENV['TELEGRAM_BOT_TOKEN'];
$token ='7404688905:AAHElIM6b5GKooDyz-XBRd_SLTMbq9njssY';
$telegram = new Api($token);

// 2. DB Ulanish
try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DATABASE']};charset=utf8mb4",
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Bazaga ulanishda xatolik: " . $e->getMessage());
}

// 3. Bazani tayyorlash
initializeDatabaseSchema($pdo);

function initializeDatabaseSchema(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        telegram_id BIGINT NOT NULL UNIQUE,
        username VARCHAR(255) NULL,
        first_name VARCHAR(255) NULL,
        role VARCHAR(50) NULL,
        phone_number VARCHAR(50) NULL,
        address TEXT NULL,
        animal_type VARCHAR(255) NULL,
        step VARCHAR(50) NOT NULL DEFAULT 'start',
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

echo "✅ ZooMarket bot ishga tushdi (Long Polling)...\n";

$offset = 0;

while (true) {
    try {
        $updates = $telegram->getUpdates([
            'offset' => $offset,
            'timeout' => 30,
        ]);

        foreach ($updates as $update) {
            $offset = $update->getUpdateId() + 1;

            $message = $update->getMessage();
            if (!$message) continue;

            $chatId = $message->getChat()->getId();
            $text = trim((string)$message->getText());
            $contact = $message->getContact();
            $from = $message->getFrom();
            $username = $from->getUsername() ?? null;

            // Foydalanuvchini bazadan qidirish
            $stmt = $pdo->prepare("SELECT * FROM users WHERE telegram_id = ?");
            $stmt->execute([$chatId]);
            $user = $stmt->fetch();

            if (!$user) {
                $pdo->prepare("INSERT INTO users (telegram_id, username, step) VALUES (?, ?, 'start')")
                    ->execute([$chatId, $username]);
                $step = 'start';
            } else {
                $step = $user['step'];
            }

            // --- LOGIKA BOSHLANDI ---

            // 1. START komandasi
            if ($text === '/start') {
                $pdo->prepare("UPDATE users SET step = 'name' WHERE telegram_id = ?")->execute([$chatId]);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "👋 ZooMarketga xush kelibsiz!\n\nRo'yxatdan o'tishni boshlaymiz. Iltimos, ismingizni kiriting:",
                    'reply_markup' => json_encode(['remove_keyboard' => true])
                ]);
                continue;
            }

            // 2. ISMNI QABUL QILISH
            if ($step === 'name') {
                $pdo->prepare("UPDATE users SET first_name = ?, step = 'role' WHERE telegram_id = ?")
                    ->execute([$text, $chatId]);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "🎯 Rahmat, $text. Endi o'z rolingizni tanlang:",
                    'reply_markup' => json_encode([
                        'keyboard' => [[['text' => 'Sotuvchi (Seller)'], ['text' => 'Xaridor (Buyer)']]],
                        'resize_keyboard' => true,
                        'one_time_keyboard' => true
                    ])
                ]);
                continue;
            }

            // 3. ROLNI QABUL QILISH
            if ($step === 'role') {
                $role = (strpos(strtolower($text), 'sotuvchi') !== false) ? 'seller' : 'buyer';

                $pdo->prepare("UPDATE users SET role = ?, step = 'phone' WHERE telegram_id = ?")
                    ->execute([$role, $chatId]);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "📞 Telefon raqamingizni pastdagi tugma orqali yuboring:",
                    'reply_markup' => json_encode([
                        'keyboard' => [[['text' => '📲 Raqamni yuborish', 'request_contact' => true]]],
                        'resize_keyboard' => true,
                        'one_time_keyboard' => true
                    ])
                ]);
                continue;
            }

            // 4. TELEFON RAQAMNI QABUL QILISH
            if ($step === 'phone') {
                $phone = $contact ? $contact->getPhoneNumber() : $text;

                $pdo->prepare("UPDATE users SET phone_number = ?, step = 'address' WHERE telegram_id = ?")
                    ->execute([$phone, $chatId]);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "🏠 Manzilingizni yozing (Viloyat, shahar, ko'cha):",
                    'reply_markup' => json_encode(['remove_keyboard' => true])
                ]);
                continue;
            }

            // 5. MANZILNI QABUL QILISH
            if ($step === 'address') {
                $pdo->prepare("UPDATE users SET address = ?, step = 'animal' WHERE telegram_id = ?")
                    ->execute([$text, $chatId]);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "🐾 Qaysi turdagi hayvonlar bilan ishlaysiz?\n(Masalan: Itlar, mushuklar, parrandalar...)"
                ]);
                continue;
            }

            // 6. HAYVON TURINI QABUL QILISH VA YAKUNLASH
            if ($step === 'animal') {
                $pdo->prepare("UPDATE users SET animal_type = ?, step = 'done' WHERE telegram_id = ?")
                    ->execute([$text, $chatId]);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "✅ Tabriklaymiz! Ro'yxatdan muvaffaqiyatli o'tdingiz.\n\nSizning ma'lumotlaringiz saqlandi. Tez orada xizmatlardan foydalanishingiz mumkin! 🚀",
                    'reply_markup' => json_encode(['remove_keyboard' => true])
                ]);
                continue;
            }

            // DEFAULT JAVOB
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Noma'lum buyruq. Qayta boshlash uchun /start bosing."
            ]);

        }
    } catch (Throwable $e) {
        echo "❌ Xatolik yuz berdi: " . $e->getMessage() . "\n";
        sleep(2); // Xatolik bo'lsa serverni charchatmaslik uchun ozgina kutamiz
    }
}
