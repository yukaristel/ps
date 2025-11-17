<?php
/**
 * =============================================
 * DAFTAR PRICING
 * File: modul/pricing/list_pricing.php
 * =============================================
 */

// Cek akses admin
if ($_SESSION['role'] != 'admin') {
    redirect('index.php?page=dashboard', 'Akses ditolak!', 'error');
}

// Ambil semua data pricing
$pricing_reguler = query("SELECT * FROM pricing WHERE tipe = 'reguler' ORDER BY id DESC");
$pricing_member = query("SELECT * FROM pricing WHERE tipe = 'member' ORDER BY paket_jam ASC");
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-tags"></i> Pricing / Harga
    </h1>
    <a href="index.php?page=tambah_pricing" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Harga Baru
    </a>
</div>

<!-- HARGA REGULER -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user"></i> Harga Rental Reguler (Per Jam)
        </h3>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Tipe</th>
                    <th>Harga per Jam</th>
                    <th>Status</th>
                    <th>Terakhir Update</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pricing_reguler) > 0): ?>
                    <?php $no = 1; foreach ($pricing_reguler as $price): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <span class="badge badge-warning">Reguler</span>
                        </td>
                        <td>
                            <strong style="font-size: 18px; color: #2563eb;">
                                <?php echo formatRupiah($price['harga_per_jam']); ?>
                            </strong>
                        </td>
                        <td>
                            <?php if ($price['is_active']): ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            <?php else: ?>
                                <span class="badge badge-danger">
                                    <i class="fas fa-times-circle"></i> Nonaktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo formatDateTime($price['updated_at']); ?></td>
                        <td>
                            <a href="index.php?page=edit_pricing&id=<?php echo $price['id']; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #9ca3af;">
                            Belum ada harga reguler
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- PAKET MEMBER -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users"></i> Paket Member
        </h3>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Tipe</th>
                    <th>Paket Jam</th>
                    <th>Harga Paket</th>
                    <th>Harga per Jam</th>
                    <th>Status</th>
                    <th>Terakhir Update</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pricing_member) > 0): ?>
                    <?php $no = 1; foreach ($pricing_member as $price): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <span class="badge badge-info">Member</span>
                        </td>
                        <td>
                            <strong style="font-size: 16px;">
                                <?php echo $price['paket_jam']; ?> Jam
                            </strong>
                        </td>
                        <td>
                            <strong style="font-size: 18px; color: #10b981;">
                                <?php echo formatRupiah($price['paket_harga']); ?>
                            </strong>
                        </td>
                        <td>
                            <span style="color: #6b7280;">
                                <?php echo formatRupiah($price['paket_harga'] / $price['paket_jam']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($price['is_active']): ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            <?php else: ?>
                                <span class="badge badge-danger">
                                    <i class="fas fa-times-circle"></i> Nonaktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo formatDateTime($price['updated_at']); ?></td>
                        <td>
                            <a href="index.php?page=edit_pricing&id=<?php echo $price['id']; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 30px; color: #9ca3af;">
                            Belum ada paket member
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>