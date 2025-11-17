<?php
/**
 * =============================================
 * FORM TAMBAH PS STATION
 * File: modul/ps_station/tambah_ps.php
 * =============================================
 */

// Cek akses admin
if ($_SESSION['role'] != 'admin') {
    redirect('index.php?page=dashboard', 'Akses ditolak!', 'error');
}
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-plus"></i> Tambah PS Station
    </h1>
    <a href="index.php?page=list_ps" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah PS Baru</h3>
    </div>

    <form action="config/ps_station_proses.php?action=create" method="POST">
        
        <div class="form-group">
            <label class="form-label">Nomor PS <span style="color: red;">*</span></label>
            <input 
                type="text" 
                name="nomor_ps" 
                class="form-control" 
                placeholder="Contoh: PS-001"
                required
                autocomplete="off"
            >
            <small style="color: #6b7280;">Format: PS-XXX (contoh: PS-001, PS-002)</small>
        </div>

        <div class="form-group">
            <label class="form-label">Status <span style="color: red;">*</span></label>
            <select name="status" class="form-control" required>
                <option value="">-- Pilih Status --</option>
                <option value="tersedia" selected>Tersedia</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="index.php?page=list_ps" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>

    </form>
</div>