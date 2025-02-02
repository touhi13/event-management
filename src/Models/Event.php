<?php
namespace Models;

use Core\Model;

class Event extends Model
{
    protected string $table        = 'events';
    protected array $allowedFields = [
        'user_id',
        'name',
        'description',
        'event_date',
        'location',
        'max_capacity',
    ];

    public function findUpcoming(): array
    {
        return $this->executeQuery(
            "SELECT * FROM {$this->table}
            WHERE event_date > NOW()
            ORDER BY event_date ASC"
        )->fetchAll();
    }

    public function findByUser(int $userId): array
    {
        return $this->findAll(['user_id' => $userId]);
    }

    public function hasCapacity(int $eventId): bool
    {
        $stmt = $this->executeQuery(
            "SELECT e.max_capacity, COUNT(a.id) as current_attendees
            FROM {$this->table} e
            LEFT JOIN attendees a ON e.id = a.event_id
            WHERE e.id = ?
            GROUP BY e.id",
            [$eventId]
        );

        $result = $stmt->fetch();
        return $result && $result['current_attendees'] < $result['max_capacity'];
    }
}