<?php
// Конфигурация приложения
session_start();

// Настройки валюты
define('USD_RATE', 12650); // 1 USD = 12650 UZS
define('BASE_CURRENCY', 'UZS');

// Данные для Telegram бота
define('TELEGRAM_TOKEN', '8692381214:AAEs86Ott1QyVt8EmPR8jUaaWo1_mrawjgM');
define('TELEGRAM_CHAT_ID', '8606629738');

// Пути к файлам данных
define('DATA_DIR', __DIR__ . '/data/');
define('ORDERS_FILE', DATA_DIR . 'orders.json');
define('MESSAGES_FILE', DATA_DIR . 'messages.json');

// Создание папки для данных, если её нет
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

// Инициализация файлов данных, если их нет
if (!file_exists(ORDERS_FILE)) {
    file_put_contents(ORDERS_FILE, json_encode([]));
}
if (!file_exists(MESSAGES_FILE)) {
    file_put_contents(MESSAGES_FILE, json_encode([]));
}

// Функция для получения курса валюты
function getCurrencyRate() {
    return USD_RATE;
}

// Функция конвертации
function convertCurrency($amount, $from = 'UZS', $to = null) {
    $rate = USD_RATE;
    if ($to === null) {
        $to = isset($_SESSION['currency']) ? $_SESSION['currency'] : 'UZS';
    }
    
    if ($from === $to) {
        return $amount;
    }
    
    if ($from === 'UZS' && $to === 'USD') {
        return round($amount / $rate, 2);
    }
    
    if ($from === 'USD' && $to === 'UZS') {
        return round($amount * $rate);
    }
    
    return $amount;
}

// Функция форматирования цены
function formatPrice($price, $currency = null) {
    if ($currency === null) {
        $currency = isset($_SESSION['currency']) ? $_SESSION['currency'] : 'UZS';
    }
    
    $symbol = $currency === 'USD' ? '$' : 'UZS';
    $formatted = number_format($price, $currency === 'USD' ? 2 : 0);
    
    return $symbol . ' ' . $formatted;
}

// Функция отправки в Telegram
function sendTelegram($message) {
    $token = TELEGRAM_TOKEN;
    $chatId = TELEGRAM_CHAT_ID;
    
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    return $result;
}

// Функция сохранения заказа
function saveOrder($orderData) {
    $orders = json_decode(file_get_contents(ORDERS_FILE), true);
    $orderData['id'] = count($orders) + 1;
    $orderData['date'] = date('Y-m-d H:i:s');
    $orderData['status'] = 'Новый';
    $orders[] = $orderData;
    file_put_contents(ORDERS_FILE, json_encode($orders, JSON_PRETTY_PRINT));
    
    // Отправка в Telegram
    $message = "🛒 <b>Новый заказ!</b>\n\n";
    $message .= "👤 Имя: {$orderData['name']}\n";
    $message .= "📞 Телефон: {$orderData['phone']}\n";
    $message .= "📦 Товар: {$orderData['product']}\n";
    $message .= "💰 Цена: {$orderData['price']}\n";
    $message .= "📅 Дата: {$orderData['date']}\n";
    
    sendTelegram($message);
    
    return true;
}

// Функция сохранения сообщения
function saveMessage($messageData) {
    $messages = json_decode(file_get_contents(MESSAGES_FILE), true);
    $messageData['id'] = count($messages) + 1;
    $messageData['date'] = date('Y-m-d H:i:s');
    $messages[] = $messageData;
    file_put_contents(MESSAGES_FILE, json_encode($messages, JSON_PRETTY_PRINT));
    
    // Отправка в Telegram
    $message = "💬 <b>Новое сообщение</b>\n\n";
    $message .= "👤 Имя: {$messageData['name']}\n";
    $message .= "📞 Телефон: {$messageData['phone']}\n";
    $message .= "📝 Сообщение: {$messageData['message']}\n";
    $message .= "📅 Дата: {$messageData['date']}\n";
    
    sendTelegram($message);
    
    return true;
}

