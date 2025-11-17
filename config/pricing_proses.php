<?php
/**
 * =============================================
 * PROSES CRUD PRICING
 * File: config/pricing_proses.php
 * =============================================
 */

session_start();
require_once 'database.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    redirect('../index.php?page=login', 'Silakan login terlebih dahulu!', 'warning');
}

// Cek role admin
if ($_SESSION['role'] != 'admin') {
    redirect('../index.php?page=dashboard', 'Akses ditolak!', 'error');
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// =============================================
// CREATE - TAMBAH PRICING BARU
// =============================================
if ($action == 'create') {
    
    $tipe = cleanInput($_POST['tipe']);
    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
    
    // Validasi tipe
    if (empty($tipe) || !in_array($tipe, ['reguler', 'member'])) {
        redirect('../index.php?page=tambah_pricing', 'Tipe tidak valid!', 'error');
    }
    
    if ($tipe == 'reguler') {
        // PRICING REGULER
        $harga_per_jam = (float)$_POST['harga_per_jam'];
        
        if (empty($harga_per_jam) || $harga_per_jam <= 0) {
            redirect('../index.php?page=tambah_pricing', 'Harga per jam harus diisi!', 'error');
        }
        
        $query = "INSERT INTO pricing (tipe, harga_per_jam, is_active) 
                  VALUES ('reguler', $harga_per_jam, $is_active)";
        
    } else {
        // PRICING MEMBER (PAKET)
        $paket_jam = (int)$_POST['paket_jam'];
        $paket_harga = (float)$_POST['paket_harga'];
        
        if (empty($paket_jam) || $paket_jam <= 0 || empty($paket_harga) || $paket_harga <= 0) {
            redirect('../index.php?page=tambah_pricing', 'Paket jam dan harga harus diisi!', 'error');
        }
        
        // Cek apakah paket jam sudah ada
        $cek = query("SELECT * FROM pricing WHERE tipe = 'member' AND paket_jam = $paket_jam");
        if (count($cek) > 0) {
            redirect('../index.php?page=tambah_pricing', 'Paket ' . $paket_jam . ' jam sudah ada!', 'error');
        }
        
        $query = "INSERT INTO pricing (tipe, paket_jam, paket_harga, is_active) 
                  VALUES ('member', $paket_jam, $paket_harga, $is_active)";
    }
    
    if (execute($query)) {
        redirect('../index.php?page=list_pricing', 'Pricing berhasil ditambahkan!', 'success');
    } else {
        redirect('../index.php?page=tambah_pricing', 'Gagal menambahkan pricing!', 'error');
    }
}

// =============================================
// UPDATE - EDIT PRICING
// =============================================
else if ($action == 'update') {
    
    $id = (int)$_POST['id'];
    $tipe = cleanInput($_POST['tipe']);
    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
    
    if ($tipe == 'reguler') {
        // UPDATE REGULER
        $harga_per_jam = (float)$_POST['harga_per_jam'];
        
        if (empty($harga_per_jam) || $harga_per_jam <= 0) {
            redirect('../index.php?page=edit_pricing&id=' . $id, 'Harga per jam harus diisi!', 'error');
        }
        
        $query = "UPDATE pricing SET 
                  harga_per_jam = $harga_per_jam,
                  is_active = $is_active
                  WHERE id = $id";
        
    } else {
        // UPDATE MEMBER
        $paket_jam = (int)$_POST['paket_jam'];
        $paket_harga = (float)$_POST['paket_harga'];
        
        if (empty($paket_jam) || $paket_jam <= 0 || empty($paket_harga) || $paket_harga <= 0) {
            redirect('../index.php?page=edit_pricing&id=' . $id, 'Paket jam dan harga harus diisi!', 'error');
        }
        
        // Cek apakah paket jam sudah digunakan paket lain
        $cek = query("SELECT * FROM pricing WHERE tipe = 'member' AND paket_jam = $paket_jam AND id != $id");
        if (count($cek) > 0) {
            redirect('../index.php?page=edit_pricing&id=' . $id, 'Paket ' . $paket_jam . ' jam sudah digunakan!', 'error');
        }
        
        $query = "UPDATE pricing SET 
                  paket_jam = $paket_jam,
                  paket_harga = $paket_harga,
                  is_active = $is_active
                  WHERE id = $id";
    }
    
    if (execute($query)) {
        redirect('../index.php?page=list_pricing', 'Pricing berhasil diupdate!', 'success');
    } else {
        redirect('../index.php?page=edit_pricing&id=' . $id, 'Gagal mengupdate pricing!', 'error');
    }
}

// =============================================
// DELETE - HAPUS PRICING (OPSIONAL)
// =============================================
else if ($action == 'delete') {
    
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Cek apakah pricing pernah digunakan
    $cek_rental = query("SELECT * FROM rentals WHERE harga_per_jam IN (SELECT harga_per_jam FROM pricing WHERE id = $id) LIMIT 1");
    $cek_topup = query("SELECT * FROM member_topups WHERE paket_harga IN (SELECT paket_harga FROM pricing WHERE id = $id) LIMIT 1");
    
    if (count($cek_rental) > 0 || count($cek_topup) > 0) {
        redirect('../index.php?page=list_pricing', 'Pricing pernah digunakan, tidak bisa dihapus! Nonaktifkan saja.', 'warning');
    }
    
    $query = "DELETE FROM pricing WHERE id = $id";
    
    if (execute($query)) {
        redirect('../index.php?page=list_pricing', 'Pricing berhasil dihapus!', 'success');
    } else {
        redirect('../index.php?page=list_pricing', 'Gagal menghapus pricing!', 'error');
    }
}

// =============================================
// ACTION TIDAK VALID
// =============================================
else {
    redirect('../index.php?page=list_pricing', 'Action tidak valid!', 'error');
}
?>