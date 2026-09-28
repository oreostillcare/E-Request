<?php
require_once '../../../init/model/bootstrap.php';
session_start();
session_unset();
session_destroy();

$cookie = session_get_cookie_params();
setcookie(session_name(), '', [
    'expires' => time() - 3600,
    'path' => $cookie['path'],
    'domain' => $cookie['domain'],
    'secure' => $cookie['secure'],
    'httponly' => $cookie['httponly'],
    'samesite' => $cookie['samesite'] ?? 'Lax',
]);

header('location:../../index.php');
exit;
?>
