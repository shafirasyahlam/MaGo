<?php
// config/paths.php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$path = rtrim(str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__), '/');

define('BASE_URL', "$protocol://$host$path/");

function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}

function redirect($path) {
    header("Location: " . url($path));
    exit();
}
?>