<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],
        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'db' => [
            'driver' => 'sqlite',
            'database' => __DIR__ . '/../development_db.sqlite3',
            // 'host' => 'localhost',
            // 'database' => 'database',
            // 'username' => 'user',
            // 'password' => 'password',
            // 'charset'   => 'utf8',
            // 'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
        'phpmailer' => [
            'smtp_server' => 'smtp.example.com',
            'username' => 'hello@example.com',
            'password' => 'password',
            'secure' => 'tls',
            'port' => 587,
            'notify_address' => 'notify@example.com',
        ]
    ],
];