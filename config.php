<?php
// Файл конфігурації: параметри роботи веб-додатку
date_default_timezone_set('Europe/Kyiv');

define('SITE_TITLE', 'Опитування: цифрові звички студентів');
define('SURVEY_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'survey');

// Питання анкети. type: radio | select | checkbox | textarea
$questions = [
    'screen_time' => [
        'text'     => 'Скільки годин на день ви проводите за смартфоном?',
        'type'     => 'radio',
        'options'  => ['Менше 2 годин', '2–4 години', '4–6 годин', 'Більше 6 годин'],
        'required' => true,
    ],
    'social' => [
        'text'     => 'Якою соцмережею чи месенджером ви користуєтесь найчастіше?',
        'type'     => 'select',
        'options'  => ['Telegram', 'Instagram', 'TikTok', 'YouTube', 'Інше'],
        'required' => true,
    ],
    'ai_usage' => [
        'text'     => 'Як часто ви використовуєте ШІ-інструменти (ChatGPT, Claude тощо) для навчання?',
        'type'     => 'radio',
        'options'  => ['Щодня', 'Кілька разів на тиждень', 'Рідко', 'Ніколи'],
        'required' => true,
    ],
    'devices' => [
        'text'     => 'Які пристрої ви використовуєте для навчання? (можна обрати кілька)',
        'type'     => 'checkbox',
        'options'  => ['Ноутбук', 'Стаціонарний ПК', 'Планшет', 'Смартфон'],
        'required' => true,
    ],
    'change' => [
        'text'     => 'Що б ви хотіли змінити у своїх цифрових звичках?',
        'type'     => 'textarea',
        'required' => false,
    ],
];
