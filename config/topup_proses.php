<?php
/**
 * =============================================
 * PROSES TOP-UP MEMBER
 * File: config/topup_proses.php
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
// PROSES TOP-UP
// =============================================
if ($action == 'topup') {
    
    $member_id = (int)$_POST['member_id'];
    $paket_id = (int)$_POST['paket_id'];
    $user_id = $_SESSION['user_id'];
    
    // Validasi
    if (empty($member_id) || empty($paket_id)) {
        redirect('../index.php?page=topup_member&id=' . $member_id, 'Pilih paket terlebih dahulu!', 'error');
    }
    
    // Ambil data member
    $member = query("SELECT * FROM members WHERE id = $member_id");
    if (count($member) == 0) {
        redirect('../index.php?page=list_member', 'Member tidak ditemukan!', 'error');
    }
    $member = $member[0];
    
    // Ambil data paket
    $paket = query("SELECT * FROM pricing WHERE id = $paket_id AND tipe = 'member' AND is_active = TRUE");
    if (count($paket) == 0) {
        redirect('../index.php?page=topup_member&id=' . $member_id, 'Paket tidak valid!', 'error');
    }
    $paket = $paket[0];
    
    // Start transaction
    beginTransaction();
    
    try {
        // 1. Insert ke tabel member_topups
        $query_topup = "INSERT INTO member_topups 
                        (member_id, paket_jam, paket_harga, jam_ditambahkan, user_id) 
                        VALUES (
                            $member_id,
                            {$paket['paket_jam']},
                            {$paket['paket_harga']},
                            {$paket['paket_jam']},
                            $user_id
                        )";
        
        if (!execute($query_topup)) {
            throw new Exception('Gagal insert topup');
        }
        
        // 2. Update saldo member
        $saldo_baru = $member['saldo_jam'] + $paket['paket_jam'];
        $deposit_baru = $member['total_deposit'] + $paket['paket_harga'];
        
        $query_update = "UPDATE members SET 
                         saldo_jam = $saldo_baru,
                         total_deposit = $deposit_baru
                         WHERE id = $member_id";
        
        if (!execute($query_update)) {
            throw new Exception('Gagal update saldo');
        }
        
        // Commit transaction
        commit();
        
        redirect('../index.php?page=list_member', 
                'Top-up berhasil! Saldo ' . htmlspecialchars($member['nama']) . ' bertambah ' . $paket['paket_jam'] . ' jam', 
                'success');
        
    } catch (Exception $e) {
        // Rollback jika ada error
        rollback();
        redirect('../index.php?page=topup_member&id=' . $member_id, 'Gagal melakukan top-up: ' . $e->getMessage(), 'error');
    }
}

// =============================================
// ACTION TIDAK VALID
// =============================================
else {
    redirect('../index.php?page=list_member', 'Action tidak valid!', 'error');
}
?>