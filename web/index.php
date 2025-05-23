<?php
/**
 * WordPress View Bootstrapper
 * 
 * This file loads the WordPress environment and template.
 * It's the entry point for all requests in a Bedrock-style setup.
 */

// Define absolute path to WordPress installation directory
define('WP_USE_THEMES', true);

// Check if WordPress is installed via Composer
if (file_exists(__DIR__ . '/wp/wp-blog-header.php')) {
    // Load WordPress from wp directory (Composer install)
    require(__DIR__ . '/wp/wp-blog-header.php');
} elseif (file_exists(__DIR__ . '/wp-blog-header.php')) {
    // Fallback: Load WordPress from current directory (standard install)
    require(__DIR__ . '/wp-blog-header.php');
} else {
    // WordPress not found - show friendly error
    http_response_code(503);
    echo '<!DOCTYPE html>
<html>
<head>
    <title>WordPress Not Found</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 50px; }
        .error { background: #f9f9f9; border-left: 4px solid #dc3232; padding: 20px; margin: 20px 0; }
        code { background: #f1f1f1; padding: 2px 4px; font-family: Consolas, Monaco, monospace; }
    </style>
</head>
<body>
    <h1>WordPress Installation Not Found</h1>
    <div class="error">
        <p><strong>WordPress core files are missing.</strong></p>
        <p>Please run <code>composer install</code> in the web directory to install WordPress and dependencies.</p>
        <p>Expected location: <code>' . __DIR__ . '/wp/wp-blog-header.php</code></p>
    </div>
</body>
</html>';
    exit;
}