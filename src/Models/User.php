<?php
namespace Models;

use Core\Model;

class User extends Model
{
    protected string $table        = 'users';
    protected array $allowedFields = [
        'username',
        'email',
        'password',
        'is_admin',
    ];

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->executeQuery(
            "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1",
            [$email]
        );
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return parent::create($data);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}