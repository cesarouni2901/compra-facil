<?php
/** Cifrado autenticado de datos personales y funciones para formularios seguros. */
class Security
{
    private string $encryptionKey;
    private string $lookupKey;

    public function __construct(string $hexKey)
    {
        $masterKey = ctype_xdigit($hexKey) ? hex2bin($hexKey) : false;
        if ($masterKey === false || strlen($masterKey) !== 32) {
            throw new RuntimeException('La clave de cifrado debe tener exactamente 64 caracteres hexadecimales.');
        }
        // Deriva claves independientes para cifrar campos y generar índices de búsqueda.
        $this->encryptionKey = hash_hmac('sha256', 'comprafacil-encryption-v1', $masterKey, true);
        $this->lookupKey = hash_hmac('sha256', 'comprafacil-lookup-v1', $masterKey, true);
    }

    /** Cifra texto con AES-256-GCM; el IV aleatorio y la etiqueta detectan alteraciones. */
    public function encrypt(string $plainText): string
    {
        $iv = random_bytes(12);
        $tag = '';
        $cipherText = openssl_encrypt($plainText, 'aes-256-gcm', $this->encryptionKey, OPENSSL_RAW_DATA, $iv, $tag, '', 16);
        if ($cipherText === false) throw new RuntimeException('No se pudo cifrar un dato personal.');
        return base64_encode($iv . $tag . $cipherText);
    }

    /** Descifra y autentica un valor creado por encrypt(). */
    public function decrypt(string $encodedValue): string
    {
        $payload = base64_decode($encodedValue, true);
        if ($payload === false || strlen($payload) < 28) throw new RuntimeException('Dato personal cifrado inválido.');
        $plainText = openssl_decrypt(substr($payload, 28), 'aes-256-gcm', $this->encryptionKey, OPENSSL_RAW_DATA, substr($payload, 0, 12), substr($payload, 12, 16));
        if ($plainText === false) throw new RuntimeException('No se pudo autenticar el dato personal.');
        return $plainText;
    }

    /** Permite buscar correo/documento sin guardar esos valores en claro. */
    public function lookupHash(string $value): string
    {
        return hash_hmac('sha256', mb_strtolower(trim($value), 'UTF-8'), $this->lookupKey);
    }
}
