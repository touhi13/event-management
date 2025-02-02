<?php
namespace Models;

use Core\Model;
use PDO;

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

    public function getAttendeeCount(int $eventId): int
    {
        $stmt = $this->executeQuery(
            "SELECT COUNT(*) as count
            FROM attendees
            WHERE event_id = ?",
            [$eventId]
        );

        $result = $stmt->fetch();
        return (int) $result['count'];
    }

    public function search($query, $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM events WHERE
                name LIKE :query OR
                description LIKE :query OR
                location LIKE :query
                ORDER BY event_date DESC
                LIMIT :limit OFFSET :offset";

        $stmt        = $this->db->prepare($sql);
        $searchQuery = "%{$query}%";
        $stmt->bindParam(':query', $searchQuery);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function count(): int
    {
        $stmt   = $this->executeQuery("SELECT COUNT(*) as total FROM {$this->table}");
        $result = $stmt->fetch();
        return (int) $result['total'];
    }

    public function getAll(int $limit = 10, int $offset = 0): array
    {
        return $this->executeQuery(
            "SELECT * FROM {$this->table}
            ORDER BY event_date DESC
            LIMIT ? OFFSET ?",
            [$limit, $offset]
        )->fetchAll();
    }

    public function getFilteredAndSorted(
        array $filters = [],
        string $sortBy = 'created_at',
        string $sortOrder = 'DESC',
        int $limit = 10,
        int $offset = 0
    ): array {
        $allowedSortFields = ['name', 'event_date', 'location', 'max_capacity', 'created_at'];
        $sortBy            = in_array($sortBy, $allowedSortFields) ? $sortBy : 'created_at';
        $sortOrder         = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

        $query = "SELECT e.*, u.username as organizer_name
                 FROM {$this->table} e
                 LEFT JOIN users u ON e.user_id = u.id
                 WHERE 1=1";

        $params = [];

        if (!empty($filters['search'])) {
            $query .= " AND (e.name LIKE :search1 OR e.description LIKE :search2 OR e.location LIKE :search3)";
            $searchTerm         = "%{$filters['search']}%";
            $params[':search1'] = $searchTerm;
            $params[':search2'] = $searchTerm;
            $params[':search3'] = $searchTerm;
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND e.event_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND e.event_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        if (!empty($filters['location'])) {
            $query .= " AND e.location = :location";
            $params[':location'] = $filters['location'];
        }

        $query .= " ORDER BY e.$sortBy $sortOrder LIMIT :limit OFFSET :offset";
        $params[':limit']  = $limit;
        $params[':offset'] = $offset;

        return $this->executeQuery($query, $params)->fetchAll();
    }

    public function getLocations(): array
    {
        return $this->executeQuery(
            "SELECT DISTINCT location FROM {$this->table} ORDER BY location"
        )->fetchAll(PDO::FETCH_COLUMN);
    }

    public function countFiltered(array $filters = []): int
    {
        $query  = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $query .= " AND (name LIKE ? OR description LIKE ? OR location LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params     = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND event_date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND event_date <= ?";
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['location'])) {
            $query .= " AND location = ?";
            $params[] = $filters['location'];
        }

        $result = $this->executeQuery($query, $params)->fetch();
        return (int) $result['total'];
    }

    public function getLatestEvents(int $limit = 5): array
    {
        return $this->findLatest($limit, 'event_date');
    }

    public function getUpcomingEvents(int $limit = 10): array
    {
        $query = "SELECT * FROM {$this->table}
                 WHERE event_date > NOW()
                 ORDER BY event_date ASC
                 LIMIT ?";

        return $this->executeQuery($query, [$limit])->fetchAll();
    }

    public function getPaginatedEvents(
        int $page = 1,
        int $perPage = 10,
        array $conditions = []
    ): array {
        return $this->findPaginated(
            $page,
            $perPage,
            'created_at',
            'DESC',
            $conditions
        );
    }
}
