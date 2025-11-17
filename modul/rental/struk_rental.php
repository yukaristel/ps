<?php
/**
 * =============================================
 * STRUK RENTAL
 * File: modul/rental/struk_rental.php
 * =============================================
 */

// Ambil ID rental dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data rental yang sudah selesai
$rental = query("
    SELECT 
        r.*,
        ps.nomor_ps,
        COALESCE(m.nama, 'Customer Umum') as nama_customer,
        COALESCE(m.no_telp, '-') as no_telp,
        u.nama_lengkap as petugas
    FROM rentals r
    LEFT JOIN ps_stations ps ON r.ps_station_id = ps.id
    LEFT JOIN members m ON r.member_id = m.id
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.id = $id AND r.status = 'selesai'
");

// Cek apakah rental ditemukan
if (count($rental) == 0) {
    redirect('index.php?page=list_rental', 'Struk tidak ditemukan!', 'error');
}

$rental = $rental[0];

// Hitung durasi
$waktu_mulai = strtotime($rental['waktu_mulai']);
$waktu_selesai = strtotime($rental['waktu_selesai']);
$durasi_detik = $waktu_selesai - $waktu_mulai;
$jam = floor($durasi_detik / 3600);
$menit = floor(($durasi_detik % 3600) / 60);
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-receipt"></i> Struk Rental
    </h1>
    <div>
        <button onclick="printDiv('struk')" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Struk
        </button>
        <a href="index.php?page=list_rental" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card" id="struk" style="max-width: 600px; margin: 0 auto;">
    <div style="padding: 40px; text-align: center; border-bottom: 2px dashed #e5e7eb;">
        <h2 style="margin-bottom: 8px; color: #2563eb;">
            <i class="fas fa-gamepad"></i> RENTAL PLAYSTATION
        </h2>
        <p style="color: #6b7280; margin: 0; font-size: 14px;">Terima kasih atas kunjungan Anda</p>
    </div>

    <div style="padding: 30px;">
        
        <!-- Info Transaksi -->
        <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                <span style="color: #6b7280;">No. Transaksi</span>
                <strong>#<?php echo str_pad($rental['id'], 6, '0', STR_PAD_LEFT); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                <span style="color: #6b7280;">Tanggal</span>
                <strong><?php echo formatDateTime($rental['waktu_selesai']); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px;">
                <span style="color: #6b7280;">Petugas</span>
                <strong><?php echo htmlspecialchars($rental['petugas']); ?></strong>
            </div>
        </div>

        <!-- Detail Customer -->
        <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb;">
            <h4 style="margin-bottom: 12px; font-size: 14px; color: #1f2937;">DETAIL CUSTOMER</h4>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                <span style="color: #6b7280;">Nama</span>
                <strong><?php echo htmlspecialchars($rental['nama_customer']); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                <span style="color: #6b7280;">No. Telp</span>
                <strong><?php echo htmlspecialchars($rental['no_telp']); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px;">
                <span style="color: #6b7280;">Tipe</span>
                <?php if ($rental['tipe_customer'] == 'member'): ?>
                    <strong style="color: #2563eb;">MEMBER</strong>
                <?php else: ?>
                    <strong style="color: #f59e0b;">UMUM</strong>
                <?php endif; ?>
            </div>
        </div>

        <!-- Detail Rental -->
        <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb;">
            <h4 style="margin-bottom: 12px; font-size: 14px; color: #1f2937;">DETAIL RENTAL</h4>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                <span style="color: #6b7280;">PS Station</span>
                <strong style="color: #2563eb; font-size: 16px;"><?php echo htmlspecialchars($rental['nomor_ps']); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                <span style="color: #6b7280;">Mulai</span>
                <strong><?php echo date('H:i', strtotime($rental['waktu_mulai'])); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                <span style="color: #6b7280;">Selesai</span>
                <strong><?php echo date('H:i', strtotime($rental['waktu_selesai'])); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px;">
                <span style="color: #6b7280;">Durasi</span>
                <strong><?php echo $jam; ?> Jam <?php echo $menit; ?> Menit</strong>
            </div>
        </div>

        <!-- Perhitungan -->
        <?php if ($rental['tipe_customer'] == 'umum'): ?>
        <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                <span style="color: #6b7280;">Durasi (dibulatkan)</span>
                <strong><?php echo $rental['durasi_jam']; ?> Jam</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                <span style="color: #6b7280;">Harga per Jam</span>
                <strong><?php echo formatRupiah($rental['harga_per_jam']); ?></strong>
            </div>
        </div>
        <?php endif; ?>

        <!-- Total -->
        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin-top: 20px;">
            <?php if ($rental['tipe_customer'] == 'umum'): ?>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 18px; font-weight: 600; color: #1f2937;">TOTAL BAYAR</span>
                <strong style="font-size: 28px; font-weight: 700; color: #10b981;">
                    <?php echo formatRupiah($rental['total_harga']); ?>
                </strong>
            </div>
            <?php else: ?>
            <div style="text-align: center;">
                <p style="margin: 0; color: #2563eb; font-size: 16px; font-weight: 600;">
                    <i class="fas fa-check-circle"></i> Menggunakan Saldo Jam Member
                </p>
                <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 13px;">
                    <?php echo number_format($rental['durasi_jam'], 2); ?> jam telah dipotong dari saldo
                </p>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <div style="padding: 20px; text-align: center; background: #f9fafb; border-top: 2px dashed #e5e7eb;">
        <p style="margin: 0; font-size: 13px; color: #6b7280;">
            Terima kasih sudah bermain!<br>
            Sampai jumpa lagi 😊
        </p>
    </div>
</div>

<div style="text-align: center; margin-top: 30px;">
    <a href="index.php?page=mulai_rental" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> Rental Baru
    </a>
    <a href="index.php?page=list_rental" class="btn btn-secondary">
        <i class="fas fa-list"></i> Lihat Rental Aktif
    </a>
</div>