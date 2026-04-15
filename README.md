# 🤖 ZooMarket Telegram Bot - O'zbek Qo'llanmasi

## 📋 Ichki Mundarija

1. [Setup va O'rnatish](#setup-va-ornatish)
2. [Registratsiya Oqimi](#registratsiya-oqimi)
3. [Foydalanuvchi Rolları](#foydalanuvchi-rolları)
4. [Sotuvchi Funksiyalari](#sotuvchi-funksiyalari)
5. [Xaridor Funksiyalari](#xaridor-funksiyalari)
6. [Database Strukturasi](#database-strukturasi)
7. [Tez Orada Keladigan Xususiyatlar](#tez-orada-keladigan-xususiyatlar)

---

## 🚀 Setup va O'rnatish

### Talablar:

- PHP 8.0+
- MySQL 5.7+
- Composer
- Telegram Bot Token

### O'rnatish Bosqichlari:

```bash
# 1. Proyektni klonlash
git clone <repository-url>
cd zoomarket-bot

# 2. Dependency larni o'rnatish
composer install

# 3. .env faylini yaratish
cp .env.example .env

# 4. .env faylini to'ldirish
TELEGRAM_BOT_TOKEN=7404688905:AAHElIM6b5GKooDyz-XBRd_SLTMbq9njssY
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=telegram_bot
DB_USERNAME=root
DB_PASSWORD=

# 5. Bot ni ishga tushirish
php bot.php
```

---

## 👤 Registratsiya Oqimi

### Qadamlar:

```
User → /start bosganligi
   ↓
Bot: "Ismingizni kiriting"
   ↓
User: "Abdullayev Ali" yozadi
   ↓
Bot: "Telefon raqamingizni yuboring"
   ↓
User: "+998901234567" yuboradi
   ↓
Bot: "Rolingizni tanlang"
   ↓
User: "Sotuvchi" yoki "Xaridor" tanlagani
   ↓
Bot: "Registratsiya tugallandi! ✅"
```

### Database da qanday saqlanadi:

```sql
users jadvalida:
┌────┬────────────────┬──────────────┬─────────────┬────────┬──────────┬──────────────┐
│ id │ telegram_id    │ name         │ phone       │ role   │ step     │ temp_data    │
├────┼────────────────┼──────────────┼─────────────┼────────┼──────────┼──────────────┤
│ 1  │ 123456789      │ Abdullayev   │ +998901234  │ seller │ main     │ {}           │
│ 2  │ 987654321      │ Ahmad        │ +998909876  │ user   │ main     │ {}           │
└────┴────────────────┴──────────────┴─────────────┴────────┴──────────┴──────────────┘
```

---

## 🎯 Foydalanuvchi Rolları

### 1️⃣ SOTUVCHI (Seller)

**Qo'l kelgan tugmalar:**

- ➕ Yangi e'lon
- 📥 So'rovlar
- 👤 Profil
- 🏠 Bosh sahifa

**E'lon qo'shish jarayoni:**

```
Yangi e'lon → Hayvon nomi → Kategoriya → Narx → Rasm → ✅ Saqlandi
```

**E'lon ma'lumotlari:**

- Hayvon nomi
- Kategoriya (It, Mushuk, Qushlar, va b.)
- Narx (UZS)
- Rasm (Telegram file ID)
- Joylashuv (avtomat: Toshkent)
- Holati (Faol, Faol emas, Sotildi)

---

### 2️⃣ XARIDOR (User)

**Qo'l kelgan tugmalar:**

- 🔍 Hayvonlarni ko'rish (tez orada)
- ❤️ Sevimlilar (tez orada)
- 👤 Profil
- 🏠 Bosh sahifa

**Funksiyalari:**

- Barcha e'lonlarni ko'rish
- So'rov qilish
- Sevimlilar ro'yxati yaratish

---

## 💼 Sotuvchi Funksiyalari

### E'lon Qo'shish

```php
// POST jadvalida quyidagi ma'lumotlar saqlanadi:
[
    'user_id'      => 1,              // Sotuvchi ID
    'category_id'  => 1,              // It, Mushuk, va b.
    'title'        => 'Toza it',      // Hayvon nomi
    'price'        => 500000,         // Narx
    'currency'     => 'UZS',          // Valyuta
    'breed'        => 'Noma\'lum',    // Shunakar turi
    'gender'       => 'male',         // Jinsi
    'age'          => '1y',           // Yoshi
    'location'     => 'Toshkent',     // Joylashuv
    'image'        => 'AgAD...',      // Telegram file ID
    'status'       => 'active',       // Holati
    'created_at'   => '2024-01-15'    // Yaratilish vaqti
]
```

### So'rovlarni Ko'rish

Xaridorlar so'rov qilganlari:

```
📥 Yangi so'rov
🐾 Hayvon: Toza it
💰 Narx: 500,000 UZS
👤 Xaridor: Ahmad
✅ Tasdiqlash yoki ❌ Rad etish
```

---

## 🛍️ Xaridor Funksiyalari

### Hayvonlarni Ko'rish (tez orada)

```
Barcha faol e'lonlarni ko'rish:
- Filtrash kategoriya bo'ylab
- Filtrash narx bo'ylab
- Foydalanuvchi profiliga o'tish
```

### So'rov Qilish

```
1. Hayvon tanlash
2. So'rov qilish tugmasini bosish
3. So'rov jo'natiladi sotuvchiga
4. Sotuvchi tasdiqlash yoki rad etadi
```

---

## 🗄️ Database Strukturasi

### 1. `users` Jadval

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    telegram_id BIGINT UNIQUE,           -- Telegram user ID
    name VARCHAR(255),                  -- Foydalanuvchi ismi
    phone VARCHAR(30),                  -- Telefon raqami
    role ENUM('user','seller'),         -- Foydalanuvchi roli
    step VARCHAR(50),                   -- Hozirgi bosqich
    temp_data JSON,                     -- Vaqtinchalik ma'lumotlar
    created_at TIMESTAMP                -- Ro'yxatdan o'tish vaqti
);
```

### 2. `categories` Jadval

```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE             -- Kategoriya nomi
);

-- Default kategoriyalar:
Insert: 'It 🐕', 'Mushuk 🐈', 'Qushlar 🦜', 'Baliqlar 🐠', 'Kemiruvchilar 🐹', 'Boshqa 🐾'
```

### 3. `posts` Jadval (E'lonlar)

```sql
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,                        -- Sotuvchi
    category_id INT,                    -- Kategoriya
    title VARCHAR(255),                 -- Sarlavha
    description TEXT,                   -- Tavsif
    price DECIMAL(15,2),                -- Narx
    currency VARCHAR(10),               -- Valyuta
    breed VARCHAR(100),                 -- Shunakar turi
    gender ENUM('male','female'),       -- Jinsi
    age VARCHAR(50),                    -- Yoshi
    location VARCHAR(255),              -- Joylashuv
    image VARCHAR(255),                 -- Rasm (Telegram file ID)
    status ENUM('active','inactive','sold'),  -- Holati
    created_at TIMESTAMP                -- Yaratilish vaqti
);
```

### 4. `purchase_requests` Jadval (So'rovlar)

```sql
CREATE TABLE purchase_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    animal_id INT,                      -- E'lon (post) ID
    buyer_id INT,                       -- Xaridor ID
    status ENUM('pending','approved','rejected'),  -- Holati
    created_at TIMESTAMP                -- So'rov vaqti
);
```

---

## 🔐 Xavfsizlik Buyurtmalari

### ✅ Amalga Oshirilgan

- ✅ SQL injection dan himoya (Prepared Statements)
- ✅ User input validation
- ✅ Single bot instance lock (bot.lock)
- ✅ Error handling va logging
- ✅ Database charset UTF-8
- ✅ Foreign key constraints

### ⚠️ Tavsiya Etilgan

- 🔒 HTTPS dan foydalaning
- 🔑 Token ni `.env` faylida saqlang
- 🛡️ Rate limiting qo'shing
- 📝 Logging va monitoring ni yoqing
- 🔏 Admin panel qo'shing

---

## 🚧 Tez Orada Keladigan Xususiyatlar

### Barcha e'lonlarni ko'rish

```
🔍 Hayvonlarni ko'rish → Kategoriya → Rasm + Ma'lumot → So'rov qilish
```

### Sevimlilar ro'yxati

```
❤️ Sevimlilar → Saqlangan e'lonlar → Boshqarish
```

### Admin Panel

```
- Barcha e'lonlarni boshqarish
- Foydalanuvchilarni boshqarish
- Kategoriyalarni qo'shish/o'chirish
- Statistika va reportlar
```

### Notifikatsiyalar

```
- So'rov jo'natildi
- E'lon tasdiqlandi
- E'lon faol bo'ldi
- So'rov rad etildi
```

### To'lov Sistema

```
- Click, Payme, Apelsin integratsiyasi
- E-wallet support
```

---

## 🐛 Umumiy Muammolar va Yechimlar

### Muammo 1: "Bot allaqachon ishlayapti"

```
Yechim: bot.lock faylini o'chiring
rm bot.lock
php bot.php
```

### Muammo 2: "Database xatosi"

```
Yechim: .env faylini tekshiring va MySQL ishlamoqdasini tekshiring
mysql -u root -p
CREATE DATABASE telegram_bot;
```

### Muammo 3: "Composer packages not found"

```
Yechim: Composer ni qayta o'rnatish
composer install --no-dev
```

---

## 📊 Asosiy Funksiyalarning Kodi

### Registratsiya Oqimi (Qisqa)

```php
// Registratsiya boshlash
case 'reg_name':
    updateUser($pdo, $chatId, ['name' => $text, 'step' => 'reg_phone']);
    // Telefon so'rash
    break;

case 'reg_phone':
    updateUser($pdo, $chatId, ['phone' => $phone, 'step' => 'reg_role']);
    // Rol so'rash
    break;

case 'reg_role':
    updateUser($pdo, $chatId, ['role' => $role, 'step' => 'main']);
    // Asosiy menyuga yuborish
    break;
```

### E'lon Qo'shish (Qisqa)

```php
case 'wait_photo':
    if ($photo) {
        insertByAvailableColumns($pdo, 'posts', $postData);
        updateUser($pdo, $chatId, ['step' => 'main']);
        sendMenu($telegram, $chatId, "✅ E'lon qo'shildi!", sellerKeyboard());
    }
    break;
```

---

## 📞 Foydalanuvchi Qo'llanmasi

### Sotuvchi uchun:

1. `/start` bosgach registratsiyadan o'ting
2. "Sotuvchi" rolini tanlang
3. "Yangi e'lon" tugmasini bosing
4. Hayvon haqidagi ma'lumotlarni kiriting
5. Rasm yuboring
6. E'lonni ko'rib chiqing va taxrirlash imkoniyatlaridan foydalaning

### Xaridor uchun:

1. `/start` bosgach registratsiyadan o'ting
2. "Xaridor" rolini tanlang
3. "Hayvonlarni ko'rish" tugmasini bosing
4. E'lonlarni ko'ring va so'rov qiling
5. Sevimlilar ro'yxatini yarating

---

## 🎓 Texnik Ma'lumotlar

- **Model:** Telegram Bot API + MySQL + PDO
- **Language:** PHP (OOP + Functional)
- **Framework:** Telegram Bot SDK
- **Database:** MySQL/MariaDB
- **Environment:** Docker (ixtiyori)

---

## 📝 Litsenziya

MIT License - Bepul foydalanish

---

## 👨‍💻 Devoloper

Ushbu bot ZooMarket loyihasini dastlab bilan yaratilgan.

**Savol-javob uchun:** support@zoomarket.uz

---

## 🚀 Oxirgi Yangilanish

- ✅ Registratsiya tizimi
- ✅ Sotuvchi xususiyatlari
- ✅ E'lon qo'shish
- ✅ So'rovlar tizimi
- 🔄 Hayvonlarni ko'rish (tez orada)
- 🔄 Sevimlilar (tez orada)
- 🔄 Admin panel (tez orada)

---

**Muvaffaqiyatli foydalanish!** 🎉
