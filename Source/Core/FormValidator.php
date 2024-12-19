<?php
namespace Source\Core;

class FormValidator
{
    private $errors = [];

    /**
     * Valida se o campo é obrigatório.
     *
     * @param string $field O nome do campo.
     * @param string $value O valor do campo.
     * @return bool Retorna true se o campo não estiver vazio, false caso contrário.
     */
    public function validateRequired(string $field, string $value): bool
    {
        if (empty($value)) {
            $this->errors[$field] = "O campo $field é obrigatório.";
            return false;
        }
        return true;
    }

    /**
     * Valida se o email é válido.
     *
     * @param string $field O nome do campo.
     * @param string $email O valor do email.
     * @return bool Retorna true se o email for válido, false caso contrário.
     */
    public function validateEmail(string $field, string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "O campo $field deve conter um email válido.";
            return false;
        }
        return true;
    }

    /**
     * Valida se o campo tem o tamanho correto.
     *
     * @param string $field O nome do campo.
     * @param string $value O valor do campo.
     * @param int $min O tamanho mínimo permitido.
     * @param int $max O tamanho máximo permitido.
     * @return bool Retorna true se o valor estiver dentro do tamanho permitido, false caso contrário.
     */
    public function validateLength(string $field, string $value, int $min, int $max): bool
    {
        $length = strlen($value);
        if ($length < $min || $length > $max) {
            $this->errors[$field] = "O campo $field deve ter entre $min e $max caracteres.";
            return false;
        }
        return true;
    }

    /**
     * Retorna os erros de validação.
     *
     * @return array Um array contendo os erros de validação.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Verifica se existem erros de validação.
     *
     * @return bool Retorna true se existirem erros, false caso contrário.
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}

