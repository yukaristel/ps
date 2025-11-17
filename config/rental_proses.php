<?php
/**
 * =============================================
 * PROSES RENTAL (MULAI & SELESAI)
 * File: config/rental_proses.php
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
// MULAI RENTAL
// =============================================
if ($action == 'mulai') {
    
    $ps_station_id = (int)$_POST['ps_station_id'];
    $tipe_customer = cleanInput($_POST['tipe_customer']);
    $harga_per_jam = (float)$_POST['harga_per_jam'];
    $member_id = isset($_POST['member_id']) && !empty($_POST['member_id']) ? (int)$_POST['member_id'] : NULL;
    $user_id = $_SESSION['user_id'];
    
    // Validasi
    if (empty($ps_station_id) || empty($tipe_customer)) {
        redirect('../index.php?page=mulai_rental', 'Data tidak lengkap!', 'error');
    }
    
    // Cek apakah PS tersedia
    $ps = query("SELECT * FROM ps_stations WHERE id = $ps_station_id");
    if (count($ps) == 0) {
        redirect('../index.php?page=mulai_rental', 'PS tidak ditemukan!', 'error');
    }
    
    if ($ps[0]['status'] != 'tersedia') {
        redirect('../index.php?page=mulai_rental', 'PS tidak tersedia!', 'error');
    }
    
    // Jika member, validasi saldo
    if ($tipe_customer == 'member') {
        if (empty($member_id)) {
            redirect('../index.php?page=mulai_rental', 'Pilih member terlebih dahulu!', 'error');
        }
        
        $member = query("SELECT * FROM members WHERE id = $member_id");
        if (count($member) == 0) {
            redirect('../index.php?page=mulai_rental', 'Member tidak ditemukan!', 'error');
        }
        
        if ($member[0]['saldo_jam'] < 0.5) {
            redirect('../index.php?page=mulai_rental', 'Saldo jam member tidak mencukupi!', 'error');
        }
    }
    
    // Start transaction
    beginTransaction();
    
    try {
        // 1. Insert rental
        $waktu_mulai = date('Y-m-d H:i:s');
        
        $query_rental = "INSERT INTO rentals 
                        (ps_station_id, member_id, tipe_customer, waktu_mulai, harga_per_jam, user_id, status) 
                        VALUES (
                            $ps_station_id,
                            " . ($member_id ? $member_id : "NULL") . ",
                            '$tipe_customer',
                            '$waktu_mulai',
                            $harga_per_jam,
                            $user_id,
                            'berlangsung'
                        )";
        
        if (!execute($query_rental)) {
            throw new Exception('Gagal insert rental');
        }
        
        // 2. Update status PS menjadi 'dipakai'
        $query_ps = "UPDATE ps_stations SET status = 'dipakai' WHERE id = $ps_station_id";
        
        if (!execute($query_ps)) {
            throw new Exception('Gagal update status PS');
        }
        
        // Commit transaction
        commit();
        
        $customer_name = ($tipe_customer == 'member' && $member_id) ? $member[0]['nama'] : 'Customer Umum';
        redirect('../index.php?page=list_rental', 
                'Rental berhasil dimulai untuk ' . htmlspecialchars($customer_name) . ' di ' . $ps[0]['nomor_ps'], 
                'success');
        
    } catch (Exception $e) {
        // Rollback jika ada error
        rollback();
        redirect('../index.php?page=mulai_rental', 'Gagal memulai rental: ' . $e->getMessage(), 'error');
    }
}

// =============================================
// SELESAI RENTAL
// =============================================
else if ($action == 'selesai') {
    
    $rental_id = (int)$_POST['rental_id'];
    $durasi_jam = (float)$_POST['durasi_jam'];
    $total_harga = (float)$_POST['total_harga'];
    
    // Ambil data rental
    $rental = query("
        SELECT r.*, m.saldo_jam, ps.id as ps_id
        FROM rentals r
        LEFT JOIN members m ON r.member_id = m.id
        LEFT JOIN ps_stations ps ON r.ps_station_id = ps.id
        WHERE r.id = $rental_id AND r.status = 'berlangsung'
    ");
    
    if (count($rental) == 0) {
        redirect('../index.php?page=list_rental', 'Data rental tidak ditemukan!', 'error');
    }
    
    $rental = $rental[0];
    $waktu_selesai = date('Y-m-d H:i:s');
    
    // Start transaction
    beginTransaction();
    
    try {
        // 1. Update rental
        $query_update_rental = "UPDATE rentals SET 
                                waktu_selesai = '$waktu_selesai',
                                durasi_jam = $durasi_jam,
                                total_harga = $total_harga,
                                status = 'selesai'
                                WHERE id = $rental_id";
        
        if (!execute($query_update_rental)) {
            throw new Exception('Gagal update rental');
        }
        
        // 2. Jika member, kurangi saldo jam
        if ($rental['tipe_customer'] == 'member' && $rental['member_id']) {
            $saldo_baru = $rental['saldo_jam'] - $durasi_jam;
            
            // Pastikan saldo tidak negatif
            if ($saldo_baru < 0) {
                throw new Exception('Saldo jam tidak mencukupi');
            }
            
            $query_update_member = "UPDATE members SET 
                                    saldo_jam = $saldo_baru
                                    WHERE id = {$rental['member_id']}";
            
            if (!execute($query_update_member)) {
                throw new Exception('Gagal update saldo member');
            }
        }
        
        // 3. Update status PS menjadi 'tersedia'
        $query_update_ps = "UPDATE ps_stations SET 
                            status = 'tersedia' 
                            WHERE id = {$rental['ps_id']}";
        
        if (!execute($query_update_ps)) {
            throw new Exception('Gagal update status PS');
        }
        
        // Commit transaction
        commit();
        
        // Redirect ke struk
        redirect('../index.php?page=struk_rental&id=' . $rental_id, 
                'Rental berhasil diselesaikan!', 
                'success');
        
    } catch (Exception $e) {
        // Rollback jika ada error
        rollback();
        redirect('../index.php?page=selesai_rental&id=' . $rental_id, 
                'Gagal menyelesaikan rental: ' . $e->getMessage(), 
                'error');
    }
}

// =============================================
// BATALKAN RENTAL (OPSIONAL)
// =============================================
else if ($action == 'batal') {
    
    $rental_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Ambil data rental
    $rental = query("
        SELECT r.*, ps.id as ps_id
        FROM rentals r
        LEFT JOIN ps_stations ps ON r.ps_station_id = ps.id
        WHERE r.id = $rental_id AND r.status = 'berlangsung'
    ");
    
    if (count($rental) == 0) {
        redirect('../index.php?page=list_rental', 'Data rental tidak ditemukan!', 'error');
    }
    
    $rental = $rental[0];
    
    // Start transaction
    beginTransaction();
    
    try {
        // 1. Update status rental
        $query_rental = "UPDATE rentals SET status = 'dibatalkan' WHERE id = $rental_id";
        if (!execute($query_rental)) {
            throw new Exception('Gagal membatalkan rental');
        }
        
        // 2. Update status PS
        $query_ps = "UPDATE ps_stations SET status = 'tersedia' WHERE id = {$rental['ps_id']}";
        if (!execute($query_ps)) {
            throw new Exception('Gagal update status PS');
        }
        
        commit();
        
        redirect('../index.php?page=list_rental', 'Rental berhasil dibatalkan!', 'success');
        
    } catch (Exception $e) {
        rollback();
        redirect('../index.php?page=list_rental', 'Gagal membatalkan rental: ' . $e->getMessage(), 'error');
    }
}

// =============================================
// ACTION TIDAK VALID
// =============================================
else {
    redirect('../index.php?page=list_rental', 'Action tidak valid!', 'error');
}
?>