<?php

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/bot.php';

use Telegram\Bot\Api;
use Dotenv\Dotenv;
use App\Core\Bot;

// ENV
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$token = $_ENV['TELEGRAM_BOT_TOKEN'] ?? '7404688905:AAHElIM6b5GKooDyz-XBRd_SLTMbq9njssY';
$telegram = new Api($token);

$bot = new Bot($telegram);
$bot->run();
