<?php
// GET /api/analytics?range=today|week|month — statistics & charts.
require_once __DIR__ . '/../includes/auth.php';

$pdo = db();
$range = $_GET['range'] ?? 'today';
if (!in_array($range, ['today', 'week', 'month'], true)) {
    $range = 'today';
}

switch ($range) {
    case 'week':
        $where = 'date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)';
        break;
    case 'month':
        $where = 'date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)';
        break;
    default:
        $where = 'date = CURDATE()';
}

// Totals
$totals = $pdo->query(
    "SELECT COUNT(*) AS total,
            SUM(classification = 'WET') AS wet,
            SUM(classification = 'DRY') AS dry
     FROM waste_records WHERE $where"
)->fetch();
$total = (int)($totals['total'] ?? 0);
$wet   = (int)($totals['wet'] ?? 0);
$dry   = (int)($totals['dry'] ?? 0);
$ratio = $dry > 0 ? round($wet / $dry, 2) : ($wet > 0 ? (float)$wet : 0);

// Wet vs dry distribution
$distribution = [['classification' => 'WET', 'count' => $wet], ['classification' => 'DRY', 'count' => $dry]];

// Hourly statistics for today
$hourly = $pdo->query(
    "SELECT HOUR(time) AS hour,
            SUM(classification = 'WET') AS wet,
            SUM(classification = 'DRY') AS dry,
            COUNT(*) AS total
     FROM waste_records
     WHERE date = CURDATE()
     GROUP BY HOUR(time)
     ORDER BY hour ASC"
)->fetchAll();

json_response([
    'range'        => $range,
    'totals'       => ['total' => $total, 'wet' => $wet, 'dry' => $dry, 'ratio' => $ratio],
    'distribution' => $distribution,
    'hourly'       => $hourly,
]);