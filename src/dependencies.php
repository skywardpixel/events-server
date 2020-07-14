<?php

use App\MyAuth\PdoStorage;

$container = $app->getContainer();

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Service factory for the ORM
$container['db'] = function ($c) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($c['settings']['db']);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    return $capsule;
};

$container['sendgrid'] = function ($c) {
    $sendgrid = new \SendGrid($c['settings']['sendgrid']['API_KEY']);
    return $sendgrid;
};

$container['App\Controllers\EventController'] = function ($c) {
    $logger = $c->get('logger');
    $table = $c->get('db')->table('events');
    return new \App\Controllers\EventController($logger, $table);
};

$container['App\Controllers\ParticipantController'] = function ($c) {
    $logger = $c->get('logger');
    $table = $c->get('db')->table('participants');
    $sendgrid = $c->get('sendgrid');
    $recaptcha = $c->get('Recaptcha');
    $email_config = $c->get('settings')['email'];
    return new \App\Controllers\ParticipantController($logger, $sendgrid, $table,
            $recaptcha, $email_config);
};

$container['OAuth2Server'] = function ($c) {
    date_default_timezone_set('UTC');
    $pdo = new PDO($c['settings']['oauth_pdo']);
    $storage = new PdoStorage($pdo);
    $server = new OAuth2\Server($storage);
    $userCreds = new OAuth2\GrantType\UserCredentials($storage);
    $server->addGrantType($userCreds);
    return $server;
};

$container['Recaptcha'] = function ($c) {
    $settings = $c->get('settings')['recaptcha'];
    $recaptcha = new \ReCaptcha\ReCaptcha(
        $settings['secret'],
        new \ReCaptcha\RequestMethod\CurlPost()
    );
    return $recaptcha;
};
