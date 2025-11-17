<?php
/**
 * =============================================
 * FORM EDIT PRICING
 * File: modul/pricing/edit_pricing.php
 * =============================================
 */

// Cek akses admin
if ($_SESSION['role'] != 'admin') {
    redirect('index.php?page=dashboard', 'Akses ditolak!', 'error');
}

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data pricing berdasarkan ID
$pricing = query("SELECT * FROM pricing WHERE id = $id");

// Cek apakah data ditemukan
if (count($pricing) == 0) {
    redirect('index.php?page=list_pricing', 'Data pricing tidak ditemukan!', 'error');
}

$pricing = $pricing[0];
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit"></i> Edit Pricing
    </h1>
    <a href="index.php?page=list_pricing" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            Form Edit: 
            <?php if ($pricing['tipe'] == 'reguler'): ?>
                Harga Reguler
            <?php else: ?>
                Paket Member <?php echo $pricing['paket_jam']; ?> Jam
            <?php endif; ?>
        </h3>
    </div>

    <form action="config/pricing_proses.php?action=update" method="POST">
        
        <input type="hidden" name="id" value="<?php echo $pricing['id']; ?>">
        <input type="hidden" name="tipe" value="<?php echo $pricing['tipe']; ?>">

        <?php if ($pricing['tipe'] == 'reguler'): ?>
        
        <!-- FIELD REGULER -->
        <div class="form-group">
            <label class="form-label">Harga per Jam <span style="color: red;">*</span></label>
            <input 
                type="number" 
                name="harga_per_jam" 
                class="form-control" 
                value="<?php echo $pricing['harga_per_jam']; ?>"
                required
                min="0"
                step="100"
            >
        </div>

        <?php else: ?>
        
        <!-- FIELD MEMBER -->
        <div class="form-group">
            <label class="form-label">Jumlah Jam Paket <span style="color: red;">*</span></label>
            <input 
                type="number" 
                name="paket_jam" 
                id="paket_jam"
                class="form-control" 
                value="<?php echo $pricing['paket_jam']; ?>"
                required
                min="1"
                onkeyup="hitungHargaPerJam()"
            >
        </div>

        <div class="form-group">
            <label class="form-label">Harga Paket <span style="color: red;">*</span></label>
            <input 
                type="number" 
                name="paket_harga" 
                id="paket_harga"
                class="form-control" 
                value="<?php echo $pricing['paket_harga']; ?>"
                required
                min="0"
                step="1000"
                onkeyup="hitungHargaPerJam()"
            >
        </div>

        <div id="info_harga_per_jam" style="background: #eff6ff; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <p style="margin: 0; color: #1e40af;">
                <i class="fas fa-calculator"></i> 
                <strong>Harga per Jam:</strong> <span id="result_harga_per_jam">
                    <?php echo formatRupiah($pricing['paket_harga'] / $pricing['paket_jam']); ?>
                </span>
            </p>
        </div>

        <?php endif; ?>

        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="is_active" class="form-control">
                <option value="1" <?php echo ($pricing['is_active']) ? 'selected' : ''; ?>>Aktif</option>
                <option value="0" <?php echo (!$pricing['is_active']) ? 'selected' : ''; ?>>Nonaktif</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="index.php?page=list_pricing" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>

    </form>
</div>

<?php if ($pricing['tipe'] == 'member'): ?>
<script>
function hitungHargaPerJam() {
    const jam = parseFloat(document.getElementById('paket_jam').value) || 0;
    const harga = parseFloat(document.getElementById('paket_harga').value) || 0;
    
    if (jam > 0 && harga > 0) {
        const hargaPerJam = harga / jam;
        document.getElementById('result_harga_per_jam').textContent = 
            'Rp ' + hargaPerJam.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }
}
</script>
<?php endif; ?>