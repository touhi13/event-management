<?php

class ApiController extends Controller
{
    public function getEvent()
    {
        $eventId = $_GET['id'] ?? null;
        if (!$eventId) {
            $this->jsonResponse(['error' => 'Event ID required'], 400);
            return;
        }

        $eventModel = new Event();
        $event      = $eventModel->findById($eventId);

        if (!$event) {
            $this->jsonResponse(['error' => 'Event not found'], 404);
            return;
        }

        $this->jsonResponse($event);
    }

    private function jsonResponse($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
    }
}
