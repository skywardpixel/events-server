<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Monolog\Logger;
use Illuminate\Database\Query\Builder;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class EventController {
    protected $logger;
    protected $table;

    public function __construct(Logger $logger, Builder $table) {
        $this->logger = $logger;
        $this->table = $table;
    }

    public function index(Request $request, Response $response, array $args) {
        $events = Event::all();
        return $response->withJson($events);
    }

    public function detail(Request $request, Response $response, array $args) {
        $event = Event::find($args['id']);
        return $response->withJson($event);
    }

    public function participants(Request $request, Response $response, array $args) {
        $event = Event::find($args['id']);
        $participants = $event->participants;
        return $response->withJson($participants);
    }

    public function delete(Request $request, Response $response, array $args) {
        $event = Event::find($args['id']);
        $event->delete();
    }

    public function create(Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $event = Event::create([
            'title' => $data['title'],
            'location' => $data['location'],
            'date_time' => $data['date_time'],
            'description' => $data['description']
        ]);
        $event->save();
    }

    public function update(Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $event = Event::find($args['id']);
        $event->title = $data['title'];
        $event->location = $data['location'];
        $event->date_time = $data['date_time'];
        $event->description = $data['description'];
        $event->save();
    }

}
