<?php
// POST /logout — destroy session and return to landing.
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/access.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();
header('Location: ' . BASE_URL . '/landing.php');
exit;