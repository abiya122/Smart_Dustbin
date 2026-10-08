<?php
// Common layout header + sidebar. Expects $CURRENT_USER and $ACTIVE_PAGE.
require_once __DIR__ . '/access.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($PAGE_TITLE ?? 'Dashboard') ?> · <?= e(APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
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
<body class="page-<?= e($ACTIVE_PAGE ?? 'dashboard') ?>">
<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon"><img class="brand-logo" src="<?= BASE_URL ?>/assets/images/smart_logo.png" alt="Smart Dustbin"></div>
    </div>

    <nav class="sidebar-nav">
      <a href="<?= BASE_URL ?>/pages/dashboard.php" class="nav-link <?= $ACTIVE_PAGE === 'dashboard' ? 'active' : '' ?>">
        <span class="nav-ico"><i class="icomoon icon-table-2"></i></span> Dashboard
      </a>
      <a href="<?= BASE_URL ?>/pages/analytics.php" class="nav-link <?= $ACTIVE_PAGE === 'analytics' ? 'active' : '' ?>">
        <span class="nav-ico"><i class="fa fa-chart-bar"></i></span> Analytics
      </a>
      <a href="<?= BASE_URL ?>/pages/history.php" class="nav-link <?= $ACTIVE_PAGE === 'history' ? 'active' : '' ?>">
        <span class="nav-ico"><i class="glyphicon glyphicon-time"></i></span> Waste History
      </a>
      <a href="<?= BASE_URL ?>/pages/alerts.php" class="nav-link <?= $ACTIVE_PAGE === 'alerts' ? 'active' : '' ?>">
        <span class="nav-ico"><i class="fa fa-bell"></i></span> Alerts
      </a>
      <a href="<?= BASE_URL ?>/pages/settings.php" class="nav-link <?= $ACTIVE_PAGE === 'settings' ? 'active' : '' ?>">
        <span class="nav-ico"><i class="glyphicon glyphicon-cog"></i></span> Settings
      </a>
    </nav>
  </aside>

  <main class="main">
    <header class="topbar">
      <h1 class="page-title"><?= e($PAGE_TITLE ?? 'Dashboard') ?></h1>
      <div class="topbar-right">
        <span class="live-dot" id="live-dot"></span>
        <span class="live-text" id="live-text">Checking…</span>
        <button type="button" class="btn-theme" id="btn-theme" title="Toggle dark / light mode" aria-label="Toggle dark / light mode">
          <i class="material-icons" id="theme-icon">dark_mode</i>
        </button>
        <div class="user-chip">
          <span class="avatar"><?= e(strtoupper(substr($CURRENT_USER['username'], 0, 1))) ?></span>
          <span><?= e($CURRENT_USER['username']) ?></span>
          <span class="role-badge"><?= e($CURRENT_USER['role']) ?></span>
        </div>
        <a href="<?= BASE_URL ?>/api/logout.php" class="btn-logout"><i class="material-icons">logout</i> Logout</a>
      </div>
    </header>
    <div class="content">