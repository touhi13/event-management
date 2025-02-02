<?php
namespace Core;

abstract class Model
{
    protected \PDO $db;
    protected string $table;
    protected array $allowedFields = [];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    protected function executeQuery(string $query, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($query);

        // Bind parameters with their appropriate types
        foreach ($params as $key => $value) {
            $paramType = match (true) {
                is_int($value) => \PDO::PARAM_INT,
                is_bool($value) => \PDO::PARAM_BOOL,
                is_null($value) => \PDO::PARAM_NULL,
                default => \PDO::PARAM_STR
            };

            // If using positional parameters (?)
            if (is_int($key)) {
                $stmt->bindValue($key + 1, $value, $paramType);
            } else {
                // If using named parameters (:param)
                $stmt->bindValue($key, $value, $paramType);
            }
        }

        $stmt->execute();
        return $stmt;
    }

    public function findById(int $id): ?array
    {
        return $this->executeQuery(
            "SELECT * FROM {$this->table} WHERE id = :id",
            [':id' => $id]
        )->fetch() ?: null;
    }

    public function findAll(array $conditions = []): array
    {
        $query  = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $key => $value) {
                $paramName          = ":$key";
                $whereClause[]      = "$key = $paramName";
                $params[$paramName] = $value;
            }
            $query .= " WHERE " . implode(' AND ', $whereClause);
        }

        return $this->executeQuery($query, $params)->fetchAll();
    }

    public function create(array $data): bool
    {
        $filteredData = array_intersect_key($data, array_flip($this->allowedFields));

        if (empty($filteredData)) {
            return false;
        }

        $columns      = implode(', ', array_keys($filteredData));
        $params       = array_map(fn($key) => ":$key", array_keys($filteredData));
        $placeholders = implode(', ', $params);

        $query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        $bindParams = array_combine($params, array_values($filteredData));

        return $this->executeQuery($query, $bindParams)->rowCount() > 0;
    }

    public function update(int $id, array $data): bool
    {
        $filteredData = array_intersect_key($data, array_flip($this->allowedFields));

        if (empty($filteredData)) {
            return false;
        }

        $setClause = implode(', ', array_map(
            fn($field) => "$field = :$field",
            array_keys($filteredData)
        ));

        $query = "UPDATE {$this->table} SET $setClause WHERE id = :id";

        $params = array_combine(
            array_map(fn($key) => ":$key", array_keys($filteredData)),
            array_values($filteredData)
        );
        $params[':id'] = $id;

        return $this->executeQuery($query, $params)->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        return $this->executeQuery(
            "DELETE FROM {$this->table} WHERE id = :id",
            [':id' => $id]
        )->rowCount() > 0;
    }

    public function findLatest(
        int $limit = 10,
        string $orderBy = 'id',
        string $direction = 'DESC'
    ): array {
        $allowedDirections = ['ASC', 'DESC'];
        $direction         = in_array(strtoupper($direction), $allowedDirections)
        ? strtoupper($direction)
        : 'DESC';

        $query = "SELECT * FROM {$this->table}
                 ORDER BY {$orderBy} {$direction}
                 LIMIT ?";

        return $this->executeQuery($query, [$limit])->fetchAll();
    }

    public function findAllOrdered(
        string $orderBy = 'id',
        string $direction = 'DESC',
        array $conditions = []
    ): array {
        $allowedDirections = ['ASC', 'DESC'];
        $direction         = in_array(strtoupper($direction), $allowedDirections)
        ? strtoupper($direction)
        : 'DESC';

        $query  = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $key => $value) {
                $whereClause[] = "$key = ?";
                $params[]      = $value;
            }
            $query .= " WHERE " . implode(' AND ', $whereClause);
        }

        $query .= " ORDER BY {$orderBy} {$direction}";

        return $this->executeQuery($query, $params)->fetchAll();
    }

    public function findPaginated(
        int $page = 1,
        int $perPage = 10,
        string $orderBy = 'id',
        string $direction = 'DESC',
        array $conditions = []
    ): array {
        $allowedDirections = ['ASC', 'DESC'];
        $direction         = in_array(strtoupper($direction), $allowedDirections)
        ? strtoupper($direction)
        : 'DESC';

        $offset = ($page - 1) * $perPage;

        $query  = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $key => $value) {
                $whereClause[] = "$key = ?";
                $params[]      = $value;
            }
            $query .= " WHERE " . implode(' AND ', $whereClause);
        }

        $query .= " ORDER BY {$orderBy} {$direction}
                   LIMIT ? OFFSET ?";

        $params[] = $perPage;
        $params[] = $offset;

        $items = $this->executeQuery($query, $params)->fetchAll();

        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM {$this->table}";
        if (!empty($conditions)) {
            $countQuery .= " WHERE " . implode(' AND ', $whereClause);
        }

        $total = (int) $this->executeQuery(
            $countQuery,
            array_slice($params, 0, -2)
        )->fetch()['total'];

        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => ceil($total / $perPage),
        ];
    }
}
