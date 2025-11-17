<?php
/**
 * =============================================
 * DAFTAR RENTAL AKTIF
 * File: modul/rental/list_rental.php
 * =============================================
 */

// Ambil rental yang sedang berlangsung
$rental_aktif = query("
    SELECT 
        r.id,
        r.waktu_mulai,
        r.tipe_customer,
        r.harga_per_jam,
        ps.nomor_ps,
        ps.status as ps_status,
        COALESCE(m.nama, 'Customer Umum') as nama_customer,
        COALESCE(m.no_telp, '-') as no_telp,
        COALESCE(m.saldo_jam, 0) as saldo_jam,
        u.nama_lengkap as petugas,
        TIMESTAMPDIFF(MINUTE, r.waktu_mulai, NOW()) as durasi_menit
    FROM rentals r
    LEFT JOIN ps_stations ps ON r.ps_station_id = ps.id
    LEFT JOIN members m ON r.member_id = m.id
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.status = 'berlangsung'
    ORDER BY r.waktu_mulai ASC
");
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-clock"></i> Rental Aktif
    </h1>
    <a href="index.php?page=mulai_rental" class="btn btn-primary">
        <i class="fas fa-play-circle"></i> Mulai Rental Baru
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Rental Sedang Berlangsung</h3>
    </div>

    <?php if (count($rental_aktif) > 0): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>PS</th>
                    <th>Customer</th>
                    <th>Tipe</th>
                    <th>Mulai</th>
                    <th>Durasi</th>
                    <th>Harga/Jam</th>
                    <th>Est. Biaya</th>
                    <th>Petugas</th>
                    <th width="12%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($rental_aktif as $rental): ?>
                <?php
                    $jam = floor($rental['durasi_menit'] / 60);
                    $menit = $rental['durasi_menit'] % 60;
                    $durasi_jam_decimal = $rental['durasi_menit'] / 60;
                    
                    // Estimasi biaya
                    if ($rental['tipe_customer'] == 'member') {
                        $est_biaya = 0; // Member pakai saldo jam
                    } else {
                        $est_biaya = ceil($durasi_jam_decimal) * $rental['harga_per_jam'];
                    }
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td>
                        <strong style="font-size: 16px; color: #2563eb;">
                            <?php echo htmlspecialchars($rental['nomor_ps']); ?>
                        </strong>
                    </td>
                    <td>
                        <div>
                            <strong><?php echo htmlspecialchars($rental['nama_customer']); ?></strong>
                        </div>
                        <small style="color: #6b7280;">
                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($rental['no_telp']); ?>
                        </small>
                        <?php if ($rental['tipe_customer'] == 'member'): ?>
                        <br>
                        <small style="color: #10b981;">
                            <i class="fas fa-clock"></i> Saldo: <?php echo number_format($rental['saldo_jam'], 1); ?> jam
                        </small>
                        <?php endif; ?>
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
                        <strong><?php echo $jam; ?>j <?php echo $menit; ?>m</strong>
                    </td>
                    <td><?php echo formatRupiah($rental['harga_per_jam']); ?></td>
                    <td>
                        <?php if ($rental['tipe_customer'] == 'member'): ?>
                            <span style="color: #10b981;">Saldo Jam</span>
                        <?php else: ?>
                            <?php echo formatRupiah($est_biaya); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <small><?php echo htmlspecialchars($rental['petugas']); ?></small>
                    </td>
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
    <div style="text-align: center; padding: 60px; color: #9ca3af;">
        <i class="fas fa-inbox" style="font-size: 64px; margin-bottom: 20px;"></i>
        <h3 style="color: #6b7280; margin-bottom: 12px;">Tidak Ada Rental Aktif</h3>
        <p style="margin-bottom: 24px;">Belum ada rental yang sedang berlangsung</p>
        <a href="index.php?page=mulai_rental" class="btn btn-primary">
            <i class="fas fa-play-circle"></i> Mulai Rental Baru
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Summary -->
<?php if (count($rental_aktif) > 0): ?>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
    <div class="card">
        <div style="padding: 20px; text-align: center;">
            <div style="font-size: 32px; font-weight: 700; color: #2563eb; margin-bottom: 8px;">
                <?php echo count($rental_aktif); ?>
            </div>
            <p style="color: #6b7280; margin: 0;">Total Rental Aktif</p>
        </div>
    </div>
    
    <div class="card">
        <div style="padding: 20px; text-align: center;">
            <div style="font-size: 32px; font-weight: 700; color: #f59e0b; margin-bottom: 8px;">
                <?php 
                $umum = 0;
                foreach ($rental_aktif as $r) {
                    if ($r['tipe_customer'] == 'umum') $umum++;
                }
                echo $umum;
                ?>
            </div>
            <p style="color: #6b7280; margin: 0;">Customer Umum</p>
        </div>
    </div>
    
    <div class="card">
        <div style="padding: 20px; text-align: center;">
            <div style="font-size: 32px; font-weight: 700; color: #10b981; margin-bottom: 8px;">
                <?php echo count($rental_aktif) - $umum; ?>
            </div>
            <p style="color: #6b7280; margin: 0;">Member</p>
        </div>
    </div>
</div>
<?php endif; ?>