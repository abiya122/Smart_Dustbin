<?php
// GET /api/alerts — list alerts. POST /api/alerts — create alert (internal).
require_once __DIR__ . '/../includes/auth.php';

$pdo = db();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET') {
    $status = $_GET['status'] ?? 'all';
    $where = '';
    $params = [];
    if ($status === 'unread' || $status === 'read') {
        $where = 'WHERE status = ?';
        $params[] = $status;
    }
    $stmt = $pdo->prepare(
        "SELECT id, type, message, status, DATE_FORMAT(created_at, '%b %e, %Y %h:%i %p') AS created_at
         FROM alerts $where ORDER BY created_at DESC, id DESC LIMIT 100"
    );
    $stmt->execute($params);

    $unread = (int)$pdo->query("SELECT COUNT(*) AS c FROM alerts WHERE status = 'unread'")->fetch()['c'];

    json_response(['alerts' => $stmt->fetchAll(), 'unread' => $unread]);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $data = body();
    $type = $data['type'] ?? 'info';
    $message = trim($data['message'] ?? '');
    if (!in_array($type, ['critical', 'warning', 'system', 'info'], true) || $message === '') {
        json_response(['error' => 'Invalid alert payload.'], 422);
    }

    // Avoid duplicate alerts with identical message in the last 10 minutes
    $dup = $pdo->prepare("SELECT id FROM alerts WHERE message = ? AND created_at > (NOW() - INTERVAL 10 MINUTE) LIMIT 1");
    $dup->execute([$message]);
    if ($dup->fetch()) {
        json_response(['success' => true, 'duplicate' => true]);
    }

    $stmt = $pdo->prepare("INSERT INTO alerts (type, message) VALUES (?, ?)");
    $stmt->execute([$type, $message]);
    json_response(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
}

json_response(['error' => 'Method not allowed'], 405);