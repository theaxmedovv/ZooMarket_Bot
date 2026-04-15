<?php

function backKeyboard()
{
    return json_encode([
        'keyboard' => [[['text' => '🏠 Bosh sahifa']]],
        'resize_keyboard' => true
    ]);
}
