<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Slim\Views\Twig;
use Monolog\Logger;
use Illuminate\Database\Query\Builder;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ParticipantController {
    protected $logger;
    protected $mailer;
    protected $table;

    public function __construct( Logger $logger, PHPMailer $mailer, Builder $table) {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->table = $table;
    }

    public function signup(Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $event = Event::find($args['id']);
        $this->logger->info('Signup for event ' . $args['id']);
        $participant = Participant::create([
            'name' => $data['name'],
            'email' => $data['email']
        ]);
        $participant->event()->associate($event);
        if ($participant->save()) {
            return $response->withJson([
                'message' => 'success'
            ]);
        } else {
            return $response->withJson([
                'message' => 'failure'
            ]);
        }
    }

    private function sendNotification(Event $event, Participant $participant) {
        // TODO:
    }

    public function delete(Request $request, Response $response, array $args) {
        $event = Participant::find($args['id']);
        $event->delete();
    }

}
