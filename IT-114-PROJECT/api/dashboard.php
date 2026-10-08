<?php
// GET /api/dashboard — live monitoring data.
require_once __DIR__ . '/../includes/auth.php';

$pdo = db();

// Live system status (single row)
$status = $pdo->query("SELECT * FROM system_status WHERE id = 1")->fetch();
if (!$status) {
    $pdo->exec("INSERT INTO system_status (id) VALUES (1)");
    $status = $pdo->query("SELECT * FROM system_status WHERE id = 1")->fetch();
}

// Recent detections (last 8)
$recent = $pdo->query(
    "SELECT id, classification, detected_item, wet_capacity, dry_capacity,
            DATE_FORMAT(date, '%m/%d/%y') AS date, DATE_FORMAT(time, '%h:%i %p') AS time
     FROM waste_records
     ORDER BY created_at DESC, id DESC
     LIMIT 8"
)->fetchAll();

// Count today's detections
$todayStats = $pdo->query(
    "SELECT COUNT(*) AS total,
            SUM(classification = 'WET') AS wet,
            SUM(classification = 'DRY') AS dry
     FROM waste_records WHERE date = CURDATE()"
)->fetch();

$unreadAlerts = (int)$pdo->query("SELECT COUNT(*) AS c FROM alerts WHERE status = 'unread'")->fetch()['c'];

// Thresholds
$warning = (int)get_setting('warning_threshold', '80');
$critical = (int)get_setting('critical_threshold', '90');

$wetLevel = (float)($status['wet_capacity'] ?? 0);
$dryLevel = (float)($status['dry_capacity'] ?? 0);

$wetState = $wetLevel >= $critical ? 'critical' : ($wetLevel >= $warning ? 'warning' : 'normal');
$dryState = $dryLevel >= $critical ? 'critical' : ($dryLevel >= $warning ? 'warning' : 'normal');

json_response([
    'system' => [
        'arduino_status'        => $status['arduino_status'],
        'communication_status'  => $status['communication_status'],
        'last_updated'          => $status['last_updated'],
        'last_updated_ago'      => time_ago($status['last_updated']),
        'current_classification'=> $status['current_classification'],
        'green_led'             => $status['green_led'],
        'yellow_led'            => $status['yellow_led'],
    ],
    'bins' => [
        'wet' => ['level' => $wetLevel, 'state' => $wetState, 'threshold_warning' => $warning, 'threshold_critical' => $critical],
        'dry' => ['level' => $dryLevel, 'state' => $dryState, 'threshold_warning' => $warning, 'threshold_critical' => $critical],
    ],
    'today' => [
        'total' => (int)($todayStats['total'] ?? 0),
        'wet'   => (int)($todayStats['wet'] ?? 0),
        'dry'   => (int)($todayStats['dry'] ?? 0),
    ],
    'recent' => $recent,
    'unread_alerts' => $unreadAlerts,
]);