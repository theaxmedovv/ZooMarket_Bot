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
        $from = $message->getFrom();
        $username = $from ? ($from->getUsername() ?? null) : null;
        $firstName = $from ? ($from->getFirstName() ?? null) : null;

        $user = getUserByChatId($pdo, $chatId);

        if (!$user) {
            handleUnregisteredUser($pdo, $telegram, $chatId, $text, $username, $firstName);
            return;
        }

        if ($username && (($user['username'] ?? null) !== $username || ($user['first_name'] ?? null) !== $firstName)) {
            updateUser($pdo, $chatId, ['username' => $username, 'first_name' => $firstName]);
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

            case 'reg_address':
                handleRegAddress($pdo, $telegram, $chatId, $text);
                break;

            case 'reg_role':
                handleRegRole($pdo, $telegram, $chatId, $text);
                break;

            case 'main':
                handleMainMenu($pdo, $telegram, $chatId, $user, $text, $user['role']);
                break;

            case 'wait_post_title':
                handleWaitPostTitle($pdo, $telegram, $chatId, $text, $user);
                break;
        }
    }
}
