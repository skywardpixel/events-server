<?php

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controllers\EventController;
use App\Controllers\ParticipantController;
use Chadicus\Slim\OAuth2\Http\RequestBridge;
use Chadicus\Slim\OAuth2\Http\ResponseBridge;


$authMiddleware = new Chadicus\Slim\OAuth2\Middleware\Authorization(
    $app->getContainer()->get('OAuth2Server'),
    $app->getContainer()
);

$app->post('/api/token', function (Request $request, Response $response, array $args)
        use ($app) {
    $server = $app->getContainer()->get('OAuth2Server');
    $oauth2Request = RequestBridge::toOAuth2($request);
    $oauth2Response = $server->handleTokenRequest($oauth2Request);
    return ResponseBridge::fromOAuth2($oauth2Response);
});

$app->get('/api/events',
    EventController::class . ':index'
)->setName('events');

$app->get('/api/event/{id:[0-9]+}',
    EventController::class . ':detail'
)->setName('show_event');

$app->post('/api/event/{id:[0-9]+}/signup',
    ParticipantController::class . ':signup'
)->setName('signup_event');

$app->group('/api/admin', function () {
    $this->get('/event/{id:[0-9]+}/participants',
        EventController::class . ':participants'
    )->setName('admin_events');

    $this->post('/event/create',
        EventController::class . ':create'
    )->setName('create_event');

    $this->put('/event/{id:[0-9]+}',
        EventController::class . ':update'
    )->setName('update_event');

    $this->delete('/event/{id:[0-9]+}',
        EventController::class . ':delete'
    )->setName('delete_event');

    $this->delete('/participant/{id:[0-9]+}',
        ParticipantController::class . ':delete'
    )->setName('delete_participant');
})->add($authMiddleware->withRequiredScope(['admin']));
