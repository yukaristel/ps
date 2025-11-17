<?php
/**
 * =============================================
 * PROSES AUTENTIKASI (LOGIN & LOGOUT)
 * File: config/auth_proses.php
 * =============================================
 */

session_start();
require_once 'database.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// =============================================
// PROSES LOGIN
// =============================================
if ($action == 'login') {
    
    // Ambil data dari form
    $username = cleanInput($_POST['username']);
    $password = $_POST['password'];
    
    // Validasi input kosong
    if (empty($username) || empty($password)) {
        redirect('../index.php?page=login', 'Username dan password harus diisi!', 'error');
    }
    
    // Cari user berdasarkan username
    $query = "SELECT * FROM users WHERE username = '" . escape($username) . "' LIMIT 1";
    $result = query($query);
    
    // Cek apakah user ditemukan
    if (count($result) > 0) {
        $user = $result[0];
        
        // Verifikasi password
        if ($password === $user['password']) {
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_time'] = time();
            
            // Update last login (optional - bisa ditambahkan kolom di database)
            
            // Redirect ke dashboard
            redirect('../index.php?page=dashboard', 'Selamat datang, ' . $user['nama_lengkap'] . '!', 'success');
            
        } else {
            // Password salah
            redirect('../index.php?page=login', 'Password salah!', 'error');
        }
        
    } else {
        // Username tidak ditemukan
        redirect('../index.php?page=login', 'Username tidak ditemukan!', 'error');
    }
}

// =============================================
// PROSES LOGOUT
// =============================================
else if ($action == 'logout') {
    
    // Hapus semua session
    session_unset();
    session_destroy();
    
    // Redirect ke login
    redirect('../index.php?page=login', 'Anda telah logout.', 'info');
}

// =============================================
// ACTION TIDAK VALID
// =============================================
else {
    redirect('../index.php?page=login', 'Action tidak valid!', 'error');
}
?>