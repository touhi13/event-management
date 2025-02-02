<?php
namespace Controllers;

use Core\Controller;
use Models\Event;

class EventController extends Controller
{
    private Event $eventModel;

    public function __construct()
    {
        $this->eventModel = new Event();
    }

    public function index(): void
    {
        $events = $this->eventModel->findAll();
        $this->render('events/index', ['events' => $events]);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateRequest($_POST, [
                'name'         => 'required|min:3',
                'event_date'   => 'required',
                'max_capacity' => 'required',
            ]);

            if (empty($errors)) {
                if ($this->eventModel->create($_POST)) {
                    $this->redirect('/events');
                }
            }

            $this->render('events/create', ['errors' => $errors]);
        }

        $this->render('events/create');
    }

    public function view(int $id): void
    {
        $event = $this->eventModel->findById($id);

        if (!$event) {
            $this->redirect('/events');
        }

        $this->render('events/view', ['event' => $event]);
    }
}