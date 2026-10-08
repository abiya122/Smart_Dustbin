<?php
define('PAGE_TITLE', 'Dashboard');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/access.php';
require __DIR__ . '/../includes/header.php';
?>

<div class="stat-row">
  <div class="stat-card">
    <span class="stat-label">System Status</span>
    <span class="badge badge-online" id="sys-status-dot">● OFFLINE</span>
    <span class="stat-sub" id="sys-last-updated">—</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Current Classification</span>
    <span class="stat-value" id="sys-classification">—</span>
    <span class="stat-sub">LED indicator</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Today's Detections</span>
    <span class="stat-value" id="today-total">0</span>
    <span class="stat-sub"><span id="today-wet" class="text-wet">0</span> <span class="text-wet">wet</span> · <span id="today-dry" class="text-dry">0</span> <span class="text-dry">dry</span></span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Unread Alerts</span>
    <span class="stat-value" id="unread-alerts">0</span>
    <span class="stat-sub">latest notifications</span>
  </div>
</div>

<div class="grid-2">
  <section class="card">
    <div class="card-head">
      <h2 class="bar-wet">Wet Bin Capacity</h2>
      <span class="badge" id="wet-badge">—</span>
    </div>
    <div class="gauge" id="wet-gauge" data-level="0">
      <div class="gauge-fill"></div>
      <span class="gauge-text" id="wet-gauge-text">0%</span>
    </div>
  </section>

  <section class="card">
    <div class="card-head">
      <h2 class="bar-dry">Dry Bin Capacity</h2>
      <span class="badge" id="dry-badge">—</span>
    </div>
    <div class="gauge" id="dry-gauge" data-level="0">
      <div class="gauge-fill"></div>
      <span class="gauge-text" id="dry-gauge-text">0%</span>
    </div>
  </section>
</div>

<div class="grid-2">
  <section class="card">
    <div class="card-head">
      <h2>LED Indicators</h2>
    </div>
    <div class="led-row">
      <div class="led-item">
        <span class="led led-green" id="led-green"></span>
        <div>
          <strong>Green LED</strong>
          <span class="stat-sub">Dry waste detected</span>
        </div>
      </div>
      <div class="led-item">
        <span class="led led-yellow" id="led-yellow"></span>
        <div>
          <strong>Yellow LED</strong>
          <span class="stat-sub">Wet waste detected</span>
        </div>
      </div>
    </div>
  </section>

  <section class="card">
    <div class="card-head">
      <h2>Communication</h2>
    </div>
    <div class="comm-cell">
      <span class="dot dot-green" id="comm-dot"></span>
      <div>
        <strong id="comm-text">Pending connection</strong>
        <span class="stat-sub">Arduino → Backend link</span>
      </div>
    </div>
    <div class="comm-cell">
      <span class="dot" id="ard-dot"></span>
      <div>
        <strong id="ard-text">Pending status</strong>
        <span class="stat-sub">Arduino status</span>
      </div>
    </div>
  </section>
</div>

<section class="card">
  <div class="card-head">
    <h2>Recent Detections</h2>
    <a class="link" href="history.php">View all →</a>
  </div>
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr><th>Date</th><th>Time</th><th>Classification</th><th>Wet Cap.</th><th>Dry Cap.</th></tr>
      </thead>
      <tbody id="recent-body">
        <tr><td colspan="5" class="empty">Loading…</td></tr>
      </tbody>
    </table>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>