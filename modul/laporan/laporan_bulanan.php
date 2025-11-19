<?php
/**
 * =============================================
 * LAPORAN BULANAN
 * File: modul/laporan/laporan_bulanan.php
 * =============================================
 */

// Ambil bulan dan tahun dari filter atau default bulan ini
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');

// Validasi
if ($bulan < 1 || $bulan > 12) $bulan = (int)date('m');
if ($tahun < 2020 || $tahun > (int)date('Y') + 1) $tahun = (int)date('Y');

// Nama bulan
$nama_bulan = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Format untuk query
$bulan_str = str_pad($bulan, 2, '0', STR_PAD_LEFT);
$periode = "$tahun-$bulan_str";

// 1. STATISTIK BULANAN
$total_rental = query("
    SELECT COUNT(*) as total 
    FROM rentals 
    WHERE DATE_FORMAT(waktu_selesai, '%Y-%m') = '$periode' AND status = 'selesai'
")[0]['total'];

$rental_umum = query("
    SELECT COUNT(*) as total 
    FROM rentals 
    WHERE DATE_FORMAT(waktu_selesai, '%Y-%m') = '$periode' AND status = 'selesai' AND tipe_customer = 'umum'
")[0]['total'];

$rental_member = query("
    SELECT COUNT(*) as total 
    FROM rentals 
    WHERE DATE_FORMAT(waktu_selesai, '%Y-%m') = '$periode' AND status = 'selesai' AND tipe_customer = 'member'
")[0]['total'];

// 2. PENDAPATAN BULANAN
$pendapatan_rental = query("
    SELECT COALESCE(SUM(total_harga), 0) as total 
    FROM rentals 
    WHERE DATE_FORMAT(waktu_selesai, '%Y-%m') = '$periode' AND status = 'selesai'
")[0]['total'];

$pendapatan_topup = query("
    SELECT COALESCE(SUM(paket_harga), 0) as total 
    FROM member_topups 
    WHERE DATE_FORMAT(created_at, '%Y-%m') = '$periode'
")[0]['total'];

$total_pendapatan = $pendapatan_rental + $pendapatan_topup;

// 3. SUMMARY PER HARI
$summary_harian = query("
    SELECT 
        DATE(r.waktu_selesai) as tanggal,
        COUNT(r.id) as total_rental,
        SUM(CASE WHEN r.tipe_customer = 'umum' THEN 1 ELSE 0 END) as rental_umum,
        SUM(CASE WHEN r.tipe_customer = 'member' THEN 1 ELSE 0 END) as rental_member,
        COALESCE(SUM(r.total_harga), 0) as pendapatan_rental
    FROM rentals r
    WHERE DATE_FORMAT(r.waktu_selesai, '%Y-%m') = '$periode' AND r.status = 'selesai'
    GROUP BY DATE(r.waktu_selesai)
    ORDER BY tanggal ASC
");

// 4. TOP-UP PER HARI
$topup_harian = query("
    SELECT 
        DATE(created_at) as tanggal,
        COUNT(*) as total_topup,
        SUM(paket_harga) as pendapatan_topup
    FROM member_topups
    WHERE DATE_FORMAT(created_at, '%Y-%m') = '$periode'
    GROUP BY DATE(created_at)
");

// Gabungkan data harian
$data_harian = [];

foreach ($summary_harian as $sh) {
    $data_harian[$sh['tanggal']] = [
        'tanggal' => $sh['tanggal'],
        'total_rental' => $sh['total_rental'],
        'rental_umum' => $sh['rental_umum'],
        'rental_member' => $sh['rental_member'],
        'pendapatan_rental' => $sh['pendapatan_rental'],
        'pendapatan_topup' => 0
    ];
}

foreach ($topup_harian as $th) {
    if (isset($data_harian[$th['tanggal']])) {
        $data_harian[$th['tanggal']]['pendapatan_topup'] = $th['pendapatan_topup'];
    } else {
        $data_harian[$th['tanggal']] = [
            'tanggal' => $th['tanggal'],
            'total_rental' => 0,
            'rental_umum' => 0,
            'rental_member' => 0,
            'pendapatan_rental' => 0,
            'pendapatan_topup' => $th['pendapatan_topup']
        ];
    }
}

// Sort by date
ksort($data_harian);

// 5. PS PALING PRODUKTIF
$ps_produktif = query("
    SELECT 
        ps.nomor_ps,
        COUNT(r.id) as total_penggunaan,
        SUM(r.durasi_jam) as total_jam,
        COALESCE(SUM(r.total_harga), 0) as total_pendapatan
    FROM rentals r
    LEFT JOIN ps_stations ps ON r.ps_station_id = ps.id
    WHERE DATE_FORMAT(r.waktu_selesai, '%Y-%m') = '$periode' AND r.status = 'selesai'
    GROUP BY r.ps_station_id
    ORDER BY total_penggunaan DESC
    LIMIT 5
");
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-chart-bar"></i> Laporan Bulanan
    </h1>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> Print Laporan
    </button>
</div>

<!-- FILTER BULAN & TAHUN -->
<div class="card">
    <div style="padding: 20px;">
        <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: end;">
            <input type="hidden" name="page" value="laporan_bulanan">
            
            <div class="form-group" style="margin: 0; flex: 1;">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-control">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($i == $bulan) ? 'selected' : ''; ?>>
                        <?php echo $nama_bulan[$i]; ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group" style="margin: 0; flex: 1;">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-control">
                    <?php for ($y = 2020; $y <= (int)date('Y'); $y++): ?>
                    <option value="<?php echo $y; ?>" <?php echo ($y == $tahun) ? 'selected' : ''; ?>>
                        <?php echo $y; ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Tampilkan
            </button>
            
            <a href="index.php?page=laporan_bulanan" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Bulan Ini
            </a>
        </form>
    </div>
</div>

<!-- INFO PERIODE -->
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 12px; margin-bottom: 20px; text-align: center; color: white;">
    <h2 style="margin-bottom: 8px;">
        <?php echo $nama_bulan[$bulan]; ?> <?php echo $tahun; ?>
    </h2>
    <p style="margin: 0; opacity: 0.9;">Laporan Transaksi Bulanan</p>
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
        <h3 class="card-title">Rincian Pendapatan Bulanan</h3>
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
            <span style="font-size: 18px; font-weight: 600;">TOTAL PENDAPATAN BULANAN</span>
            <strong style="color: #10b981; font-size: 28px; font-weight: 700;">
                <?php echo formatRupiah($total_pendapatan); ?>
            </strong>
        </div>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #6b7280;">Rata-rata per Hari</span>
                <strong style="color: #2563eb;">
                    <?php 
                    $hari_kerja = count($data_harian);
                    echo $hari_kerja > 0 ? formatRupiah($total_pendapatan / $hari_kerja) : 'Rp 0';
                    ?>
                </strong>
            </div>
        </div>
    </div>
</div>

<!-- SUMMARY PER HARI -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Ringkasan Harian - <?php echo $nama_bulan[$bulan]; ?> <?php echo $tahun; ?></h3>
    </div>

    <?php if (count($data_harian) > 0): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Total Rental</th>
                    <th>Umum</th>
                    <th>Member</th>
                    <th>Pendapatan Rental</th>
                    <th>Pendapatan Top-up</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_harian as $data): ?>
                <tr>
                    <td><strong><?php echo formatTanggal($data['tanggal']); ?></strong></td>
                    <td><?php echo $data['total_rental']; ?></td>
                    <td><?php echo $data['rental_umum']; ?></td>
                    <td><?php echo $data['rental_member']; ?></td>
                    <td><?php echo formatRupiah($data['pendapatan_rental']); ?></td>
                    <td><?php echo formatRupiah($data['pendapatan_topup']); ?></td>
                    <td>
                        <strong><?php echo formatRupiah($data['pendapatan_rental'] + $data['pendapatan_topup']); ?></strong>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot style="background: #f9fafb; font-weight: 600;">
                <tr>
                    <td>TOTAL</td>
                    <td><?php echo $total_rental; ?></td>
                    <td><?php echo $rental_umum; ?></td>
                    <td><?php echo $rental_member; ?></td>
                    <td><?php echo formatRupiah($pendapatan_rental); ?></td>
                    <td><?php echo formatRupiah($pendapatan_topup); ?></td>
                    <td><strong style="color: #10b981; font-size: 16px;"><?php echo formatRupiah($total_pendapatan); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 40px; color: #9ca3af;">
        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px;"></i>
        <p>Tidak ada transaksi pada periode ini</p>
    </div>
    <?php endif; ?>
</div>

<!-- PS PALING PRODUKTIF -->
<?php if (count($ps_produktif) > 0): ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">PS Station Paling Produktif</h3>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th width="10%">Rank</th>
                    <th>Nomor PS</th>
                    <th>Total Penggunaan</th>
                    <th>Total Jam</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($ps_produktif as $ps): ?>
                <tr>
                    <td>
                        <?php if ($rank == 1): ?>
                            <span style="font-size: 24px;">🥇</span>
                        <?php elseif ($rank == 2): ?>
                            <span style="font-size: 24px;">🥈</span>
                        <?php elseif ($rank == 3): ?>
                            <span style="font-size: 24px;">🥉</span>
                        <?php else: ?>
                            <strong><?php echo $rank; ?></strong>
                        <?php endif; ?>
                    </td>
                    <td><strong style="font-size: 16px; color: #2563eb;"><?php echo htmlspecialchars($ps['nomor_ps']); ?></strong></td>
                    <td><?php echo $ps['total_penggunaan']; ?> kali</td>
                    <td><?php echo number_format($ps['total_jam'], 1); ?> jam</td>
                    <td><strong><?php echo formatRupiah($ps['total_pendapatan']); ?></strong></td>
                </tr>
                <?php $rank++; endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>