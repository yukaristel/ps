<?php
/**
 * =============================================
 * FORM EDIT MEMBER
 * File: modul/member/edit_member.php
 * =============================================
 */

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data member berdasarkan ID
$member = query("SELECT * FROM members WHERE id = $id");

// Cek apakah data ditemukan
if (count($member) == 0) {
    redirect('index.php?page=list_member', 'Data member tidak ditemukan!', 'error');
}

$member = $member[0];
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit"></i> Edit Member
    </h1>
    <a href="index.php?page=list_member" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Member: <?php echo htmlspecialchars($member['nama']); ?></h3>
    </div>

    <form action="config/member_proses.php?action=update" method="POST">
        
        <input type="hidden" name="id" value="<?php echo $member['id']; ?>">

        <div class="form-group">
            <label class="form-label">Nama Lengkap <span style="color: red;">*</span></label>
            <input 
                type="text" 
                name="nama" 
                class="form-control" 
                value="<?php echo htmlspecialchars($member['nama']); ?>"
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
                value="<?php echo htmlspecialchars($member['no_telp']); ?>"
                required
                autocomplete="off"
                pattern="[0-9]{10,13}"
                title="Nomor telepon harus 10-13 digit"
            >
        </div>

        <div style="background: #fef3c7; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <p style="margin: 0; color: #92400e;">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Perhatian:</strong> Saldo jam tidak bisa diubah di sini. Gunakan fitur Top-up untuk menambah saldo.
            </p>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="index.php?page=list_member" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>

    </form>
</div>