<?php
require_once __DIR__ . '/includes/functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
bootstrap_admin();
require_once __DIR__ . '/includes/access.php';

// Already logged in? Go straight to dashboard.
if (!empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login · <?= e(APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <script>
    (function () {
      var t = localStorage.getItem('sd-theme');
      if (t === 'dark' || (!t && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.setAttribute('data-theme', 'dark');
      }
    })();
  </script>
</head>
<body class="login-body">
  <button type="button" class="btn-theme login-theme-fixed" id="btn-theme" title="Toggle dark / light mode" aria-label="Toggle dark / light mode">
    <i class="material-icons" id="theme-icon">dark_mode</i>
  </button>
  <div class="login-layout">
    <div class="login-image">
      <img src="<?= BASE_URL ?>/assets/images/bin.png?v=<?= filemtime(__DIR__ . '/assets/images/bin.png') ?>" alt="Smart Dustbin">
    </div>
    <div class="login-card">
    <div class="login-icon"><img class="login-logo" src="<?= BASE_URL ?>/assets/images/smart_logo.png" alt="Smart Dustbin"></div>
    <p class="login-sub">· Sign in to continue</p>

    <form id="login-form" autocomplete="off">
      <label class="field">
        <span>Username / Email</span>
        <input type="text" name="username" placeholder="Enter username" required>
      </label>
      <label class="field">
        <span>Password</span>
        <input type="password" name="password" placeholder="Enter password" required>
      </label>

      <div class="form-error" id="login-error" hidden></div>

      <button type="submit" class="btn btn-primary btn-block" id="login-btn">Login</button>
    </form>
  </div>
  </div>

  <script>
    window.BASE_URL = <?= json_encode(BASE_URL) ?>;
  </script>
  <script src="<?= BASE_URL ?>/assets/js/app.js?v=<?= filemtime(__DIR__ . '/assets/js/app.js') ?>"></script>
</body>
</html>