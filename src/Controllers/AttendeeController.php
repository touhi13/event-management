<?php
namespace Controllers;

use Core\Auth;
use Core\Controller;
use Models\Attendee;
use Models\Event;

class AttendeeController extends Controller
{
    private Event $eventModel;
    private Attendee $attendeeModel;
    private Auth $auth;

    public function __construct()
    {
        $this->eventModel    = new Event();
        $this->attendeeModel = new Attendee();
        $this->auth          = Auth::getInstance();
    }

    public function register(): void
    {
        $eventId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$eventId) {
            $this->redirect('/events');
        }

        $event = $this->eventModel->findById($eventId);
        if (!$event) {
            $this->redirect('/events');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateRequest($_POST, [
                'name'  => 'required|min:3',
                'email' => 'required|email',
                'phone' => 'required',
            ]);

            if (empty($errors)) {
                // Check if already registered
                if ($this->attendeeModel->isAlreadyRegistered($eventId, $_POST['email'])) {
                    $errors['email'] = 'You are already registered for this event';
                }
                // Check event capacity
                elseif (!$this->eventModel->hasCapacity($eventId)) {
                    $errors['capacity'] = 'Sorry, this event is already at full capacity';
                } else {
                    $registrationData = array_merge($_POST, ['event_id' => $eventId]);

                    if ($this->attendeeModel->create($registrationData)) {
                        $this->redirect("/events/view?id={$eventId}");
                    } else {
                        $errors['general'] = 'Registration failed';
                    }
                }
            }

            $this->render('attendees/register', [
                'event'  => $event,
                'errors' => $errors,
            ]);
            return;
        }

        $this->render('attendees/register', ['event' => $event]);
    }

    public function exportAttendees(): void
    {
        $eventId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$eventId) {
            $this->redirect('/events');
        }

        // Check if user is admin or event organizer
        if (!$this->auth->isAdmin()) {
            $event = $this->eventModel->findById($eventId);
            if (!$event || $event['user_id'] !== $this->auth->getUserId()) {
                $this->redirect('/events');
            }
        }

        $csv = $this->attendeeModel->exportToCsv($eventId);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="attendees.csv"');
        echo $csv;
        exit;
    }

    public function list(): void
    {
        $eventId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$eventId) {
            $this->redirect('/events');
        }

        // Check if user is admin or event organizer
        if (!$this->auth->isAdmin()) {
            $event = $this->eventModel->findById($eventId);
            if (!$event || $event['user_id'] !== $this->auth->getUserId()) {
                $this->redirect('/events');
            }
        }

        $attendees = $this->attendeeModel->findByEventId($eventId);
        $event     = $this->eventModel->findById($eventId);

        $this->render('attendees/list', [
            'attendees' => $attendees,
            'event'     => $event,
            'isAdmin'   => $this->auth->isAdmin(),
            'userId'    => $this->auth->getUserId(),
        ]);
    }
}
