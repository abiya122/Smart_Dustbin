<?php
// Entry point — displays the landing page (landing.php) before login.
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/access.php';

header('Location: ' . BASE_URL . '/landing.php');
exit;