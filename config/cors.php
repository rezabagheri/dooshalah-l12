<?php
return [
    'paths' => ['api/*'], // فقط برای مسیرهای API اعمال می‌شه
    'allowed_methods' => ['*'], // همه‌ی متدها (GET, POST, و غیره) مجازن
    'allowed_origins' => ['https://dooshalah-l12.test'], // فقط دامنه‌ی خودت
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // همه‌ی هدرها مجازن
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // برای ارسال کوکی‌ها
];
