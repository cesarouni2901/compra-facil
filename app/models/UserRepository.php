<?php
/** Repositorio de archivo JSON protegido: no requiere servidor SQL ni extensiones PDO. */
class UserRepository
{
    private string $filePath;

    public function __construct(private Security $security)
    {
        $this->filePath = dirname(__DIR__) . '/private/users.json';
    }

    /** Cifra campos identificables antes de guardarlos y guarda clave con hash seguro. */
    public function create(array $user): int
    {
        return $this->modify(function (array &$records) use ($user): int {
            $emailLookup = $this->security->lookupHash($user['email']);
            $documentLookup = $this->security->lookupHash($user['document']);
            $usernameLookup = $this->security->lookupHash($user['username']);
            foreach ($records as $record) {
                if (hash_equals($record['email_lookup'], $emailLookup) || hash_equals($record['document_lookup'], $documentLookup)) {
                    throw new DomainException('Ya existe una cuenta con ese correo o documento.');
                }
                if (isset($record['username_lookup']) && hash_equals($record['username_lookup'], $usernameLookup)) {
                    throw new DomainException('Ese nombre de usuario ya está ocupado. Elige otro.');
                }
            }

            $id = 1;
            foreach ($records as $record) $id = max($id, ((int) $record['id']) + 1);
            $records[] = [
                'id' => $id,
                'role' => $user['role'],
                'username_lookup' => $usernameLookup,
                'username_cipher' => $this->security->encrypt($user['username']),
                'email_lookup' => $emailLookup,
                'email_cipher' => $this->security->encrypt($user['email']),
                'first_name_cipher' => $this->security->encrypt($user['first_name']),
                'last_name_cipher' => $this->security->encrypt($user['last_name']),
                'document_lookup' => $documentLookup,
                'document_cipher' => $this->security->encrypt($user['document']),
                'birth_date_cipher' => $this->security->encrypt($user['birth_date']),
                'phone_cipher' => $this->security->encrypt($user['phone'] ?? ''),
                'business_cipher' => $this->security->encrypt($user['business'] ?? ''),
                'password_hash' => password_hash($user['password'], PASSWORD_DEFAULT),
                'created_at' => date(DATE_ATOM),
            ];
            return $id;
        });
    }

    /** Localiza por correo o usuario y verifica la contraseña con password_verify(). */
    public function authenticate(string $identity, string $password): ?array
    {
        $records = $this->read();
        $identityLookup = $this->security->lookupHash($identity);
        foreach ($records as $record) {
            $matchesEmail = hash_equals($record['email_lookup'], $identityLookup);
            $matchesUsername = isset($record['username_lookup']) && hash_equals($record['username_lookup'], $identityLookup);
            if (($matchesEmail || $matchesUsername) && password_verify($password, $record['password_hash'])) {
                return [
                    'id' => (int) $record['id'],
                    'role' => $record['role'],
                    'username' => isset($record['username_cipher']) ? $this->security->decrypt($record['username_cipher']) : $this->security->decrypt($record['first_name_cipher']),
                    'display_name' => $this->security->decrypt($record['first_name_cipher']),
                    'is_adult' => AccountRules::isAdult($this->security->decrypt($record['birth_date_cipher'])),
                ];
            }
        }
        return null;
    }

    /** Devuelve los campos descifrados del dueño de la sesión para editar su perfil. */
    public function profile(int $id): ?array
    {
        foreach ($this->read() as $record) {
            if ((int) $record['id'] !== $id) continue;
            return [
                'role' => $record['role'],
                'username' => isset($record['username_cipher']) ? $this->security->decrypt($record['username_cipher']) : '',
                'first_name' => $this->security->decrypt($record['first_name_cipher']),
                'last_name' => $this->security->decrypt($record['last_name_cipher']),
                'document' => $this->security->decrypt($record['document_cipher']),
                'email' => $this->security->decrypt($record['email_cipher']),
                'phone' => $this->security->decrypt($record['phone_cipher']),
                'business' => $this->security->decrypt($record['business_cipher']),
            ];
        }
        return null;
    }

    /** Actualiza datos de perfil tras comprobar que usuario, correo y documento sigan siendo únicos. */
    public function updateProfile(int $id, array $user): void
    {
        $this->modify(function (array &$records) use ($id, $user): void {
            $targetIndex = null;
            $emailLookup = $this->security->lookupHash($user['email']);
            $documentLookup = $this->security->lookupHash($user['document']);
            $usernameLookup = $this->security->lookupHash($user['username']);
            foreach ($records as $index => $record) {
                if ((int) $record['id'] === $id) {
                    $targetIndex = $index;
                    continue;
                }
                if (hash_equals($record['email_lookup'], $emailLookup) || hash_equals($record['document_lookup'], $documentLookup)
                    || (isset($record['username_lookup']) && hash_equals($record['username_lookup'], $usernameLookup))) {
                    throw new DomainException('Ese usuario, correo o documento ya pertenece a otra cuenta.');
                }
            }
            if ($targetIndex === null) throw new DomainException('No se encontró la cuenta que quieres editar.');

            $records[$targetIndex]['username_lookup'] = $usernameLookup;
            $records[$targetIndex]['username_cipher'] = $this->security->encrypt($user['username']);
            $records[$targetIndex]['email_lookup'] = $emailLookup;
            $records[$targetIndex]['email_cipher'] = $this->security->encrypt($user['email']);
            $records[$targetIndex]['document_lookup'] = $documentLookup;
            $records[$targetIndex]['document_cipher'] = $this->security->encrypt($user['document']);
            $records[$targetIndex]['first_name_cipher'] = $this->security->encrypt($user['first_name']);
            $records[$targetIndex]['last_name_cipher'] = $this->security->encrypt($user['last_name']);
            $records[$targetIndex]['phone_cipher'] = $this->security->encrypt($user['phone']);
            $records[$targetIndex]['business_cipher'] = $this->security->encrypt($user['business']);
        });
    }

    /** Lee el archivo con bloqueo compartido y valida que su estructura sea JSON de lista. */
    private function read(): array
    {
        if (!is_file($this->filePath)) return [];
        $handle = fopen($this->filePath, 'rb');
        if ($handle === false) throw new RuntimeException('No se pudo leer el archivo privado de cuentas.');
        try {
            if (!flock($handle, LOCK_SH)) throw new RuntimeException('No se pudo bloquear el archivo privado de cuentas.');
            $content = stream_get_contents($handle);
            $records = json_decode($content ?: '[]', true, 512, JSON_THROW_ON_ERROR);
            return is_array($records) ? $records : [];
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    /** Escribe una modificación bajo bloqueo exclusivo para no perder registros concurrentes. */
    private function modify(callable $callback): mixed
    {
        $lockPath = dirname($this->filePath) . '/.users.lock';
        $lock = fopen($lockPath, 'c');
        if ($lock === false) throw new RuntimeException('No se pudo abrir el bloqueo del archivo de cuentas.');
        try {
            if (!flock($lock, LOCK_EX)) throw new RuntimeException('No se pudo bloquear el archivo de cuentas.');
            $records = $this->read();
            $result = $callback($records);
            $json = json_encode($records, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            $temporaryPath = $this->filePath . '.tmp';
            if (file_put_contents($temporaryPath, $json, LOCK_EX) === false || !rename($temporaryPath, $this->filePath)) {
                throw new RuntimeException('No se pudieron guardar los datos privados de la cuenta.');
            }
            return $result;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
}
