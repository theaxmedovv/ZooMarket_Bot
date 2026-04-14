# Telegram Bot (PHP)

Bu loyiha `irazasyed/telegram-bot-sdk` bilan minimal echo bot.

Bot XAMPP MySQL bilan ishlashga moslangan.

## 1) Bot token olish

1. Telegram ichida `@BotFather` ni oching.
2. `/newbot` buyrug'ini yuboring.
3. Berilgan tokenni saqlab oling.

## 2) Tokenni sozlash (Windows PowerShell)

### Variant A: Hozirgi terminal uchun (eng tez)

```powershell
$env:TELEGRAM_BOT_TOKEN = "REAL_BOT_TOKEN"
```

Shu terminalning o'zida botni ishga tushirasiz.

### Variant B: Doimiy saqlash (`setx`)

```powershell
setx TELEGRAM_BOT_TOKEN "REAL_BOT_TOKEN"
```

`setx` dan keyin albatta yangi terminal oching.

### Variant C: `.env` fayl orqali

Loyiha ildizida `.env` fayl yarating:

```env
TELEGRAM_BOT_TOKEN=REAL_BOT_TOKEN
```

## 3) Botni ishga tushirish

### XAMPP MySQL sozlamasi

1. XAMPP Control Panel'dan `MySQL` ni `Start` qiling.
2. Loyiha ildizida `.env` fayl yarating (`.env.example` dan nusxa oling).
3. `.env` ichida quyidagilar bo'lsin:

```env
TELEGRAM_BOT_TOKEN=REAL_BOT_TOKEN
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=telegram_bot
DB_USERNAME=root
DB_PASSWORD=
```

Bot birinchi ishga tushganda `telegram_bot` bazasi va `users` jadvalini avtomatik yaratadi.

### Ishga tushirish

```powershell
php .\bot.php
```

## 4) Test

Telegramda botga `/start` yuboring yoki oddiy xabar yozing.
Bot sizning xabaringizni qaytaradi.
`/start` dan keyin `Nomerni yuborish` tugmasi chiqadi va kontakt yuborsangiz raqam bazaga saqlanadi.

## Eslatma

- Bu variant long polling ishlatadi.
- Serverga deploy qilgandan keyin webhook varianti tezroq va barqarorroq bo'ladi.
