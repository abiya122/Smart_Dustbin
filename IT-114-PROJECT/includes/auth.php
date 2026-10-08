<?php
// Auth guard + session helpers. Include this in every protected page.
require_once __DIR__ . '/access.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

bootstrap_admin();
require_login();

$CURRENT_USER = [
    'id'       => $_SESSION['user_id'],
    'username' => $_SESSION['username'] ?? 'user',
    'role'     => $_SESSION['role'] ?? 'staff',
];