// Функция аутентификации админа
function isAdmin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }
    return true;
}

// Функция проверки логина
function loginAdmin($username, $password) {
    $validUsername = 'abdulloh_a';
    $validPassword = 'abdullokh1111';
    
    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}

// Функция выхода
function logoutAdmin() {
    session_destroy();
    return true;
}

// Получение списка товаров (демо-данные)
function getProducts() {
    return [
        [
            'id' => 1,
            'name' => 'iPhone 15 Pro Max',
            'brand' => 'Apple',
            'price' => 13500000,
            'description' => 'Флагманский смартфон Apple с титановым корпусом, чипом A17 Pro и улучшенной камерой.',
            'image' => 'https://via.placeholder.com/300x300/007AFF/FFFFFF?text=iPhone+15+Pro+Max',
            'specs' => [
                'Экран' => '6.7" OLED, 120Hz',
                'Процессор' => 'A17 Pro',
                'Память' => '8GB RAM, 256GB ROM',
                'Камера' => '48MP + 12MP + 12MP',
                'Батарея' => '4441 мАч'
            ]
        ],
        [
            'id' => 2,
            'name' => 'Samsung Galaxy S24 Ultra',
            'brand' => 'Samsung',
            'price' => 12500000,
            'description' => 'Мощный флагман Samsung с S Pen, 200-мегапиксельной камерой и AI-функциями.',
            'image' => 'https://via.placeholder.com/300x300/1428A0/FFFFFF?text=Galaxy+S24+Ultra',
            'specs' => [
                'Экран' => '6.8" Dynamic AMOLED 2X, 120Hz',
                'Процессор' => 'Snapdragon 8 Gen 3',
                'Память' => '12GB RAM, 256GB ROM',
                'Камера' => '200MP + 12MP + 50MP + 10MP',
                'Батарея' => '5000 мАч'
            ]
        ],
        [
            'id' => 3,
            'name' => 'Xiaomi 14 Pro',
            'brand' => 'Xiaomi',
            'price' => 8500000,
            'description' => 'Флагман Xiaomi с камерой Leica, быстрой зарядкой и мощным процессором.',
            'image' => 'https://via.placeholder.com/300x300/FF6900/FFFFFF?text=Xiaomi+14+Pro',
            'specs' => [
                'Экран' => '6.73" AMOLED, 120Hz',
                'Процессор' => 'Snapdragon 8 Gen 3',
                'Память' => '12GB RAM, 256GB ROM',
                'Камера' => '50MP + 50MP + 50MP',
                'Батарея' => '4880 мАч'
            ]
        ],
        [
            'id' => 4,
            'name' => 'JBL Tune 770NC',
            'brand' => 'JBL',
            'price' => 1500000,
            'description' => 'Наушники с активным шумоподавлением, 70 часов работы и качественным звуком.',
            'image' => 'https://via.placeholder.com/300x300/004B93/FFFFFF?text=JBL+Tune+770NC',
            'specs' => [
                'Тип' => 'Накладные',
                'Подключение' => 'Bluetooth 5.3, 3.5mm',
                'Шумоподавление' => 'Да, Adaptive ANC',
                'Время работы' => 'до 70 часов',
                'Зарядка' => 'USB-C, быстрая'
            ]
        ],
        [
            'id' => 5,
            'name' => 'Sony WH-1000XM5',
            'brand' => 'Sony',
            'price' => 3200000,
            'description' => 'Лучшие наушники с шумоподавлением от Sony с превосходным качеством звука.',
            'image' => 'https://via.placeholder.com/300x300/1A1A1A/FFFFFF?text=Sony+WH-1000XM5',
            'specs' => [
                'Тип' => 'Накладные',
                'Подключение' => 'Bluetooth 5.2, 3.5mm',
                'Шумоподавление' => 'Да, HD Noise Cancelling',
                'Время работы' => 'до 30 часов',
                'Зарядка' => 'USB-C, быстрая'
            ]
        ],
        [
            'id' => 6,
            'name' => 'MacBook Air 15" M3',
            'brand' => 'Apple',
            'price' => 18000000,
            'description' => 'Легкий и мощный ноутбук Apple с чипом M3, 15-дюймовым экраном и долгой батареей.',
            'image' => 'https://via.placeholder.com/300x300/555555/FFFFFF?text=MacBook+Air+M3',
            'specs' => [
                'Экран' => '15.3" Liquid Retina',
                'Процессор' => 'Apple M3',
                'Память' => '8GB RAM, 256GB SSD',
                'Вес' => '1.51 кг',
                'Батарея' => 'до 18 часов'
            ]
        ],
        [
            'id' => 7,
            'name' => 'Realme GT 5',
            'brand' => 'Realme',
            'price' => 4500000,
            'description' => 'Игровой смартфон с 240W зарядкой и мощным процессором.',
            'image' => 'https://via.placeholder.com/300x300/C8A851/FFFFFF?text=Realme+GT+5',
            'specs' => [
                'Экран' => '6.74" AMOLED, 144Hz',
                'Процессор' => 'Snapdragon 8 Gen 2',
                'Память' => '16GB RAM, 512GB ROM',
                'Зарядка' => '240W Ultra Fast',
                'Батарея' => '4600 мАч'
            ]
        ],
        [
            'id' => 8,
            'name' => 'Anker Power Bank 20000mAh',
            'brand' => 'Anker',
            'price' => 450000,
            'description' => 'Мощный повербанк с быстрой зарядкой и индикацией остатка заряда.',
            'image' => 'https://via.placeholder.com/300x300/1E1E1E/FFFFFF?text=Anker+Power+Bank',
            'specs' => [
                'Емкость' => '20000 мАч',
                'Выход' => 'USB-C PD 30W',
                'Вход' => 'USB-C 30W',
                'Вес' => '350 г',
                'Особенности' => 'Дисплей, 3 порта'
            ]
        ]
    ];
}

