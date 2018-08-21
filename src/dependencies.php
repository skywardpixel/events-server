<?php

use App\MyAuth\PdoStorage;
use Aptoma\Twig\Extension\MarkdownExtension;
use Aptoma\Twig\Extension\MarkdownEngine;

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

$container['mailer'] = function($c) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $settings = $c['settings']['phpmailer'];
    $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->host = $settings['smtp_server'];
    $mail->username = $settings['username'];
    $mail->password = $settings['password'];
    $mail->secure = $settings['secure'];
    $mail->port = $settings['port'];
    $mail->setFrom($settings['from'], 'Micetek Events');
    foreach($settings['receivers'] as $address) {
        $mail->addAddress($address);
    }
    return $mail;
};

$container['App\Controllers\EventController'] = function ($c) {
    $logger = $c->get('logger');
    $table = $c->get('db')->table('events');
    return new \App\Controllers\EventController($logger, $table);
};

$container['App\Controllers\ParticipantController'] = function ($c) {
    $logger = $c->get('logger');
    $table = $c->get('db')->table('participants');
    $mailer = $c->get('mailer');
    return new \App\Controllers\ParticipantController($logger, $mailer, $table);
};

$container['OAuth2Server'] = function ($c) {
    $pdo = new PDO($c['settings']['oauth_pdo']);
    $storage = new PdoStorage($pdo);
    $server = new OAuth2\Server($storage);
    $userCreds = new OAuth2\GrantType\UserCredentials($storage);
    $server->addGrantType($userCreds);
    return $server;
};