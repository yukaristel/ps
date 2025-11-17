<?php
/**
 * =============================================
 * HALAMAN DASHBOARD
 * File: modul/dashboard/dashboard.php
 * =============================================
 */

// Ambil statistik untuk dashboard
$today = date('Y-m-d');

// 1. Total PS & Status
$total_ps = query("SELECT COUNT(*) as total FROM ps_stations")[0]['total'];
$ps_tersedia = query("SELECT COUNT(*) as total FROM ps_stations WHERE status = 'tersedia'")[0]['total'];
$ps_dipakai = query("SELECT COUNT(*) as total FROM ps_stations WHERE status = 'dipakai'")[0]['total'];
$ps_maintenance = query("SELECT COUNT(*) as total FROM ps_stations WHERE status = 'maintenance'")[0]['total'];

// 2. Rental Aktif Hari Ini
$rental_aktif = query("SELECT COUNT(*) as total FROM rentals WHERE DATE(waktu_mulai) = '$today' AND status = 'berlangsung'")[0]['total'];

// 3. Total Member
$total_member = query("SELECT COUNT(*) as total FROM members")[0]['total'];

// 4. Pendapatan Hari Ini
$pendapatan_rental = query("SELECT COALESCE(SUM(total_harga), 0) as total FROM rentals WHERE DATE(waktu_selesai) = '$today' AND status = 'selesai'")[0]['total'];
$pendapatan_topup = query("SELECT COALESCE(SUM(paket_harga), 0) as total FROM member_topups WHERE DATE(created_at) = '$today'")[0]['total'];
$total_pendapatan = $pendapatan_rental + $pendapatan_topup;

// 5. Rental Aktif Sekarang (Detail)
$rental_berlangsung = query("
    SELECT 
        r.id,
        r.waktu_mulai,
        ps.nomor_ps,
        r.tipe_customer,
        COALESCE(m.nama, 'Customer Umum') as nama_customer,
        COALESCE(m.no_telp, '-') as no_telp,
        u.nama_lengkap as petugas,
        TIMESTAMPDIFF(MINUTE, r.waktu_mulai, NOW()) as durasi_menit
    FROM rentals r
    LEFT JOIN ps_stations ps ON r.ps_station_id = ps.id
    LEFT JOIN members m ON r.member_id = m.id
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.status = 'berlangsung'
    ORDER BY r.waktu_mulai DESC
    LIMIT 10
");
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p style="color: #6b7280; margin-top: 8px;">
            <i class="fas fa-calendar-alt"></i> <?php echo formatTanggal(date('Y-m-d')); ?>
        </p>
    </div>
</div>

<!-- STATISTICS CARDS -->
<div class="stats-grid">
    
    <!-- PS Tersedia -->
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-gamepad"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $ps_tersedia; ?></h3>
            <p>PS Tersedia</p>
        </div>
    </div>

    <!-- PS Dipakai -->
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $ps_dipakai; ?></h3>
            <p>PS Sedang Dipakai</p>
        </div>
    </div>

    <!-- Rental Aktif -->
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $rental_aktif; ?></h3>
            <p>Rental Hari Ini</p>
        </div>
    </div>

    <!-- Pendapatan Hari Ini -->
    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo formatRupiah($total_pendapatan); ?></h3>
            <p>Pendapatan Hari Ini</p>
        </div>
    </div>

</div>

<!-- ROW 2: INFO CARDS -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
    
    <!-- Status PS -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Status PS Station
            </h3>
        </div>
        <div style="padding: 10px 0;">
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                <span>Total PS</span>
                <strong><?php echo $total_ps; ?> Unit</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                <span><i class="fas fa-check-circle" style="color: #10b981;"></i> Tersedia</span>
                <strong><?php echo $ps_tersedia; ?> Unit</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                <span><i class="fas fa-play-circle" style="color: #f59e0b;"></i> Dipakai</span>
                <strong><?php echo $ps_dipakai; ?> Unit</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px 0;">
                <span><i class="fas fa-tools" style="color: #ef4444;"></i> Maintenance</span>
                <strong><?php echo $ps_maintenance; ?> Unit</strong>
            </div>
        </div>
    </div>

    <!-- Pendapatan Detail -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line"></i> Pendapatan Hari Ini
            </h3>
        </div>
        <div style="padding: 10px 0;">
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                <span>Dari Rental</span>
                <strong><?php echo formatRupiah($pendapatan_rental); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                <span>Dari Top-up Member</span>
                <strong><?php echo formatRupiah($pendapatan_topup); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px 0; background: #eff6ff; margin: 10px -10px -10px -10px; padding: 15px 10px; border-radius: 0 0 8px 8px;">
                <span style="font-weight: 600;">Total Pendapatan</span>
                <strong style="color: #2563eb; font-size: 18px;"><?php echo formatRupiah($total_pendapatan); ?></strong>
            </div>
        </div>
    </div>

</div>

<!-- RENTAL AKTIF SEKARANG -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i> Rental Berlangsung Saat Ini
        </h3>
    </div>

    <?php if (count($rental_berlangsung) > 0): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No PS</th>
                    <th>Customer</th>
                    <th>Tipe</th>
                    <th>Mulai</th>
                    <th>Durasi</th>
                    <th>Petugas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rental_berlangsung as $rental): ?>
                <tr>
                    <td><strong><?php echo $rental['nomor_ps']; ?></strong></td>
                    <td>
                        <?php echo htmlspecialchars($rental['nama_customer']); ?><br>
                        <small style="color: #6b7280;"><?php echo $rental['no_telp']; ?></small>
                    </td>
                    <td>
                        <?php if ($rental['tipe_customer'] == 'member'): ?>
                            <span class="badge badge-info">Member</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Umum</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('H:i', strtotime($rental['waktu_mulai'])); ?></td>
                    <td>
                        <?php 
                        $jam = floor($rental['durasi_menit'] / 60);
                        $menit = $rental['durasi_menit'] % 60;
                        echo $jam . ' jam ' . $menit . ' menit';
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($rental['petugas']); ?></td>
                    <td>
                        <a href="index.php?page=selesai_rental&id=<?php echo $rental['id']; ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-stop-circle"></i> Selesai
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 40px; color: #9ca3af;">
        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px;"></i>
        <p>Tidak ada rental yang sedang berlangsung</p>
    </div>
    <?php endif; ?>
</div>