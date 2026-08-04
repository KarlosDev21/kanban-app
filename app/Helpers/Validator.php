<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Validações reutilizáveis para inputs de formulários.
 * Uso encadeado (fluent interface):
 *
 *   $validator = new Validator();
 *   $validator->required($name, 'name', 'nome')->maxLength($name, 100, 'name', 'nome');
 *   if ($validator->fails()) { ... $validator->errors() ... }
 */
final class Validator
{
    private array $errors = [];

    public function required(?string $value, string $field, string $label): self
    {
        if ($value === null || trim($value) === '') {
            $this->errors[$field] = "O campo {$label} é obrigatório.";
        }
        return $this;
    }

    public function email(?string $value, string $field = 'email', string $label = 'e-mail'): self
    {
        if ($value !== null && trim($value) !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "Informe um {$label} válido.";
        }
        return $this;
    }

    public function minLength(?string $value, int $min, string $field, string $label): self
    {
        if ($value !== null && mb_strlen($value) < $min) {
            $this->errors[$field] = "O campo {$label} deve ter no mínimo {$min} caracteres.";
        }
        return $this;
    }

    public function maxLength(?string $value, int $max, string $field, string $label): self
    {
        if ($value !== null && mb_strlen($value) > $max) {
            $this->errors[$field] = "O campo {$label} deve ter no máximo {$max} caracteres.";
        }
        return $this;
    }

    public function matches(?string $value, ?string $other, string $field, string $message): self
    {
        if ($value !== $other) {
            $this->errors[$field] = $message;
        }
        return $this;
    }

    public function in(?string $value, array $allowed, string $field, string $label): self
    {
        if ($value !== null && !in_array($value, $allowed, true)) {
            $this->errors[$field] = "Valor inválido para {$label}.";
        }
        return $this;
    }

    public function fails(): bool
    {
        return count($this->errors) > 0;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}