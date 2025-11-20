<?php

declare(strict_types=1);

namespace App\Services;

class Validator
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $value = is_string($value) ? trim($value) : $value;
            $rulesList = is_array($fieldRules) ? $fieldRules : explode('|', (string) $fieldRules);

            foreach ($rulesList as $rule) {
                if ($rule === 'required' && self::isEmpty($value)) {
                    $errors[$field][] = 'This field is required.';
                }

                if ($rule === 'string' && !is_string($value)) {
                    $errors[$field][] = 'Must be text.';
                }

                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (is_string($value) && strlen($value) < $min) {
                        $errors[$field][] = "Must be at least {$min} characters.";
                    }
                }

                if ($rule === 'email' && !self::validEmail($value)) {
                    $errors[$field][] = 'Please provide a valid email address.';
                }

                if ($rule === 'boolean' && !is_bool(filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) {
                    $errors[$field][] = 'Must be true or false.';
                }

                if (str_starts_with($rule, 'in:')) {
                    $choices = explode(',', substr($rule, 3));
                    if (!in_array((string) $value, $choices, true)) {
                        $errors[$field][] = 'Contains an unexpected value.';
                    }
                }
            }
        }

        return $errors;
    }

    public static function sanitize(array $data): array
    {
        $clean = [];
        foreach ($data as $key => $value) {
            $clean[$key] = self::sanitizeValue($value);
        }

        return $clean;
    }

    private static function sanitizeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map([self::class, 'sanitizeValue'], $value);
        }

        if (is_numeric($value)) {
            return $value + 0;
        }

        return trim(strip_tags((string) $value));
    }

    private static function isEmpty(mixed $value): bool
    {
        return $value === null || $value === '' || (is_array($value) && $value === []);
    }

    private static function validEmail(mixed $value): bool
    {
        if (self::isEmpty($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
