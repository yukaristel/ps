<?php
/**
 * =============================================
 * FORM EDIT PS STATION
 * File: modul/ps_station/edit_ps.php
 * =============================================
 */

// Cek akses admin
if ($_SESSION['role'] != 'admin') {
    redirect('index.php?page=dashboard', 'Akses ditolak!', 'error');
}

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data PS berdasarkan ID
$ps = query("SELECT * FROM ps_stations WHERE id = $id");

// Cek apakah data ditemukan
if (count($ps) == 0) {
    redirect('index.php?page=list_ps', 'Data PS tidak ditemukan!', 'error');
}

$ps = $ps[0];
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit"></i> Edit PS Station
    </h1>
    <a href="index.php?page=list_ps" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit PS: <?php echo htmlspecialchars($ps['nomor_ps']); ?></h3>
    </div>

    <form action="config/ps_station_proses.php?action=update" method="POST">
        
        <input type="hidden" name="id" value="<?php echo $ps['id']; ?>">

        <div class="form-group">
            <label class="form-label">Nomor PS <span style="color: red;">*</span></label>
            <input 
                type="text" 
                name="nomor_ps" 
                class="form-control" 
                value="<?php echo htmlspecialchars($ps['nomor_ps']); ?>"
                required
                autocomplete="off"
            >
        </div>

        <div class="form-group">
            <label class="form-label">Status <span style="color: red;">*</span></label>
            <select name="status" class="form-control" required>
                <option value="">-- Pilih Status --</option>
                <option value="tersedia" <?php echo ($ps['status'] == 'tersedia') ? 'selected' : ''; ?>>Tersedia</option>
                <option value="dipakai" <?php echo ($ps['status'] == 'dipakai') ? 'selected' : ''; ?> disabled>Dipakai (Tidak bisa diubah manual)</option>
                <option value="maintenance" <?php echo ($ps['status'] == 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
            </select>
            <small style="color: #6b7280;">Status "Dipakai" akan otomatis berubah saat ada rental aktif</small>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="index.php?page=list_ps" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>

    </form>
</div>