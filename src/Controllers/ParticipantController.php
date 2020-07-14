<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Monolog\Logger;
use Illuminate\Database\Query\Builder;
use ReCaptcha\ReCaptcha;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response as Response;
use SendGrid;

class ParticipantController {
    protected $logger;
    protected $sendgrid;
    protected $table;
    protected $recaptcha;
    protected $email_config;

    public function __construct(Logger $logger, SendGrid $sendgrid,
            Builder $table, Recaptcha $recaptcha, array $email_config) {
        $this->logger = $logger;
        $this->sendgrid = $sendgrid;
        $this->table = $table;
        $this->recaptcha = $recaptcha;
        $this->email_config = $email_config;
    }

    public function signup(Request $request, Response $response, array $args) {
        $recaptchaResponse = $request->getParsedBody()['recaptchaResponse'];
        $remoteIp = $_SERVER['REMOTE_ADDR'];
        $resp = $this->recaptcha->verify($recaptchaResponse, $remoteIp);
        if (!$resp->isSuccess()) {
            $errors = $resp->getErrorCodes();
            return $response->withJson([
                'message' => 'invalid recaptcha response',
                'errors' => $errors
            ])->withStatus(400);
        } else {
            $data = $request->getParsedBody()['participantData'];
            $event = Event::find($args['id']);
            $this->logger->info('New registration for event ' . $event->title . '.');
            if (!($data['name'] && $data['email'] && $data['company'] && $data['phone'])) {
                return $response->withJson([
                    'message' => 'Invalid participant data.'
                ])->withStatus(400);
            }
            $participant = Participant::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'company' => $data['company'],
                'phone' => $data['phone'],
            ]);
            $participant->event()->associate($event);
            if ($participant->save() && $this->sendNotification($event, $participant)) {
                $this->logger->info('Successfully saved and sent email.');
                return $response->withJson([
                    'message' => 'success'
                ]);
            } else {
                $this->logger->info('Registration failure.');
                return $response->withJson([
                    'message' => 'failure'
                ])->withStatus(500);
            }
        }
    }

    private function sendNotification(Event $event, Participant $participant) {
        $subject = "New participant for $event->title";
        $body = "A new participant has registered for your event $event->title.\n";
        $body .= "Name: $participant->name\n";
        $body .= "Email: $participant->email\n";
        $body .= "Company: $participant->company\n";
        $body .= "Phone: $participant->phone\n";

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($this->email_config['from_address'], $this->email_config['from_name']);
        $email->setSubject($subject);
        foreach ($this->email_config['to_addresses'] as $to) {
            $email->addTo($to);
        }
        $email->addContent("text/plain", $body);
        try {
            $response = $this->sendgrid->send($email);
            $this->logger->info($response->statusCode() . "\n");
            $this->logger->info($response->headers());
            $this->logger->info($response->body() . "\n");
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Caught exception: ' . $e->getMessage() . "\n");
            return false;
        }
    }

    public function delete(Request $request, Response $response, array $args) {
        $event = Participant::find($args['id']);
        $event->delete();
    }

}
