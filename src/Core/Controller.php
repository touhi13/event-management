<?php
namespace Core;

abstract class Controller
{
    protected function render(string $template, array $data = []): void
    {
        // Always get the authenticated user
        $auth = Auth::getInstance();
        $user = $auth->getUser();

        // Merge user data with other view data
        $data = array_merge(['user' => $user], $data);

        // Extract data to make variables available in template
        extract($data);

        // Include the template
        require_once __DIR__ . "/../../templates/$template.php";
    }

    protected function redirect(string $path): void
    {
        header("Location: " . BASE_PATH . $path);
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
        foreach ($rules as $field => $fieldRules) {
            $fieldRules = explode('|', $fieldRules);
            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && empty($data[$field])) {
                    $errors[$field] = ucfirst($field) . ' is required';
                }
                if (strpos($rule, 'min:') === 0) {
                    $min = substr($rule, 4);
                    if (strlen($data[$field]) < $min) {
                        $errors[$field] = ucfirst($field) . ' must be at least ' . $min . ' characters';
                    }
                }
                if ($rule === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'Invalid email format';
                }
            }
        }
        return $errors;
    }
}
