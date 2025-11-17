<?php
/**
 * =============================================
 * DAFTAR PS STATION
 * File: modul/ps_station/list_ps.php
 * =============================================
 */

// Ambil semua data PS Station
$ps_stations = query("
    SELECT * FROM ps_stations 
    ORDER BY nomor_ps ASC
");
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-gamepad"></i> PS Station
    </h1>
    <?php if ($_SESSION['role'] == 'admin'): ?>
    <a href="index.php?page=tambah_ps" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah PS Baru
    </a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Semua PS Station</h3>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nomor PS</th>
                    <th>Status</th>
                    <th>Terakhir Update</th>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                    <th width="20%">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (count($ps_stations) > 0): ?>
                    <?php $no = 1; foreach ($ps_stations as $ps): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <strong style="font-size: 16px;"><?php echo htmlspecialchars($ps['nomor_ps']); ?></strong>
                        </td>
                        <td>
                            <?php if ($ps['status'] == 'tersedia'): ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Tersedia
                                </span>
                            <?php elseif ($ps['status'] == 'dipakai'): ?>
                                <span class="badge badge-warning">
                                    <i class="fas fa-play-circle"></i> Dipakai
                                </span>
                            <?php else: ?>
                                <span class="badge badge-danger">
                                    <i class="fas fa-tools"></i> Maintenance
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo formatDateTime($ps['updated_at']); ?></td>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                        <td>
                            <a href="index.php?page=edit_ps&id=<?php echo $ps['id']; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            
                            <?php if ($ps['status'] != 'dipakai'): ?>
                            <a href="config/ps_station_proses.php?action=delete&id=<?php echo $ps['id']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirmDelete('Yakin ingin menghapus PS <?php echo $ps['nomor_ps']; ?>?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                            <?php else: ?>
                            <button class="btn btn-danger btn-sm" disabled title="PS sedang digunakan">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #9ca3af;">
                            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px;"></i>
                            <p>Belum ada data PS Station</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>