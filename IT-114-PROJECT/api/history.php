<?php
// GET /api/history — waste detection records with filters.
require_once __DIR__ . '/../includes/auth.php';

$pdo = db();

$range = $_GET['range'] ?? 'all';
$class = $_GET['classification'] ?? 'all';
$search = trim($_GET['q'] ?? '');

$where = [];
$params = [];

if ($range === 'today') {
    $where[] = 'w.date = CURDATE()';
} elseif ($range === 'week') {
    $where[] = 'w.date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)';
} elseif ($range === 'month') {
    $where[] = 'w.date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)';
}

if ($class === 'WET' || $class === 'DRY') {
    $where[] = 'w.classification = ?';
    $params[] = $class;
}

if ($search !== '') {
    $where[] = '(w.detected_item LIKE ? OR w.classification LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql = "SELECT w.id, w.classification, w.detected_item, w.wet_capacity, w.dry_capacity,
               DATE_FORMAT(w.date, '%m/%d/%y') AS date, DATE_FORMAT(w.time, '%h:%i %p') AS time
        FROM waste_records w";
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY w.created_at DESC, w.id DESC LIMIT 500';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$records = $stmt->fetchAll();

// Summary totals for the same filter set (separate stats query)
$sqlStats = "SELECT COUNT(*) AS total,
                    SUM(w.classification = 'WET') AS wet,
                    SUM(w.classification = 'DRY') AS dry
             FROM waste_records w";
if ($where) {
    $sqlStats .= ' WHERE ' . implode(' AND ', $where);
}
$stmt = $pdo->prepare($sqlStats);
$stmt->execute($params);
$stats = $stmt->fetch();

json_response([
    'records'   => $records,
    'summary'   => [
        'total' => (int)($stats['total'] ?? 0),
        'wet'   => (int)($stats['wet'] ?? 0),
        'dry'   => (int)($stats['dry'] ?? 0),
    ],
    'filters'   => ['range' => $range, 'classification' => $class, 'q' => $search],
]);