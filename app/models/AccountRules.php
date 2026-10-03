<?php
/** Reglas de validación de edad, nombres, documentos y claves de acceso. */
class AccountRules
{
    /** Acepta nombres con letras Unicode y espacios; descarta dígitos y símbolos. */
    public static function validName(string $name): bool
    {
        return preg_match('/^[\p{L}\p{M} ]{2,60}$/u', trim($name)) === 1;
    }

    /** Usuario corto para mostrar en el menú y permitir iniciar sesión. */
    public static function validUsername(string $username): bool
    {
        return preg_match('/^[A-Za-z0-9_.-]{3,20}$/', $username) === 1;
    }

    /** Solo se admiten dígitos para el documento de identidad. */
    public static function validDocument(string $document): bool
    {
        return preg_match('/^[0-9]{5,20}$/', $document) === 1;
    }

    /** El teléfono conserva solo dígitos y puede incluir prefijo de país. */
    public static function validPhone(string $phone): bool
    {
        return preg_match('/^[0-9]{7,15}$/', $phone) === 1;
    }

    /** El límite incluye el día en que la persona cumple 18 años. */
    public static function isAdult(string $birthDate): bool
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $birthDate);
        $today = new DateTimeImmutable('today');
        return $date !== false && $date->format('Y-m-d') === $birthDate && $date <= $today && $date <= $today->modify('-18 years');
    }

    /** Clave de 8–12 caracteres con al menos una mayúscula y un símbolo. */
    public static function validPassword(string $password): bool
    {
        $length = mb_strlen($password, 'UTF-8');
        return $length >= 8 && $length <= 12
            && preg_match('/\p{Lu}/u', $password) === 1
            && preg_match('/[^\p{L}\p{N}\s]/u', $password) === 1;
    }
}
