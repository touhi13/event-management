<?php
namespace Core;

abstract class Controller
{
    protected function render(string $template, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../../templates/$template.php";
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function validateRequest(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = "The $field field is required";
                continue;
            }

            if (strpos($rule, 'email') !== false) {
                if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Invalid email format";
                }
            }

            if (preg_match('/min:(\d+)/', $rule, $matches)) {
                $min = (int) $matches[1];
                if (strlen($data[$field]) < $min) {
                    $errors[$field] = "The $field must be at least $min characters";
                }
            }
        }

        return $errors;
    }
}