<?php
/**
 * File: index.php
 * Deskripsi: Halaman utama yang mengatur redirect berdasarkan status login
 */

// 1. Mulai session
session_start();

// 2. Redirect berdasarkan status login
if (isset($_SESSION['user_id'])) {
    // Jika sudah login
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
} else {
    // Jika belum login
    header('Location: login.php');
}

// 3. Pastikan tidak ada output setelah redirect
exit();
?>