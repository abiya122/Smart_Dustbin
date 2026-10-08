<?php
define('PAGE_TITLE', 'Alerts');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/access.php';
require __DIR__ . '/../includes/header.php';
?>
<div class="filters">
  <div class="seg">
    <button class="seg-btn active" data-status="all">All</button>
    <button class="seg-btn" data-status="unread">Unread</button>
    <button class="seg-btn" data-status="read">Read</button>
  </div>
  <button class="btn btn-outline" id="mark-all">Mark all read</button>
</div>

<section class="card">
  <div id="alerts-list" class="alerts-list">
    <p class="empty">Loading…</p>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>