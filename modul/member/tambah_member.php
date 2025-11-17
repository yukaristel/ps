<?php
/**
 * =============================================
 * FORM TAMBAH MEMBER
 * File: modul/member/tambah_member.php
 * =============================================
 */
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-plus"></i> Tambah Member
    </h1>
    <a href="index.php?page=list_member" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah Member Baru</h3>
    </div>

    <form action="config/member_proses.php?action=create" method="POST">
        
        <div class="form-group">
            <label class="form-label">Nama Lengkap <span style="color: red;">*</span></label>
            <input 
                type="text" 
                name="nama" 
                class="form-control" 
                placeholder="Masukkan nama lengkap"
                required
                autocomplete="off"
            >
        </div>

        <div class="form-group">
            <label class="form-label">No. Telepon <span style="color: red;">*</span></label>
            <input 
                type="text" 
                name="no_telp" 
                class="form-control" 
                placeholder="Contoh: 081234567890"
                required
                autocomplete="off"
                pattern="[0-9]{10,13}"
                title="Nomor telepon harus 10-13 digit"
            >
            <small style="color: #6b7280;">Masukkan nomor yang aktif (10-13 digit)</small>
        </div>

        <div style="background: #eff6ff; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <p style="margin: 0; color: #1e40af;">
                <i class="fas fa-info-circle"></i> 
                <strong>Info:</strong> Member baru akan memiliki saldo 0 jam. Silakan lakukan top-up setelah mendaftar.
            </p>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="index.php?page=list_member" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>

    </form>
</div>