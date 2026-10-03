<?php
require_once __DIR__ . '/app/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken((string) ($_POST['csrf_token'] ?? ''))) {
    $_SESSION = [];
    session_regenerate_id(true);
}
header('Location: index.html');
exit;
