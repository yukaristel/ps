<?php
/**
 * =============================================
 * DAFTAR MEMBER
 * File: modul/member/list_member.php
 * =============================================
 */

// Ambil semua data member
$members = query("
    SELECT * FROM members 
    ORDER BY nama ASC
");
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-users"></i> Member
    </h1>
    <a href="index.php?page=tambah_member" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Member Baru
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Semua Member</h3>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Member</th>
                    <th>No. Telp</th>
                    <th>Saldo Jam</th>
                    <th>Total Deposit</th>
                    <th>Terdaftar</th>
                    <th width="25%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($members) > 0): ?>
                    <?php $no = 1; foreach ($members as $member): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($member['nama']); ?></strong>
                        </td>
                        <td>
                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($member['no_telp']); ?>
                        </td>
                        <td>
                            <?php if ($member['saldo_jam'] <= 2): ?>
                                <span class="badge badge-danger">
                                    <?php echo number_format($member['saldo_jam'], 1); ?> Jam
                                </span>
                            <?php elseif ($member['saldo_jam'] <= 5): ?>
                                <span class="badge badge-warning">
                                    <?php echo number_format($member['saldo_jam'], 1); ?> Jam
                                </span>
                            <?php else: ?>
                                <span class="badge badge-success">
                                    <?php echo number_format($member['saldo_jam'], 1); ?> Jam
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo formatRupiah($member['total_deposit']); ?></td>
                        <td><?php echo formatTanggal($member['created_at']); ?></td>
                        <td>
                            <a href="index.php?page=topup_member&id=<?php echo $member['id']; ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-plus-circle"></i> Top-up
                            </a>
                            <a href="index.php?page=edit_member&id=<?php echo $member['id']; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="index.php?page=history_topup&id=<?php echo $member['id']; ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-history"></i> History
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #9ca3af;">
                            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px;"></i>
                            <p>Belum ada data member</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>