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
        $page    = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 10;

        // Get filter and sort parameters
        $filters = [
            'search'    => $_GET['search'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to'   => $_GET['date_to'] ?? '',
            'location'  => $_GET['location'] ?? '',
        ];

        // Default sort is by created_at DESC
        $sortBy    = $_GET['sort'] ?? 'created_at';
        $sortOrder = $_GET['order'] ?? 'DESC';

        // Get filtered and sorted events with pagination
        $result = $this->eventModel->getFilteredAndSorted(
            $filters,
            $sortBy,
            $sortOrder,
            $perPage,
            ($page - 1) * $perPage
        );

        // Get total count for pagination
        $total      = $this->eventModel->countFiltered($filters);
        $totalPages = ceil($total / $perPage);

        // Get locations for filter dropdown
        $locations = $this->eventModel->getLocations();

        $this->render('events/index', [
            'events'      => $result,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'filters'     => $filters,
            'sortBy'      => $sortBy,
            'sortOrder'   => $sortOrder,
            'locations'   => $locations,
            'isAdmin'     => $this->auth->isAdmin(),
            'userId'      => $this->auth->getUserId(),
            'title'       => 'Events',
        ]);
    }

    public function create(): void
    {
        $this->render('events/create');
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/events');
        }

        $errors = $this->validateRequest($_POST, [
            'name'         => 'required|min:3|max:100',
            'description'  => 'required|min:10|max:1000',
            'event_date'   => 'required|date|future_date',
            'location'     => 'required|min:3|max:255',
            'max_capacity' => 'required|integer|positive',
        ]);

        if (empty($errors)) {
            $eventData            = $_POST;
            $eventData['user_id'] = $this->auth->getUserId();

            // Additional server-side validation
            if (strtotime($eventData['event_date']) < time()) {
                $errors['event_date'] = 'Event date must be in the future';
            }

            if ((int) $eventData['max_capacity'] > 1000) {
                $errors['max_capacity'] = 'Maximum capacity cannot exceed 1000';
            }

            if (empty($errors) && $this->eventModel->create($eventData)) {
                $this->redirect('/events');
            } else {
                $errors['general'] = 'Failed to create event';
            }
        }

        // If we get here, there were validation errors
        $this->render('events/create', [
            'errors' => $errors,
            'event'  => $_POST,
        ]);
    }

    public function edit(): void
    {
        $eventId = $_GET['id'] ?? null;
        if (!$eventId) {
            $this->redirect('/events');
        }

        $event = $this->eventModel->findById($eventId);
        if (!$event || ($event['user_id'] !== $this->auth->getUserId() && !$this->auth->isAdmin())) {
            $this->redirect('/events');
        }

        $this->render('events/edit', ['event' => $event]);
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/events');
        }

        $eventId = $_POST['id'] ?? null;
        if (!$eventId) {
            $this->redirect('/events');
        }

        $event = $this->eventModel->findById($eventId);
        if (!$event || ($event['user_id'] !== $this->auth->getUserId() && !$this->auth->isAdmin())) {
            $this->redirect('/events');
        }

        $errors = $this->validateRequest($_POST, [
            'name'         => 'required|min:3|max:100',
            'description'  => 'required|min:10|max:1000',
            'event_date'   => 'required|date|future_date',
            'location'     => 'required|min:3|max:255',
            'max_capacity' => 'required|integer|positive',
        ]);

        if (empty($errors)) {
            if ($this->eventModel->update($eventId, $_POST)) {
                $this->redirect('/events');
            } else {
                $errors['general'] = 'Failed to update event';
            }
        }

        $this->render('events/edit', [
            'event'  => array_merge($event, $_POST),
            'errors' => $errors,
        ]);
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/events');
        }

        $eventId = $_POST['id'] ?? null;
        if (!$eventId) {
            $this->redirect('/events');
        }

        $event = $this->eventModel->findById($eventId);
        if (!$event || ($event['user_id'] !== $this->auth->getUserId() && !$this->auth->isAdmin())) {
            $this->redirect('/events');
        }

        if ($this->eventModel->delete($eventId)) {
            // Success message could be added here
            $this->redirect('/events');
        } else {
            // Error message could be added here
            $this->redirect('/events');
        }
    }

    public function view(): void
    {
        $eventId = $_GET['id'] ?? null;
        if (!$eventId) {
            $this->redirect('/events');
        }

        $event = $this->eventModel->findById($eventId);
        if (!$event) {
            $this->redirect('/events');
        }

        // Get attendee count for the event
        $attendeeCount = $this->eventModel->getAttendeeCount($eventId);

        $this->render('events/view', [
            'event'         => $event,
            'attendeeCount' => $attendeeCount,
            'isAdmin'       => $this->auth->isAdmin(),
            'userId'        => $this->auth->getUserId(),
            'hasCapacity'   => $this->eventModel->hasCapacity($eventId),
            'title'         => 'View Event',
        ]);
    }

    public function api(): void
    {
        // Check for API authentication (you might want to use API keys or tokens)
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
        if (!$apiKey || !$this->validateApiKey($apiKey)) {
            $this->json(['error' => 'Invalid or missing API key'], 401);
        }

        $eventId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        // Handle different HTTP methods
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if ($eventId) {
                    $this->getEventDetails($eventId);
                } else {
                    $this->getEventsList();
                }
                break;

            default:
                $this->json(['error' => 'Method not allowed'], 405);
        }
    }

    private function getEventDetails(int $eventId): void
    {
        $event = $this->eventModel->findById($eventId);

        if (!$event) {
            $this->json(['error' => 'Event not found'], 404);
        }

        // Get additional event information
        $attendeeCount = $this->eventModel->getAttendeeCount($eventId);
        $hasCapacity   = $this->eventModel->hasCapacity($eventId);

        // Format the response
        $response = [
            'id'                => $event['id'],
            'name'              => $event['name'],
            'description'       => $event['description'],
            'event_date'        => $event['event_date'],
            'location'          => $event['location'],
            'max_capacity'      => $event['max_capacity'],
            'current_attendees' => $attendeeCount,
            'has_capacity'      => $hasCapacity,
            'created_at'        => $event['created_at'],
            'updated_at'        => $event['updated_at'],
        ];

        $this->json($response);
    }

    private function getEventsList(): void
    {
        // Get query parameters
        $page    = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $perPage = filter_input(INPUT_GET, 'per_page', FILTER_VALIDATE_INT) ?: 10;

        // Get filter parameters
        $filters = [
            'search'    => $_GET['search'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to'   => $_GET['date_to'] ?? '',
            'location'  => $_GET['location'] ?? '',
        ];

        // Get events with pagination
        $events = $this->eventModel->getFilteredAndSorted(
            $filters,
            'event_date',
            'ASC',
            $perPage,
            ($page - 1) * $perPage
        );

        $total = $this->eventModel->countFiltered($filters);

        // Format the response
        $response = [
            'data' => array_map(function ($event) {
                return [
                    'id'           => $event['id'],
                    'name'         => $event['name'],
                    'description'  => $event['description'],
                    'event_date'   => $event['event_date'],
                    'location'     => $event['location'],
                    'max_capacity' => $event['max_capacity'],
                ];
            }, $events),
            'meta' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'total_pages'  => ceil($total / $perPage),
            ],
        ];

        $this->json($response);
    }

    private function validateApiKey(string $apiKey): bool
    {
        // In a real application, you would validate against a database of API keys
        // For this example, we'll use a simple environment variable
        $validApiKey = getenv('API_KEY') ?: 'your-secret-api-key';
        return hash_equals($validApiKey, $apiKey);
    }
}
