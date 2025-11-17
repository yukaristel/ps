<?php
/**
 * =============================================
 * RENTAL PLAYSTATION - MAIN ROUTER
 * File: index.php
 * =============================================
 */

// Start session
session_start();

// Include database config
require_once 'config/database.php';

// Cek apakah user sudah login (kecuali halaman login)
$page = isset($_GET['page']) ? $_GET['page'] : 'login';

// Halaman yang tidak perlu login
$public_pages = ['login'];

if (!in_array($page, $public_pages)) {
    // Cek session login
    if (!isset($_SESSION['user_id'])) {
        redirect('index.php?page=login', 'Silakan login terlebih dahulu!', 'warning');
    }
}

// Load header
include 'modul/layouts/header.php';

// Routing content
include 'modul/layouts/content.php';

// Load footer
include 'modul/layouts/footer.php';
?>