// Получение всех брендов
function getBrands() {
    $brands = [
        'Apple', 'Samsung', 'Xiaomi', 'Redmi', 'Poco', 'Huawei', 'Honor', 
        'Realme', 'Infinix', 'Tecno', 'Vivo', 'Oppo', 'OnePlus', 'Google', 
        'Nothing', 'Meizu', 'Nokia', 'Hisense', 'Green Lion', 'Porodo', 
        'Baseus', 'Anker', 'Hoco', 'Borofone', 'Remax', 'Joyroom', 'Ugreen', 
        'LDNIO', 'Mcdodo', 'Choetech', 'Budi', 'Awei', 'Celebrat', 'Earldom', 
        'Spigen', 'UNIQ', 'WiWU', 'KZDOO', 'Santa Barbara', 'JBL', 'Sony', 
        'Marshall', 'Beats', 'Amazfit', 'Haylou', 'Kieslect', 'Mibro', 'Kisonli', 
        'Defender', 'Sven', 'Fantech', 'Bloody', 'HyperX', 'Razer', 'Logitech', 
        'Artel', 'Hofmann', 'Avalon', 'Shivaki', 'Premier', 'Goodwell', 'Uaken', 
        'Ziffler', 'Ferre', 'Welkin', 'Royal', 'Milano', 'Midea', 'Beko', 
        'Loretto', 'Beston', 'Immer', 'Ssmart', 'Sitronic', 'Selva', 'Brando', 
        'Moonx', 'Rulls', 'Akay', 'Zarget', 'Aishang', 'Zelmer', 'Polaris', 
        'Scarlett', 'Vitek', 'Ideal', 'Vesta', 'Perfectum'
    ];
    sort($brands);
    return $brands;
}