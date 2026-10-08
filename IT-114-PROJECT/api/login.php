<?php
// POST /login — authenticate user, create session.
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/access.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

bootstrap_admin();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$data = body();
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if ($username === '' || $password === '') {
    json_response(['error' => 'Please enter username and password.'], 422);
}

$stmt = db()->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    json_response(['error' => 'Invalid username or password.'], 401);
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

json_response(['success' => true, 'redirect' => BASE_URL . '/pages/dashboard.php']);