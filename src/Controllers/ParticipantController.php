<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Slim\Views\Twig;
use Monolog\Logger;
use Illuminate\Database\Query\Builder;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use ReCaptcha\ReCaptcha;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ParticipantController {
    protected $logger;
    protected $mailer;
    protected $table;
    protected $recaptcha;

    public function __construct(Logger $logger, PHPMailer $mailer, Builder $table, Recaptcha $recaptcha) {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->table = $table;
        $this->recaptcha = $recaptcha;
    }

    public function signup(Request $request, Response $response, array $args) {
        $recaptchaResponse = $request->getParsedBody()['recaptchaResponse'];
        $resp = $this->recaptcha->verify($recaptchaResponse);
        if (!$resp->isSuccess()) {
            $errors = $resp->getErrorCodes();
            return $response->withJson([
                'message' => 'invalid recaptcha response'
            ])->withStatus(400);
        }

        $data = $request->getParsedBody()['participantData'];
        $event = Event::find($args['id']);
        $this->logger->info('Signup for event ' . $args['id']);
        if (!($data['name'] && $data['email'])) {
            return $response->withJson([
                'message' => 'invalid data'
            ])->withStatus(400);
        }
        $participant = Participant::create([
            'name' => $data['name'],
            'email' => $data['email']
        ]);
        $participant->event()->associate($event);
        if ($participant->save()/* && $this->sendNotification($event, $participant)*/) {
            return $response->withJson([
                'message' => 'success'
            ]);
        } else {
            return $response->withJson([
                'message' => 'failure'
            ])->withStatus(500);
        }
    }

    private function sendNotification(Event $event, Participant $participant) {
        $subject = 'New participant for ' . $event->title;
        $body = $participant->name . ' (' . $participant->email . ')'
            . ' has registered for your event ' . $event->title;
        // $this->logger->info($body);

        $this->mailer->Subject = $subject;
        $this->mailer->Body = $body;
        try {
            return $this->mailer->send();
        } catch (Exception $e) {
            $this->logger->error('Message could not be sent. Mailer Error: ' . $this->mailer->ErrorInfo);
            return false;
        }
        // return true;
    }

    public function delete(Request $request, Response $response, array $args) {
        $event = Participant::find($args['id']);
        $event->delete();
    }

}
