<?php

namespace App\Services;

class Validator
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = trim($data[$field] ?? '');
            $rulesList = explode('|', $fieldRules);

            foreach ($rulesList as $rule) {
                if ($rule === 'required' && $value === '') {
                    $errors[$field][] = 'This field is required.';
                }

                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = "Must be at least {$min} characters.";
                    }
                }

                if ($rule === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'Please provide a valid email address.';
                }
            }
        }

        return $errors;
    }
}
