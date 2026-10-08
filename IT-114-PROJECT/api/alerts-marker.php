<?php
// POST /api/alerts/mark-read — mark alerts as read.
require_once __DIR__ . '/../includes/auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}
$data = body();
$id = (int)($data['id'] ?? 0);

if ($id > 0) {
    $pdo = db();
    $stmt = $pdo->prepare("UPDATE alerts SET status = 'read' WHERE id = ?");
    $stmt->execute([$id]);
} else {
    db()->query("UPDATE alerts SET status = 'read' WHERE status = 'unread'");
}
json_response(['success' => true]);