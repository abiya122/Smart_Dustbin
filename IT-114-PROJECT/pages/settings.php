<?php
define('PAGE_TITLE', 'Settings');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/access.php';
require __DIR__ . '/../includes/header.php';
$csrf = csrf_token();
?>

<div class="grid-2">
  <section class="card">
    <div class="card-head"><h2 class="bar-info">System Information</h2></div>
    <dl class="kv">
      <div><dt>Arduino Status</dt><dd id="st-arduino">—</dd></div>
      <div><dt>Communication</dt><dd id="st-comm">—</dd></div>
      <div><dt>Last Update</dt><dd id="st-last">—</dd></div>
      <div><dt>Wet Bin</dt><dd id="st-wet" class="text-wet">—</dd></div>
      <div><dt>Dry Bin</dt><dd id="st-dry" class="text-dry">—</dd></div>
      <div><dt>System Version</dt><dd><?= e(APP_VERSION) ?></dd></div>
    </dl>
  </section>

  <section class="card">
    <div class="card-head"><h2 class="bar-wet">Alert Settings</h2></div>
    <form id="settings-form" class="form">
      <input type="hidden" name="_token" value="<?= e($csrf) ?>">
      <label class="field">
        <span>Wet/ Dry Bin Warning Level (%)</span>
        <input type="number" name="warning_threshold" min="0" max="100" value="80" required>
      </label>
      <label class="field">
        <span>Wet/Dry Bin Critical Level (%)</span>
        <input type="number" name="critical_threshold" min="0" max="100" value="90" required>
      </label>
      <p class="hint">0–79% normal · 80%+ warning · 90%+ critical.</p>
      <button class="btn btn-primary" type="submit">Save Settings</button>
      <span class="form-success" id="settings-msg" hidden></span>
    </form>
  </section>
</div>

<section class="card">
  <div class="card-head"><h2 class="bar-info">Appearance</h2></div>
  <p class="hint appearance-hint">Choose a theme for the dashboard.</p>
  <div class="seg">
    <button type="button" class="seg-btn" data-theme-option="light"><i class="fa fa-sun"></i> Light</button>
    <button type="button" class="seg-btn" data-theme-option="dark"><i class="fa fa-moon"></i> Dark</button>
  </div>
</section>

<section class="card">
  <div class="card-head"><h2>Account</h2></div>
  <dl class="kv">
    <div><dt>Username</dt><dd><?= e($CURRENT_USER['username']) ?></dd></div>
    <div><dt>Role</dt><dd><span class="role-badge"><?= e($CURRENT_USER['role']) ?></span></dd></div>
  </dl>
  <a class="btn btn-outline danger" href="<?= BASE_URL ?>/api/logout.php">Logout</a>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>