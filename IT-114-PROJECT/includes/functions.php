<?php
// Shared helper functions
require_once __DIR__ . '/../config/database.php';

function bootstrap_admin(): void
{
    $pdo = db();
    try {
        $count = $pdo->query("SELECT COUNT(*) AS c FROM users")->fetch()['c'];
    } catch (PDOException $e) {
        http_response_code(500);
        die('Database tables not found. Import <code>database/database.sql</code> first: ' . htmlspecialchars($e->getMessage()));
    }
    if ((int)$count === 0) {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
        $stmt->execute(['admin', password_hash('admin123', PASSWORD_BCRYPT)]);
    }
}

function get_setting(string $key, string $default = ''): string
{
    $pdo  = db();
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : $default;
}

function update_setting(string $key, string $value): void
{
    $pdo = db();
    $stmt = $pdo->prepare(
        "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
    );
    $stmt->execute([$key, $value]);
}

function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function body(): array
{
    // Supports JSON bodies, form posts, and GET (hardware) payloads.
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (is_array($json)) {
        return array_merge($_GET, $json);
    }
    return array_merge($_GET, $_POST);
}

function require_login(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(?string $token): bool
{
    return !empty($token) && hash_equals($_SESSION['csrf'] ?? '', $token);
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function time_ago(?string $datetime): string
{
    if (!$datetime) {
        return 'Never';
    }
    $then = strtotime($datetime);
    $diff = time() - $then;
    if ($diff < 60)       return 'Just now';
    if ($diff < 3600)     return floor($diff / 60) . ' min ago';
    if ($diff < 86400)    return floor($diff / 3600) . ' hr ago';
    return floor($diff / 86400) . ' days ago';
}