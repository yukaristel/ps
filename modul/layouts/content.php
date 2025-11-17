<?php
/**
 * =============================================
 * CONTENT ROUTER
 * File: modul/layouts/content.php
 * =============================================
 * Routing semua halaman berdasarkan parameter ?page=
 */

$page = isset($_GET['page']) ? $_GET['page'] : 'login';

// Daftar halaman yang tersedia
$available_pages = [
    // Auth
    'login' => 'modul/auth/login.php',
    
    // Dashboard
    'dashboard' => 'modul/dashboard/dashboard.php',
    
    // PS Station
    'list_ps' => 'modul/ps_station/list_ps.php',
    'tambah_ps' => 'modul/ps_station/tambah_ps.php',
    'edit_ps' => 'modul/ps_station/edit_ps.php',
    
    // Member
    'list_member' => 'modul/member/list_member.php',
    'tambah_member' => 'modul/member/tambah_member.php',
    'edit_member' => 'modul/member/edit_member.php',
    'topup_member' => 'modul/member/topup_member.php',
    'history_topup' => 'modul/member/history_topup.php',
    
    // Pricing
    'list_pricing' => 'modul/pricing/list_pricing.php',
    'tambah_pricing' => 'modul/pricing/tambah_pricing.php',
    'edit_pricing' => 'modul/pricing/edit_pricing.php',
    
    // Rental
    'list_rental' => 'modul/rental/list_rental.php',
    'mulai_rental' => 'modul/rental/mulai_rental.php',
    'selesai_rental' => 'modul/rental/selesai_rental.php',
    'struk_rental' => 'modul/rental/struk_rental.php',
    
    // Laporan
    'laporan_harian' => 'modul/laporan/laporan_harian.php',
    'laporan_bulanan' => 'modul/laporan/laporan_bulanan.php',
];

// Cek apakah halaman ada
if (array_key_exists($page, $available_pages)) {
    $file = $available_pages[$page];
    
    // Cek apakah file exist
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<div class='alert alert-error'>";
        echo "<h3>File Not Found</h3>";
        echo "<p>File <strong>$file</strong> tidak ditemukan.</p>";
        echo "</div>";
    }
} else {
    // Default jika user sudah login
    if (isset($_SESSION['user_id'])) {
        include 'modul/dashboard/dashboard.php';
    } else {
        include 'modul/auth/login.php';
    }
}
?>