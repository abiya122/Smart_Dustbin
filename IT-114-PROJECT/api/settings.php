<?php
// GET /api/settings — system info + thresholds.
// POST /api/settings — save thresholds (auth required).
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET') {
    $pdo = db();
    $status = $pdo->query("SELECT * FROM system_status WHERE id = 1")->fetch();

    json_response([
        'system' => [
            'arduino_status'       => $status['arduino_status'] ?? 'offline',
            'communication_status' => $status['communication_status'] ?? 'disconnected',
            'last_updated'         => $status['last_updated'] ?? null,
            'last_updated_ago'     => time_ago($status['last_updated'] ?? null),
            'wet_capacity'         => (float)($status['wet_capacity'] ?? 0),
            'dry_capacity'         => (float)($status['dry_capacity'] ?? 0),
            'version'              => APP_VERSION,
        ],
        'alerts' => [
            'warning_threshold'  => (int)get_setting('warning_threshold', '80'),
            'critical_threshold' => (int)get_setting('critical_threshold', '90'),
        ],
        'user'  => ['id' => $_SESSION['user_id'], 'username' => $_SESSION['username'], 'role' => $_SESSION['role']],
    ]);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $data = body();
    if (!csrf_check($data['_token'] ?? null)) {
        json_response(['error' => 'Invalid CSRF token.'], 403);
    }
    $warning = (int)($data['warning_threshold'] ?? 80);
    $critical = (int)($data['critical_threshold'] ?? 90);

    $warning = max(0, min(100, $warning));
    $critical = max(0, min(100, $critical));
    if ($critical < $warning) {
        json_response(['error' => 'Critical must be greater than or equal to Warning.'], 422);
    }

    update_setting('warning_threshold', (string)$warning);
    update_setting('critical_threshold', (string)$critical);
    json_response(['success' => true, 'warning_threshold' => $warning, 'critical_threshold' => $critical]);
}

json_response(['error' => 'Method not allowed'], 405);