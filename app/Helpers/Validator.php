<?php
namespace App\Helpers;

class Validator {
    private array $errors = [];
    private array $data;

    public function __construct(array $data) { $this->data = $data; }

    public function required(string $field, string $label = ''): self {
        if (empty(trim($this->data[$field] ?? ''))) {
            $this->errors[$field] = "{$label} est obligatoire.";
        }
        return $this;
    }

    public function email(string $field, string $label = ''): self {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "{$label} n'est pas un email valide.";
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $label = ''): self {
        if (!empty($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->errors[$field] = "{$label} doit contenir au moins {$min} caractères.";
        }
        return $this;
    }

    public function maxLength(string $field, int $max, string $label = ''): self {
        if (!empty($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->errors[$field] = "{$label} ne doit pas dépasser {$max} caractères.";
        }
        return $this;
    }

    public function numeric(string $field, string $label = ''): self {
        if (!empty($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = "{$label} doit être numérique.";
        }
        return $this;
    }

    public function errors(): array { return $this->errors; }
    public function fails(): bool { return !empty($this->errors); }
    public function firstError(): ?string { return !empty($this->errors) ? reset($this->errors) : null; }
}
