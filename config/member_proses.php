<?php
/**
 * =============================================
 * PROSES CRUD MEMBER
 * File: config/member_proses.php
 * =============================================
 */

session_start();
require_once 'database.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    redirect('../index.php?page=login', 'Silakan login terlebih dahulu!', 'warning');
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// =============================================
// CREATE - TAMBAH MEMBER BARU
// =============================================
if ($action == 'create') {
    
    $nama = cleanInput($_POST['nama']);
    $no_telp = cleanInput($_POST['no_telp']);
    
    // Validasi
    if (empty($nama) || empty($no_telp)) {
        redirect('../index.php?page=tambah_member', 'Semua field harus diisi!', 'error');
    }
    
    // Validasi format nomor telepon
    if (!preg_match('/^[0-9]{10,13}$/', $no_telp)) {
        redirect('../index.php?page=tambah_member', 'Format nomor telepon tidak valid!', 'error');
    }
    
    // Cek apakah nomor telepon sudah terdaftar
    $cek = query("SELECT * FROM members WHERE no_telp = '" . escape($no_telp) . "'");
    if (count($cek) > 0) {
        redirect('../index.php?page=tambah_member', 'Nomor telepon sudah terdaftar!', 'error');
    }
    
    // Insert data
    $query = "INSERT INTO members (nama, no_telp, saldo_jam, total_deposit) 
              VALUES (
                  '" . escape($nama) . "', 
                  '" . escape($no_telp) . "',
                  0,
                  0
              )";
    
    if (execute($query)) {
        redirect('../index.php?page=list_member', 'Member berhasil ditambahkan!', 'success');
    } else {
        redirect('../index.php?page=tambah_member', 'Gagal menambahkan member!', 'error');
    }
}

// =============================================
// UPDATE - EDIT MEMBER
// =============================================
else if ($action == 'update') {
    
    $id = (int)$_POST['id'];
    $nama = cleanInput($_POST['nama']);
    $no_telp = cleanInput($_POST['no_telp']);
    
    // Validasi
    if (empty($nama) || empty($no_telp)) {
        redirect('../index.php?page=edit_member&id=' . $id, 'Semua field harus diisi!', 'error');
    }
    
    // Validasi format nomor telepon
    if (!preg_match('/^[0-9]{10,13}$/', $no_telp)) {
        redirect('../index.php?page=edit_member&id=' . $id, 'Format nomor telepon tidak valid!', 'error');
    }
    
    // Cek apakah nomor telepon sudah digunakan member lain
    $cek = query("SELECT * FROM members WHERE no_telp = '" . escape($no_telp) . "' AND id != $id");
    if (count($cek) > 0) {
        redirect('../index.php?page=edit_member&id=' . $id, 'Nomor telepon sudah digunakan member lain!', 'error');
    }
    
    // Update data
    $query = "UPDATE members SET 
              nama = '" . escape($nama) . "',
              no_telp = '" . escape($no_telp) . "'
              WHERE id = $id";
    
    if (execute($query)) {
        redirect('../index.php?page=list_member', 'Data member berhasil diupdate!', 'success');
    } else {
        redirect('../index.php?page=edit_member&id=' . $id, 'Gagal mengupdate data member!', 'error');
    }
}

// =============================================
// DELETE - HAPUS MEMBER
// =============================================
else if ($action == 'delete') {
    
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Cek apakah member pernah melakukan rental
    $cek_rental = query("SELECT * FROM rentals WHERE member_id = $id LIMIT 1");
    if (count($cek_rental) > 0) {
        redirect('../index.php?page=list_member', 'Member pernah melakukan rental, tidak bisa dihapus!', 'warning');
    }
    
    // Cek apakah member pernah top-up
    $cek_topup = query("SELECT * FROM member_topups WHERE member_id = $id LIMIT 1");
    if (count($cek_topup) > 0) {
        redirect('../index.php?page=list_member', 'Member pernah melakukan top-up, tidak bisa dihapus!', 'warning');
    }
    
    // Delete data
    $query = "DELETE FROM members WHERE id = $id";
    
    if (execute($query)) {
        redirect('../index.php?page=list_member', 'Member berhasil dihapus!', 'success');
    } else {
        redirect('../index.php?page=list_member', 'Gagal menghapus member!', 'error');
    }
}

// =============================================
// ACTION TIDAK VALID
// =============================================
else {
    redirect('../index.php?page=list_member', 'Action tidak valid!', 'error');
}
?>