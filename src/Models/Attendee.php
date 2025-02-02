<?php
namespace Models;

use Core\Model;

class Attendee extends Model
{
    protected string $table        = 'attendees';
    protected array $allowedFields = [
        'event_id',
        'name',
        'email',
        'phone',
    ];

    public function findByEventId(int $eventId): array
    {
        return $this->findAll(['event_id' => $eventId]);
    }

    public function isAlreadyRegistered(int $eventId, string $email): bool
    {
        $stmt = $this->executeQuery(
            "SELECT COUNT(*) FROM {$this->table}
            WHERE event_id = ? AND email = ?",
            [$eventId, $email]
        );
        return (int) $stmt->fetchColumn() > 0;
    }

    public function getEventAttendeeCount(int $eventId): int
    {
        $stmt = $this->executeQuery(
            "SELECT COUNT(*) FROM {$this->table} WHERE event_id = ?",
            [$eventId]
        );
        return (int) $stmt->fetchColumn();
    }

    public function exportToCsv(int $eventId): string
    {
        $attendees = $this->findByEventId($eventId);
        $output    = fopen('php://temp', 'r+');

        // Add headers
        fputcsv($output, ['Name', 'Email', 'Phone', 'Registration Date']);

        // Add data
        foreach ($attendees as $attendee) {
            fputcsv($output, [
                $attendee['name'],
                $attendee['email'],
                $attendee['phone'],
                $attendee['registration_date'],
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}