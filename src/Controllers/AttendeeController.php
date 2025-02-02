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
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Invalid event ID'], 400);
            }
            $this->redirect('/events');
        }

        $event = $this->eventModel->findById($eventId);
        if (!$event) {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Event not found'], 404);
            }
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
                        if ($this->isAjaxRequest()) {
                            $this->json([
                                'success'  => true,
                                'message'  => 'Registration successful!',
                                'redirect' => "/events/view?id={$eventId}",
                            ]);
                        }
                        $this->redirect("/events/view?id={$eventId}");
                    } else {
                        $errors['general'] = 'Registration failed';
                    }
                }
            }

            if ($this->isAjaxRequest()) {
                $this->json(['errors' => $errors], 400);
            }

            $this->render('attendees/register', [
                'event'  => $event,
                'errors' => $errors,
            ]);
            return;
        }

        $this->render('attendees/register', ['event' => $event]);
    }

    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
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

        // Get pagination parameters
        $page    = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 10;

        // Get filter and sort parameters
        $filters = [
            'search'    => $_GET['search'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to'   => $_GET['date_to'] ?? '',
        ];

        // Default sort is by registration_date DESC
        $sortBy    = $_GET['sort'] ?? 'registration_date';
        $sortOrder = $_GET['order'] ?? 'DESC';

        // Get filtered and sorted attendees
        $attendees = $this->attendeeModel->getFilteredAndSorted(
            $eventId,
            $filters,
            $sortBy,
            $sortOrder,
            $perPage,
            ($page - 1) * $perPage
        );

        // Get total count for pagination
        $total      = $this->attendeeModel->countFiltered($eventId, $filters);
        $totalPages = ceil($total / $perPage);

        $event = $this->eventModel->findById($eventId);

        $this->render('attendees/list', [
            'attendees'   => $attendees,
            'event'       => $event,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'filters'     => $filters,
            'sortBy'      => $sortBy,
            'sortOrder'   => $sortOrder,
            'isAdmin'     => $this->auth->isAdmin(),
            'userId'      => $this->auth->getUserId(),
        ]);
    }
}
