<?php
/**
 * =============================================
 * PROSES CRUD PS STATION
 * File: config/ps_station_proses.php
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
// CREATE - TAMBAH PS BARU
// =============================================
if ($action == 'create') {
    
    $nomor_ps = cleanInput($_POST['nomor_ps']);
    $status = cleanInput($_POST['status']);
    
    // Validasi
    if (empty($nomor_ps) || empty($status)) {
        redirect('../index.php?page=tambah_ps', 'Semua field harus diisi!', 'error');
    }
    
    // Cek apakah nomor PS sudah ada
    $cek = query("SELECT * FROM ps_stations WHERE nomor_ps = '" . escape($nomor_ps) . "'");
    if (count($cek) > 0) {
        redirect('../index.php?page=tambah_ps', 'Nomor PS sudah terdaftar!', 'error');
    }
    
    // Insert data
    $query = "INSERT INTO ps_stations (nomor_ps, status) 
              VALUES ('" . escape($nomor_ps) . "', '" . escape($status) . "')";
    
    if (execute($query)) {
        redirect('../index.php?page=list_ps', 'PS Station berhasil ditambahkan!', 'success');
    } else {
        redirect('../index.php?page=tambah_ps', 'Gagal menambahkan PS Station!', 'error');
    }
}

// =============================================
// UPDATE - EDIT PS
// =============================================
else if ($action == 'update') {
    
    $id = (int)$_POST['id'];
    $nomor_ps = cleanInput($_POST['nomor_ps']);
    $status = cleanInput($_POST['status']);
    
    // Validasi
    if (empty($nomor_ps) || empty($status)) {
        redirect('../index.php?page=edit_ps&id=' . $id, 'Semua field harus diisi!', 'error');
    }
    
    // Cek apakah nomor PS sudah digunakan oleh PS lain
    $cek = query("SELECT * FROM ps_stations WHERE nomor_ps = '" . escape($nomor_ps) . "' AND id != $id");
    if (count($cek) > 0) {
        redirect('../index.php?page=edit_ps&id=' . $id, 'Nomor PS sudah digunakan!', 'error');
    }
    
    // Update data
    $query = "UPDATE ps_stations SET 
              nomor_ps = '" . escape($nomor_ps) . "',
              status = '" . escape($status) . "'
              WHERE id = $id";
    
    if (execute($query)) {
        redirect('../index.php?page=list_ps', 'PS Station berhasil diupdate!', 'success');
    } else {
        redirect('../index.php?page=edit_ps&id=' . $id, 'Gagal mengupdate PS Station!', 'error');
    }
}

// =============================================
// DELETE - HAPUS PS
// =============================================
else if ($action == 'delete') {
    
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Cek apakah PS sedang dipakai
    $ps = query("SELECT * FROM ps_stations WHERE id = $id");
    if (count($ps) == 0) {
        redirect('../index.php?page=list_ps', 'Data PS tidak ditemukan!', 'error');
    }
    
    if ($ps[0]['status'] == 'dipakai') {
        redirect('../index.php?page=list_ps', 'PS sedang digunakan, tidak bisa dihapus!', 'error');
    }
    
    // Cek apakah PS pernah digunakan untuk rental (opsional, bisa soft delete)
    $cek_rental = query("SELECT * FROM rentals WHERE ps_station_id = $id LIMIT 1");
    if (count($cek_rental) > 0) {
        redirect('../index.php?page=list_ps', 'PS pernah digunakan untuk rental, tidak bisa dihapus!', 'warning');
    }
    
    // Delete data
    $query = "DELETE FROM ps_stations WHERE id = $id";
    
    if (execute($query)) {
        redirect('../index.php?page=list_ps', 'PS Station berhasil dihapus!', 'success');
    } else {
        redirect('../index.php?page=list_ps', 'Gagal menghapus PS Station!', 'error');
    }
}

// =============================================
// ACTION TIDAK VALID
// =============================================
else {
    redirect('../index.php?page=list_ps', 'Action tidak valid!', 'error');
}
?>