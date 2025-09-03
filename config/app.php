<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'MVC Framework',
    'debug' => $_ENV['APP_DEBUG'] ?? true,
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'UTC',
    'locale' => 'en',
    'key' => $_ENV['APP_KEY'] ?? 'your-secret-key-here',
];