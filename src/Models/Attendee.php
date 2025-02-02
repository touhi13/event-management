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
        return (bool) $this->executeQuery(
            "SELECT COUNT(*) FROM {$this->table}
             WHERE event_id = :event_id AND email = :email",
            [':event_id' => $eventId, ':email' => $email]
        )->fetchColumn();
    }

    public function getEventAttendeeCount(int $eventId): int
    {
        return (int) $this->executeQuery(
            "SELECT COUNT(*) FROM {$this->table} WHERE event_id = :event_id",
            [':event_id' => $eventId]
        )->fetchColumn();
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

    public function getFilteredAndSorted(
        int $eventId,
        array $filters = [],
        string $sortBy = 'registration_date',
        string $sortOrder = 'DESC',
        int $limit = 10,
        int $offset = 0
    ): array {
        $allowedSortFields = ['name', 'email', 'phone', 'registration_date'];
        $sortBy            = in_array($sortBy, $allowedSortFields) ? $sortBy : 'registration_date';
        $sortOrder         = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

        $query = "SELECT a.*, e.name as event_name
                 FROM {$this->table} a
                 LEFT JOIN events e ON a.event_id = e.id
                 WHERE a.event_id = :event_id";

        $params = [':event_id' => $eventId];

        if (!empty($filters['search'])) {
            $query .= " AND (a.name LIKE :search1 OR a.email LIKE :search2 OR a.phone LIKE :search3)";
            $searchTerm         = "%{$filters['search']}%";
            $params[':search1'] = $searchTerm;
            $params[':search2'] = $searchTerm;
            $params[':search3'] = $searchTerm;
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND a.registration_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND a.registration_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $query .= " ORDER BY a.$sortBy $sortOrder LIMIT :limit OFFSET :offset";
        $params[':limit']  = $limit;
        $params[':offset'] = $offset;

        return $this->executeQuery($query, $params)->fetchAll();
    }

    public function countFiltered(int $eventId, array $filters = []): int
    {
        $query  = "SELECT COUNT(*) as total FROM {$this->table} WHERE event_id = :event_id";
        $params = [':event_id' => $eventId];

        if (!empty($filters['search'])) {
            $query .= " AND (name LIKE :search1 OR email LIKE :search2 OR phone LIKE :search3)";
            $searchTerm         = "%{$filters['search']}%";
            $params[':search1'] = $searchTerm;
            $params[':search2'] = $searchTerm;
            $params[':search3'] = $searchTerm;
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND registration_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND registration_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $result = $this->executeQuery($query, $params)->fetch();
        return (int) $result['total'];
    }
}
