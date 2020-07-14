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
            'name' => 'events',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'oauth_pdo' => 'sqlite:' . __DIR__ . '/../oauth.sqlite3',
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
        'sendgrid' => [
            'API_KEY' => '',
        ],
        'recaptcha' => [
            'secret' => '',
        ],
        'email' => [
            'to_addresses' => [
                'test@example.com',
            ],
            'from_address' => 'hello@example.com',
            'from_name' => 'Hello World',
        ],
    ],
];
