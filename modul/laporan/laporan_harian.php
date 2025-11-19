<?php
/**
 * =============================================
 * LAPORAN HARIAN
 * File: modul/laporan/laporan_harian.php
 * =============================================
 */

// Ambil tanggal dari filter atau default hari ini
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Validasi format tanggal
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    $tanggal = date('Y-m-d');
}

// 1. STATISTIK RENTAL
$total_rental = query("
    SELECT COUNT(*) as total 
    FROM rentals 
    WHERE DATE(waktu_selesai) = '$tanggal' AND status = 'selesai'
")[0]['total'];

$rental_umum = query("
    SELECT COUNT(*) as total 
    FROM rentals 
    WHERE DATE(waktu_selesai) = '$tanggal' AND status = 'selesai' AND tipe_customer = 'umum'
")[0]['total'];

$rental_member = query("
    SELECT COUNT(*) as total 
    FROM rentals 
    WHERE DATE(waktu_selesai) = '$tanggal' AND status = 'selesai' AND tipe_customer = 'member'
")[0]['total'];

// 2. PENDAPATAN
$pendapatan_rental = query("
    SELECT COALESCE(SUM(total_harga), 0) as total 
    FROM rentals 
    WHERE DATE(waktu_selesai) = '$tanggal' AND status = 'selesai'
")[0]['total'];

$pendapatan_topup = query("
    SELECT COALESCE(SUM(paket_harga), 0) as total 
    FROM member_topups 
    WHERE DATE(created_at) = '$tanggal'
")[0]['total'];

$total_pendapatan = $pendapatan_rental + $pendapatan_topup;

// 3. DETAIL TRANSAKSI RENTAL
$detail_rental = query("
    SELECT 
        r.id,
        r.waktu_mulai,
        r.waktu_selesai,
        r.durasi_jam,
        r.total_harga,
        r.tipe_customer,
        ps.nomor_ps,
        COALESCE(m.nama, 'Customer Umum') as nama_customer,
        u.nama_lengkap as petugas
    FROM rentals r
    LEFT JOIN ps_stations ps ON r.ps_station_id = ps.id
    LEFT JOIN members m ON r.member_id = m.id
    LEFT JOIN users u ON r.user_id = u.id
    WHERE DATE(r.waktu_selesai) = '$tanggal' AND r.status = 'selesai'
    ORDER BY r.waktu_selesai DESC
");

// 4. DETAIL TOP-UP
$detail_topup = query("
    SELECT 
        t.*,
        m.nama as nama_member,
        u.nama_lengkap as petugas
    FROM member_topups t
    LEFT JOIN members m ON t.member_id = m.id
    LEFT JOIN users u ON t.user_id = u.id
    WHERE DATE(t.created_at) = '$tanggal'
    ORDER BY t.created_at DESC
");
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-alt"></i> Laporan Harian
    </h1>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> Print Laporan
    </button>
</div>

<!-- FILTER TANGGAL -->
<div class="card">
    <div style="padding: 20px;">
        <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: end;">
            <input type="hidden" name="page" value="laporan_harian">
            
            <div class="form-group" style="margin: 0; flex: 1;">
                <label class="form-label">Pilih Tanggal</label>
                <input 
                    type="date" 
                    name="tanggal" 
                    class="form-control" 
                    value="<?php echo $tanggal; ?>"
                    max="<?php echo date('Y-m-d'); ?>"
                >
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Tampilkan
            </button>
            
            <a href="index.php?page=laporan_harian" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Hari Ini
            </a>
        </form>
    </div>
</div>

<!-- INFO TANGGAL -->
<div style="background: #eff6ff; padding: 20px; border-radius: 12px; margin-bottom: 20px; text-align: center;">
    <h2 style="color: #1e40af; margin-bottom: 8px;">
        <?php echo formatTanggal($tanggal); ?>
    </h2>
    <p style="color: #6b7280; margin: 0;">Laporan Transaksi Harian</p>
</div>

<!-- STATISTIK CARDS -->
<div class="stats-grid">
    
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-list"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $total_rental; ?></h3>
            <p>Total Transaksi</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-user"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $rental_umum; ?></h3>
            <p>Customer Umum</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $rental_member; ?></h3>
            <p>Member</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo formatRupiah($total_pendapatan); ?></h3>
            <p>Total Pendapatan</p>
        </div>
    </div>

</div>

<!-- RINCIAN PENDAPATAN -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Rincian Pendapatan</h3>
    </div>
    
    <div style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
            <span>Pendapatan dari Rental</span>
            <strong><?php echo formatRupiah($pendapatan_rental); ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
            <span>Pendapatan dari Top-up Member</span>
            <strong><?php echo formatRupiah($pendapatan_topup); ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 20px 0; margin-top: 10px; background: #f0fdf4; margin-left: -20px; margin-right: -20px; padding-left: 20px; padding-right: 20px;">
            <span style="font-size: 18px; font-weight: 600;">TOTAL PENDAPATAN KOTOR</span>
            <strong style="color: #10b981; font-size: 24px; font-weight: 700;">
                <?php echo formatRupiah($total_pendapatan); ?>
            </strong>
        </div>
    </div>
</div>

<!-- DETAIL TRANSAKSI RENTAL -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Transaksi Rental (<?php echo count($detail_rental); ?>)</h3>
    </div>

    <?php if (count($detail_rental) > 0): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>No. Trans</th>
                    <th>PS</th>
                    <th>Customer</th>
                    <th>Tipe</th>
                    <th>Waktu</th>
                    <th>Durasi</th>
                    <th>Total</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($detail_rental as $r): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><strong>#<?php echo str_pad($r['id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                    <td><?php echo htmlspecialchars($r['nomor_ps']); ?></td>
                    <td><?php echo htmlspecialchars($r['nama_customer']); ?></td>
                    <td>
                        <?php if ($r['tipe_customer'] == 'member'): ?>
                            <span class="badge badge-info">Member</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Umum</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo date('H:i', strtotime($r['waktu_mulai'])); ?> - 
                        <?php echo date('H:i', strtotime($r['waktu_selesai'])); ?>
                    </td>
                    <td><?php echo number_format($r['durasi_jam'], 1); ?> jam</td>
                    <td>
                        <?php if ($r['tipe_customer'] == 'member'): ?>
                            <span style="color: #10b981;">Saldo Jam</span>
                        <?php else: ?>
                            <strong><?php echo formatRupiah($r['total_harga']); ?></strong>
                        <?php endif; ?>
                    </td>
                    <td><small><?php echo htmlspecialchars($r['petugas']); ?></small></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot style="background: #f9fafb; font-weight: 600;">
                <tr>
                    <td colspan="7" style="text-align: right;">SUBTOTAL RENTAL:</td>
                    <td colspan="2"><strong><?php echo formatRupiah($pendapatan_rental); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 40px; color: #9ca3af;">
        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px;"></i>
        <p>Tidak ada transaksi rental pada tanggal ini</p>
    </div>
    <?php endif; ?>
</div>

<!-- DETAIL TOP-UP MEMBER -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Top-up Member (<?php echo count($detail_topup); ?>)</h3>
    </div>

    <?php if (count($detail_topup) > 0): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Waktu</th>
                    <th>Member</th>
                    <th>Paket</th>
                    <th>Jam Ditambahkan</th>
                    <th>Harga</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($detail_topup as $t): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo date('H:i', strtotime($t['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($t['nama_member']); ?></td>
                    <td><strong>Paket <?php echo $t['paket_jam']; ?> Jam</strong></td>
                    <td>
                        <span class="badge badge-success">
                            +<?php echo number_format($t['jam_ditambahkan'], 1); ?> jam
                        </span>
                    </td>
                    <td><strong><?php echo formatRupiah($t['paket_harga']); ?></strong></td>
                    <td><small><?php echo htmlspecialchars($t['petugas']); ?></small></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot style="background: #f9fafb; font-weight: 600;">
                <tr>
                    <td colspan="5" style="text-align: right;">SUBTOTAL TOP-UP:</td>
                    <td colspan="2"><strong><?php echo formatRupiah($pendapatan_topup); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 40px; color: #9ca3af;">
        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px;"></i>
        <p>Tidak ada transaksi top-up pada tanggal ini</p>
    </div>
    <?php endif; ?>
</div>