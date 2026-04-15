<?php

namespace App\Core;

use Telegram\Bot\Api;
use PDO;

class Bot
{
    private Api $telegram;
    private PDO $pdo;
    private int $offset = 0;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
        $this->pdo = require __DIR__ . '/../config/database.php';

        require_once __DIR__ . '/../database/init.php';
        initDatabase($this->pdo);
    }

    public function run()
    {
        while (true) {
            $updates = $this->telegram->getUpdates([
                'offset' => $this->offset,
                'timeout' => 30
            ]);

            foreach ($updates as $update) {
                $this->offset = $update->getUpdateId() + 1;

                $message = $update->getMessage();
                if (!$message) continue;

                Router::handle($this->telegram, $this->pdo, $message);
            }
        }
    }
}
