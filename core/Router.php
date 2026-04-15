<?php

namespace App\Core;

use Telegram\Bot\Api;
use PDO;

require_once __DIR__ . '/../helpers/db.php';
require_once __DIR__ . '/../handlers/AuthHandler.php';
require_once __DIR__ . '/../handlers/MenuHandler.php';
require_once __DIR__ . '/../handlers/PostHandler.php';

class Router
{
    public static function handle(Api $telegram, PDO $pdo, $message)
    {
        $chatId = $message->getChat()->getId();
        $text = trim((string)$message->getText());
        $contact = $message->getContact();
        $photo = $message->getPhoto();

        $user = getUserByChatId($pdo, $chatId);

        if (!$user) {
            handleUnregisteredUser($pdo, $telegram, $chatId, $text);
            return;
        }

        $step = $user['step'];

        if ($text === '/start' || $text === '🏠 Bosh sahifa') {
            handleHomeMenu($pdo, $telegram, $chatId, $user['role'] ?? null);
            return;
        }

        switch ($step) {
            case 'reg_name':
                handleRegName($pdo, $telegram, $chatId, $text);
                break;

            case 'reg_phone':
                handleRegPhone($pdo, $telegram, $chatId, $text, $contact);
                break;

            case 'reg_role':
                handleRegRole($pdo, $telegram, $chatId, $text);
                break;

            case 'main':
                handleMainMenu($pdo, $telegram, $chatId, $user, $text, $user['role']);
                break;

            case 'wait_animal_type':
                handleWaitAnimalType($pdo, $telegram, $chatId, $text);
                break;

            case 'wait_photo':
                handleWaitPhoto($pdo, $telegram, $chatId, $photo, $user);
                break;
        }
    }
}
