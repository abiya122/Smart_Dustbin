<?php
// POST /api/arduino/data — entry point for the ESP8266/Arduino hardware.
//
// Expected JSON/URL-encoded payload:
//   classification  : "WET" | "DRY"
//   detected_item   : optional label (e.g. "banana peel")
//   wet_capacity    : wet bin fill % (0 - 100)
//   dry_capacity    : dry bin fill % (0 - 100)
//   green_led       : "on" | "off"
//   yellow_led      : "on" | "off"
//
// SCRATCH: Because hardware units use GET + a shared secret instead of
// session cookies, we authenticate with a device key (query param `key`).
require_once __DIR__ . '/../../includes/functions.php';

define('DEVICE_KEY', 'SMARTDUSTBIN2026'); // change this to match your Arduino sketch

$key = trim($_GET['key'] ?? $_POST['key'] ?? '');
if ($key !== DEVICE_KEY) {
    json_response(['error' => 'Unauthorized'], 401);
}

$data = body();

$classification = strtoupper(trim($data['classification'] ?? ''));
if (!in_array($classification, ['WET', 'DRY'], true)) {
    json_response(['error' => 'classification must be WET or DRY'], 422);
}

$detectedItem = trim($data['detected_item'] ?? '') ?: null;
$wetCap   = isset($data['wet_capacity']) ? round((float)$data['wet_capacity'], 1) : null;
$dryCap   = isset($data['dry_capacity']) ? round((float)$data['dry_capacity'], 1) : null;
$greenLed = ($data['green_led'] ?? '') === 'on' ? 'on' : 'off';
$yellowLed = ($data['yellow_led'] ?? '') === 'on' ? 'on' : 'off';

$dryCap = $dryCap === null ? null : max(0, min(100, $dryCap));
$wetCap = $wetCap === null ? null : max(0, min(100, $wetCap));

$pdo = db();

// 1. Save waste detection record
$stmt = $pdo->prepare(
    "INSERT INTO waste_records (classification, detected_item, wet_capacity, dry_capacity, date, time)
     VALUES (?, ?, ?, ?, CURDATE(), CURTIME())"
);
$stmt->execute([$classification, $detectedItem, $wetCap, $dryCap]);

// 2. Update live system status
$stmt = $pdo->prepare(
    "UPDATE system_status SET
        arduino_status = 'online',
        communication_status = 'connected',
        wet_capacity = ?,
        dry_capacity = ?,
        current_classification = ?,
        green_led = ?,
        yellow_led = ?,
        last_updated = NOW(),
        status_count = status_count + 1
     WHERE id = 1"
);
$stmt->execute([$wetCap, $dryCap, $classification, $greenLed, $yellowLed]);

// 3. Generate alerts based on thresholds
$warning = (int)get_setting('warning_threshold', '80');
$critical = (int)get_setting('critical_threshold', '90');

$alerts = [];
if ($wetCap !== null && $wetCap >= $critical) {
    $alerts[] = ['critical', "Wet Waste Bin is {$wetCap}% full. Please empty the compartment."];
} elseif ($wetCap !== null && $wetCap >= $warning) {
    $alerts[] = ['warning', "Wet Waste Bin is {$wetCap}% full."];
}
if ($dryCap !== null && $dryCap >= $critical) {
    $alerts[] = ['critical', "Dry Waste Bin is {$dryCap}% full. Please empty the compartment."];
} elseif ($dryCap !== null && $dryCap >= $warning) {
    $alerts[] = ['warning', "Dry Waste Bin is {$dryCap}% full."];
}

foreach ($alerts as [$type, $message]) {
    $dup = $pdo->prepare("SELECT id FROM alerts WHERE message = ? AND created_at > (NOW() - INTERVAL 30 MINUTE) LIMIT 1");
    $dup->execute([$message]);
    if (!$dup->fetch()) {
        $ins = $pdo->prepare("INSERT INTO alerts (type, message) VALUES (?, ?)");
        $ins->execute([$type, $message]);
    }
}

json_response([
    'success'   => true,
    'saved'     => true,
    'record_id' => (int)$pdo->lastInsertId(),
    'alerts_created' => count($alerts),
]);