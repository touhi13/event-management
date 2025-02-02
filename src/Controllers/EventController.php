<?php
namespace Controllers;

use Core\Auth;
use Core\Controller;
use Models\Event;

class EventController extends Controller
{
    private Event $eventModel;
    private Auth $auth;

    public function __construct()
    {
        $this->eventModel = new Event();
        $this->auth       = Auth::getInstance();
    }

    public function index(): void
    {
        $events  = $this->eventModel->findAll();
        $userId  = $this->auth->getUserId();
        $isAdmin = $this->auth->isAdmin();

        $this->render('events/index', [
            'events'  => $events,
            'userId'  => $userId,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function create(): void
    {
        $this->render('events/create');
    }

    public function store(): void
    {
        $errors = $this->validateRequest($_POST, [
            'name'         => 'required|min:3',
            'event_date'   => 'required',
            'max_capacity' => 'required',
            'location'     => 'required',
        ]);

        if (empty($errors)) {
            $eventData = array_merge($_POST, [
                'user_id' => $this->auth->getUserId(),
            ]);

            if ($this->eventModel->create($eventData)) {
                $this->redirect('/events');
            }
        }

        $this->render('events/create', ['errors' => $errors]);
    }

    public function edit(): void
    {
        $id    = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $event = $this->eventModel->findById($id);

        if (!$event) {
            $this->redirect('/events');
        }

        $this->render('events/edit', ['event' => $event]);
    }

    public function update(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('/events');
        }

        // Check if event exists and user has permission
        $event = $this->eventModel->findById($id);
        if (!$event || ($event['user_id'] !== $this->auth->getUserId() && !$this->auth->isAdmin())) {
            $this->redirect('/events');
        }

        $errors = $this->validateRequest($_POST, [
            'name'         => 'required|min:3',
            'event_date'   => 'required',
            'max_capacity' => 'required',
            'location'     => 'required',
        ]);

        if (empty($errors)) {
            // Prepare event data
            $eventData = [
                'name'         => $_POST['name'],
                'description'  => $_POST['description'],
                'event_date'   => $_POST['event_date'],
                'location'     => $_POST['location'],
                'max_capacity' => $_POST['max_capacity'],
                'user_id'      => $event['user_id'], // Keep original user_id
            ];

            if ($this->eventModel->update($id, $eventData)) {
                $this->redirect('/events');
                return;
            }
        }

        // If we get here, there was an error
        $this->render('events/edit', [
            'event'  => array_merge($event, $_POST), // Preserve submitted data
            'errors' => $errors,
        ]);
    }

    public function delete(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id && $this->eventModel->delete($id)) {
            $this->redirect('/events');
        }
        $this->redirect('/events');
    }

    public function view(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('/events');
        }

        $event = $this->eventModel->findById($id);
        if (!$event) {
            $this->redirect('/events');
        }

        // Get attendee count
        $attendeeCount = $this->eventModel->getAttendeeCount($id);

        // Get user info
        $userId  = $this->auth->getUserId();
        $isAdmin = $this->auth->isAdmin();

        $this->render('events/view', [
            'event'         => $event,
            'attendeeCount' => $attendeeCount,
            'userId'        => $userId,
            'isAdmin'       => $isAdmin,
        ]);
    }
}
