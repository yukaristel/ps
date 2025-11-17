<?php
/**
 * =============================================
 * FORM SELESAI RENTAL
 * File: modul/rental/selesai_rental.php
 * =============================================
 */

// Ambil ID rental dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data rental
$rental = query("
    SELECT 
        r.*,
        ps.nomor_ps,
        COALESCE(m.nama, 'Customer Umum') as nama_customer,
        COALESCE(m.no_telp, '-') as no_telp,
        COALESCE(m.saldo_jam, 0) as saldo_jam,
        u.nama_lengkap as petugas,
        TIMESTAMPDIFF(MINUTE, r.waktu_mulai, NOW()) as durasi_menit
    FROM rentals r
    LEFT JOIN ps_stations ps ON r.ps_station_id = ps.id
    LEFT JOIN members m ON r.member_id = m.id
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.id = $id AND r.status = 'berlangsung'
");

// Cek apakah data ditemukan
if (count($rental) == 0) {
    redirect('index.php?page=list_rental', 'Data rental tidak ditemukan atau sudah selesai!', 'error');
}

$rental = $rental[0];

// Hitung durasi dan biaya
$durasi_menit = $rental['durasi_menit'];
$durasi_jam_decimal = $durasi_menit / 60;
$durasi_jam_dibulatkan = ceil($durasi_jam_decimal); // Pembulatan ke atas

// Hitung biaya
if ($rental['tipe_customer'] == 'member') {
    $total_harga = 0; // Member tidak bayar, pakai saldo jam
    $jam_dipakai = $durasi_jam_dibulatkan;
    $saldo_setelah = $rental['saldo_jam'] - $jam_dipakai;
} else {
    $total_harga = $durasi_jam_dibulatkan * $rental['harga_per_jam'];
    $jam_dipakai = 0;
    $saldo_setelah = 0;
}

$jam = floor($durasi_menit / 60);
$menit = $durasi_menit % 60;
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-stop-circle"></i> Selesai Rental
    </h1>
    <a href="index.php?page=list_rental" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Konfirmasi Selesai Rental</h3>
    </div>

    <!-- Info Rental -->
    <div style="background: #f9fafb; padding: 24px; border-radius: 8px; margin-bottom: 24px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            
            <div>
                <h4 style="margin-bottom: 16px; color: #1f2937;">Detail Rental:</h4>
                <div style="margin-bottom: 12px;">
                    <span style="color: #6b7280;">PS Station:</span><br>
                    <strong style="font-size: 20px; color: #2563eb;">
                        <?php echo htmlspecialchars($rental['nomor_ps']); ?>
                    </strong>
                </div>
                <div style="margin-bottom: 12px;">
                    <span style="color: #6b7280;">Customer:</span><br>
                    <strong><?php echo htmlspecialchars($rental['nama_customer']); ?></strong>
                    <?php if ($rental['tipe_customer'] == 'member'): ?>
                        <span class="badge badge-info">Member</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Umum</span>
                    <?php endif; ?>
                </div>
                <div style="margin-bottom: 12px;">
                    <span style="color: #6b7280;">No. Telp:</span><br>
                    <strong><?php echo htmlspecialchars($rental['no_telp']); ?></strong>
                </div>
                <div>
                    <span style="color: #6b7280;">Petugas:</span><br>
                    <strong><?php echo htmlspecialchars($rental['petugas']); ?></strong>
                </div>
            </div>

            <div>
                <h4 style="margin-bottom: 16px; color: #1f2937;">Waktu & Durasi:</h4>
                <div style="margin-bottom: 12px;">
                    <span style="color: #6b7280;">Waktu Mulai:</span><br>
                    <strong><?php echo formatDateTime($rental['waktu_mulai']); ?></strong>
                </div>
                <div style="margin-bottom: 12px;">
                    <span style="color: #6b7280;">Waktu Selesai:</span><br>
                    <strong><?php echo formatDateTime(date('Y-m-d H:i:s')); ?></strong>
                </div>
                <div style="margin-bottom: 12px;">
                    <span style="color: #6b7280;">Durasi Aktual:</span><br>
                    <strong style="font-size: 18px; color: #f59e0b;">
                        <?php echo $jam; ?> jam <?php echo $menit; ?> menit
                    </strong>
                </div>
                <div>
                    <span style="color: #6b7280;">Durasi Dibulatkan:</span><br>
                    <strong style="font-size: 18px; color: #2563eb;">
                        <?php echo $durasi_jam_dibulatkan; ?> jam
                    </strong>
                </div>
            </div>

        </div>
    </div>

    <!-- Perhitungan Biaya -->
    <div style="background: #eff6ff; padding: 24px; border-radius: 8px; margin-bottom: 24px;">
        <h4 style="margin-bottom: 16px; color: #1e40af;">Perhitungan:</h4>
        
        <?php if ($rental['tipe_customer'] == 'member'): ?>
        
        <!-- MEMBER -->
        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #bfdbfe;">
            <span>Saldo Jam Sebelum:</span>
            <strong><?php echo number_format($rental['saldo_jam'], 1); ?> jam</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #bfdbfe;">
            <span>Jam Terpakai:</span>
            <strong style="color: #ef4444;">- <?php echo $jam_dipakai; ?> jam</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 16px 0; background: white; margin: 12px -12px -12px -12px; padding: 20px 12px; border-radius: 0 0 8px 8px;">
            <span style="font-weight: 600; font-size: 16px;">Saldo Jam Setelah:</span>
            <strong style="color: #2563eb; font-size: 24px;">
                <?php echo number_format($saldo_setelah, 1); ?> jam
            </strong>
        </div>
        
        <?php if ($saldo_setelah < 2): ?>
        <div style="background: #fef3c7; padding: 16px; border-radius: 8px; margin-top: 16px; color: #92400e;">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Perhatian:</strong> Saldo jam member tinggal <?php echo number_format($saldo_setelah, 1); ?> jam. 
            Sarankan untuk top-up!
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        
        <!-- UMUM -->
        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #bfdbfe;">
            <span>Durasi:</span>
            <strong><?php echo $durasi_jam_dibulatkan; ?> jam</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #bfdbfe;">
            <span>Harga per Jam:</span>
            <strong><?php echo formatRupiah($rental['harga_per_jam']); ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 16px 0; background: white; margin: 12px -12px -12px -12px; padding: 20px 12px; border-radius: 0 0 8px 8px;">
            <span style="font-weight: 600; font-size: 16px;">TOTAL BAYAR:</span>
            <strong style="color: #10b981; font-size: 28px;">
                <?php echo formatRupiah($total_harga); ?>
            </strong>
        </div>
        
        <?php endif; ?>
    </div>

    <!-- Form Submit -->
    <form action="config/rental_proses.php?action=selesai" method="POST">
        <input type="hidden" name="rental_id" value="<?php echo $rental['id']; ?>">
        <input type="hidden" name="durasi_jam" value="<?php echo $durasi_jam_dibulatkan; ?>">
        <input type="hidden" name="total_harga" value="<?php echo $total_harga; ?>">
        
        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn btn-success" style="font-size: 16px; padding: 12px 24px;">
                <i class="fas fa-check-circle"></i> Konfirmasi Selesai
            </button>
            <a href="index.php?page=list_rental" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>

</div>