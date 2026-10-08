<?php
// Defines BASE_URL, $PAGE_TITLE, $ACTIVE_PAGE used by header/footer.
require_once __DIR__ . '/functions.php';

// Compute application base url.
// Works whether the app sits under the document root, inside a sub-folder,
// or is mapped via an Apache Alias.
$appRootLocal = str_replace('\\', '/', realpath(__DIR__ . '/..'));
$docRoot      = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');

if ($docRoot !== '' && strpos($appRootLocal, $docRoot) === 0) {
    // App is inside the document root (e.g. htdocs/smart-dustbin)
    $base = substr($appRootLocal, strlen($docRoot));
} else {
    // App is served outside the document root (Apache Alias / built-in server).
    // Derive the base from the request URI by matching the app folder name.
    $script  = rtrim(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''), '/');
    $parts   = explode('/', trim($script, '/'));
    $appName = basename($appRootLocal);
    $idx     = $parts ? array_search($appName, $parts) : false;
    $base    = $idx !== false ? '/' . implode('/', array_slice($parts, 0, $idx + 1)) : '';
}

if (!defined('BASE_URL')) {
    define('BASE_URL', $base);
}

defined('PAGE_TITLE') or define('PAGE_TITLE', 'Dashboard');

$ACTIVE_PAGE = strtolower(pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME));
$PAGE_TITLE  = PAGE_TITLE;