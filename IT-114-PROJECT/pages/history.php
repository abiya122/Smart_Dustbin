<?php
define('PAGE_TITLE', 'Waste History');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/access.php';
require __DIR__ . '/../includes/header.php';
?>
<div class="filters">
  <select class="input" id="f-range">
    <option value="all">All Time</option>
    <option value="today">Today</option>
    <option value="week">This Week</option>
    <option value="month">This Month</option>
  </select>
  <select class="input" id="f-class">
    <option value="all">All Classification</option>
    <option value="WET">WET</option>
    <option value="DRY">DRY</option>
  </select>
  <input class="input search" type="text" id="f-q" placeholder="Search item…">
</div>

<div class="stat-row small">
  <div class="stat-card"><span class="stat-label">Records</span><span class="stat-value" id="h-summary-total">0</span></div>
  <div class="stat-card"><span class="stat-label">Wet</span><span class="stat-value stat-wet" id="h-summary-wet">0</span></div>
  <div class="stat-card"><span class="stat-label">Dry</span><span class="stat-value stat-dry" id="h-summary-dry">0</span></div>
</div>

<section class="card">
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr><th>Date</th><th>Time</th><th>Classification</th><th>Detected Item</th><th>Wet Cap.</th><th>Dry Cap.</th></tr>
      </thead>
      <tbody id="history-body">
        <tr><td colspan="6" class="empty">Loading…</td></tr>
      </tbody>
    </table>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>