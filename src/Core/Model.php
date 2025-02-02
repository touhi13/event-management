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
        $stmt->execute($params);
        return $stmt;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->executeQuery(
            "SELECT * FROM {$this->table} WHERE id = ?",
            [$id]
        );
        return $stmt->fetch() ?: null;
    }

    public function findAll(array $conditions = []): array
    {
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

        $stmt = $this->executeQuery($query, $params);
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $filteredData = array_intersect_key($data, array_flip($this->allowedFields));

        $columns = implode(', ', array_keys($filteredData));
        $values  = implode(', ', array_fill(0, count($filteredData), '?'));

        $query = "INSERT INTO {$this->table} ($columns) VALUES ($values)";

        return $this->executeQuery($query, array_values($filteredData))->rowCount() > 0;
    }

    public function update(int $id, array $data): bool
    {
        $filteredData = array_intersect_key($data, array_flip($this->allowedFields));

        $setClause = implode(', ', array_map(
            fn($field) => "$field = ?",
            array_keys($filteredData)
        ));

        $query = "UPDATE {$this->table} SET $setClause WHERE id = ?";

        return $this->executeQuery(
            $query,
            [ ...array_values($filteredData), $id]
        )->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        return $this->executeQuery(
            "DELETE FROM {$this->table} WHERE id = ?",
            [$id]
        )->rowCount() > 0;
    }
}