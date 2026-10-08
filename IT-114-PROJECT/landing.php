<?php
// Public landing page — displayed before the login page.
require_once __DIR__ . '/includes/functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/access.php';

$isLoggedIn = !empty($_SESSION['user_id']);
$ctaUrl     = $isLoggedIn ? BASE_URL . '/pages/dashboard.php' : BASE_URL . '/login.php';
$ctaLabel   = $isLoggedIn ? 'Go to Dashboard' : 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e(APP_NAME) ?> · Smart Dustbin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
<body class="landing-body">
  <header class="landing-nav">
    <div class="landing-brand">
      <img src="<?= BASE_URL ?>/assets/images/smart_logo.png" alt="Smart Dustbin">
    </div>
    <nav class="landing-links">
      <button type="button" class="btn-theme" id="btn-theme" title="Toggle dark / light mode" aria-label="Toggle dark / light mode">
        <i class="material-icons" id="theme-icon">dark_mode</i>
      </button>
      <a class="btn btn-primary" href="<?= e($ctaUrl) ?>"><?= e($ctaLabel) ?></a>
    </nav>
  </header>

  <main class="landing-main">
    <section class="hero">
      <div class="hero-media">
        <img class="hero-bg" src="<?= BASE_URL ?>/assets/images/login_img.jpeg" alt="Smart Dustbin illustration">
      </div>
      <div class="hero-content">
        <h1>Smart Dustbin <span>Monitoring System</span></h1>
        <p>A real-time WET and DRY waste classification system powered by Arduino — monitor bin capacity,
           LED indicators, waste history and alerts from one clean dashboard.</p>

        <div class="hero-cta">
          <a class="btn btn-primary btn-lg" href="#features"><i class="fa-regular fa-circle-play"></i> Explore Features</a>
        </div>

        <div class="hero-chips">
          <span class="hero-chip"><span class="dot dot-green"></span> Live Arduino feed</span>
          <span class="hero-chip"><span class="dot dot-amber"></span> WET / DRY sorting</span>
          <span class="hero-chip"><span class="dot" style="background:var(--accent)"></span> Analytics &amp; alerts</span>
        </div>
      </div>
    </section>

    <section id="features" class="features">
      <article class="feature">
        <span class="feature-ico i-info"><i class="fa-solid fa-gauge-high"></i></span>
        <h3>Live Monitoring</h3>
        <p>Watch wet and dry bin capacity, LED states and the Arduino link — refreshed automatically every few seconds.</p>
      </article>
      <article class="feature">
        <span class="feature-ico i-wet"><i class="fa-solid fa-droplet"></i></span>
        <h3>WET / DRY Sorting</h3>
        <p>Moisture-based classification with a green LED for DRY and a yellow LED for WET, logged with time and item name.</p>
      </article>
      <article class="feature">
        <span class="feature-ico i-accent"><i class="fa-solid fa-bell"></i></span>
        <h3>Alerts &amp; Analytics</h3>
        <p>Configurable warning and critical thresholds, searchable waste history and today / week / month statistics.</p>
      </article>
    </section>

    <section id="how" class="how">
      <div class="how-head">
        <h2>How it works</h2>
        <p>From sensor to dashboard in three simple steps</p>
      </div>
      <div class="steps">
        <div class="step">
          <span class="step-num">1</span>
          <strong>Sense</strong>
          <span>Ultrasonic and moisture sensors capture the waste and bin levels.</span>
        </div>
        <div class="step">
          <span class="step-num">2</span>
          <strong>Classify</strong>
          <span>The Arduino flags it DRY (green LED) or WET (yellow LED).</span>
        </div>
        <div class="step">
          <span class="step-num">3</span>
          <strong>Monitor</strong>
          <span>Records sync to the dashboard for history, stats and alerts.</span>
        </div>
      </div>
    </section>
  </main>

  <footer class="landing-foot">
    © <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.
  </footer>

  <script>
    window.BASE_URL = <?= json_encode(BASE_URL) ?>;
  </script>
  <script src="<?= BASE_URL ?>/assets/js/app.js?v=<?= filemtime(__DIR__ . '/assets/js/app.js') ?>"></script>
</body>
</html>
