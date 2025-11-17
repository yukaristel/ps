<?php
/**
 * =============================================
 * HISTORY TOP-UP MEMBER
 * File: modul/member/history_topup.php
 * =============================================
 */

// Ambil ID member dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data member
$member = query("SELECT * FROM members WHERE id = $id");

if (count($member) == 0) {
    redirect('index.php?page=list_member', 'Data member tidak ditemukan!', 'error');
}

$member = $member[0];

// Ambil history top-up
$history = query("
    SELECT 
        t.*,
        u.nama_lengkap as petugas
    FROM member_topups t
    LEFT JOIN users u ON t.user_id = u.id
    WHERE t.member_id = $id
    ORDER BY t.created_at DESC
");

// Hitung total
$total_topup = 0;
$total_jam = 0;
foreach ($history as $h) {
    $total_topup += $h['paket_harga'];
    $total_jam += $h['jam_ditambahkan'];
}
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-history"></i> History Top-up
    </h1>
    <a href="index.php?page=list_member" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<!-- Info Member -->
<div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
    
    <div class="card">
        <div style="padding: 20px;">
            <h3 style="margin-bottom: 12px; color: #1f2937;">
                <?php echo htmlspecialchars($member['nama']); ?>
            </h3>
            <p style="color: #6b7280; margin: 0;">
                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($member['no_telp']); ?>
            </p>
        </div>
    </div>

    <div class="card">
        <div style="padding: 20px; text-align: center;">
            <div style="font-size: 28px; font-weight: 700; color: #2563eb; margin-bottom: 4px;">
                <?php echo number_format($member['saldo_jam'], 1); ?>
            </div>
            <p style="color: #6b7280; margin: 0; font-size: 14px;">Saldo Jam Saat Ini</p>
        </div>
    </div>

    <div class="card">
        <div style="padding: 20px; text-align: center;">
            <div style="font-size: 28px; font-weight: 700; color: #10b981; margin-bottom: 4px;">
                <?php echo count($history); ?>
            </div>
            <p style="color: #6b7280; margin: 0; font-size: 14px;">Total Transaksi</p>
        </div>
    </div>

</div>

<!-- History Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Top-up</h3>
    </div>

    <?php if (count($history) > 0): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Tanggal & Waktu</th>
                    <th>Paket</th>
                    <th>Jam Ditambahkan</th>
                    <th>Harga</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($history as $h): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo formatDateTime($h['created_at']); ?></td>
                    <td>
                        <strong>Paket <?php echo $h['paket_jam']; ?> Jam</strong>
                    </td>
                    <td>
                        <span class="badge badge-success">
                            +<?php echo number_format($h['jam_ditambahkan'], 1); ?> Jam
                        </span>
                    </td>
                    <td><?php echo formatRupiah($h['paket_harga']); ?></td>
                    <td><?php echo htmlspecialchars($h['petugas']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: #f9fafb; font-weight: 600;">
                    <td colspan="3" style="text-align: right;">TOTAL:</td>
                    <td>
                        <span class="badge badge-info">
                            <?php echo number_format($total_jam, 1); ?> Jam
                        </span>
                    </td>
                    <td><?php echo formatRupiah($total_topup); ?></td>
                    <td>-</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 40px; color: #9ca3af;">
        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px;"></i>
        <p>Belum ada riwayat top-up</p>
        <a href="index.php?page=topup_member&id=<?php echo $member['id']; ?>" class="btn btn-success" style="margin-top: 16px;">
            <i class="fas fa-plus-circle"></i> Top-up Sekarang
        </a>
    </div>
    <?php endif; ?>
</div>