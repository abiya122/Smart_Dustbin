<?php
define('PAGE_TITLE', 'Analytics');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/access.php';
require __DIR__ . '/../includes/header.php';
?>
<div class="seg">
  <button class="seg-btn active" data-range="today">Today</button>
  <button class="seg-btn" data-range="week">This Week</button>
  <button class="seg-btn" data-range="month">This Month</button>
</div>

<div class="stat-row">
  <div class="stat-card">
    <span class="stat-label">Total Waste</span>
    <span class="stat-value" id="an-total">0</span>
    <span class="stat-sub">detections</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Wet Waste</span>
    <span class="stat-value stat-wet" id="an-wet">0</span>
    <span class="stat-sub">detections</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Dry Waste</span>
    <span class="stat-value stat-dry" id="an-dry">0</span>
    <span class="stat-sub">detections</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Wet / Dry Ratio</span>
    <span class="stat-value" id="an-ratio">0</span>
    <span class="stat-sub">wet per dry unit</span>
  </div>
</div>

<div class="grid-2">
  <section class="card">
    <div class="card-head"><h2 class="bar-wet">Wet vs Dry Waste</h2></div>
    <div class="chart-box"><canvas id="chart-distribution"></canvas></div>
  </section>
  <section class="card">
    <div class="card-head"><h2 class="bar-dry">Hourly Waste Statistics</h2></div>
    <div class="chart-box"><canvas id="chart-hourly"></canvas></div>
  </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<?php require __DIR__ . '/../includes/footer.php'; ?>