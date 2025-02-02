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
            $value      = $data[$field] ?? '';

            foreach ($fieldRules as $rule) {
                // Required field validation
                if ($rule === 'required' && empty($value)) {
                    $errors[$field] = ucfirst($field) . ' is required';
                    break; // Skip other validations if field is required but empty
                }

                // Skip other validations if field is empty and not required
                if (empty($value) && $rule !== 'required') {
                    continue;
                }

                // Minimum length validation
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = ucfirst($field) . ' must be at least ' . $min . ' characters';
                    }
                }

                // Maximum length validation
                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = ucfirst($field) . ' must not exceed ' . $max . ' characters';
                    }
                }

                // Email validation
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'Invalid email format';
                }

                // Numeric validation
                if ($rule === 'numeric' && !is_numeric($value)) {
                    $errors[$field] = ucfirst($field) . ' must be a number';
                }

                // Date validation
                if ($rule === 'date' && !strtotime($value)) {
                    $errors[$field] = ucfirst($field) . ' must be a valid date';
                }

                // Future date validation
                if ($rule === 'future_date' && strtotime($value) < strtotime('today')) {
                    $errors[$field] = ucfirst($field) . ' must be a future date';
                }

                // Integer validation
                if ($rule === 'integer' && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $errors[$field] = ucfirst($field) . ' must be an integer';
                }

                // Positive number validation
                if ($rule === 'positive' && (!is_numeric($value) || $value <= 0)) {
                    $errors[$field] = ucfirst($field) . ' must be a positive number';
                }
            }
        }
        return $errors;
    }
}
