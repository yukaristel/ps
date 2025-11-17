<?php
/**
 * =============================================
 * FORM TAMBAH PRICING
 * File: modul/pricing/tambah_pricing.php
 * =============================================
 */

// Cek akses admin
if ($_SESSION['role'] != 'admin') {
    redirect('index.php?page=dashboard', 'Akses ditolak!', 'error');
}
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-plus"></i> Tambah Harga
    </h1>
    <a href="index.php?page=list_pricing" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah Harga / Paket Baru</h3>
    </div>

    <form action="config/pricing_proses.php?action=create" method="POST" id="formPricing">
        
        <div class="form-group">
            <label class="form-label">Tipe <span style="color: red;">*</span></label>
            <select name="tipe" id="tipe" class="form-control" required onchange="toggleFields()">
                <option value="">-- Pilih Tipe --</option>
                <option value="reguler">Reguler (Per Jam)</option>
                <option value="member">Member (Paket)</option>
            </select>
        </div>

        <!-- FIELD UNTUK REGULER -->
        <div id="field_reguler" style="display: none;">
            <div class="form-group">
                <label class="form-label">Harga per Jam <span style="color: red;">*</span></label>
                <input 
                    type="number" 
                    name="harga_per_jam" 
                    id="harga_per_jam"
                    class="form-control" 
                    placeholder="Contoh: 5000"
                    min="0"
                    step="100"
                >
                <small style="color: #6b7280;">Harga rental per jam untuk customer umum</small>
            </div>
        </div>

        <!-- FIELD UNTUK MEMBER -->
        <div id="field_member" style="display: none;">
            <div class="form-group">
                <label class="form-label">Jumlah Jam Paket <span style="color: red;">*</span></label>
                <input 
                    type="number" 
                    name="paket_jam" 
                    id="paket_jam"
                    class="form-control" 
                    placeholder="Contoh: 10"
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
                    placeholder="Contoh: 45000"
                    min="0"
                    step="1000"
                    onkeyup="hitungHargaPerJam()"
                >
            </div>

            <div id="info_harga_per_jam" style="background: #eff6ff; padding: 16px; border-radius: 8px; display: none;">
                <p style="margin: 0; color: #1e40af;">
                    <i class="fas fa-calculator"></i> 
                    <strong>Harga per Jam:</strong> <span id="result_harga_per_jam">-</span>
                </p>
            </div>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <label class="form-label">Status</label>
            <select name="is_active" class="form-control">
                <option value="1" selected>Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="index.php?page=list_pricing" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>

    </form>
</div>

<script>
function toggleFields() {
    const tipe = document.getElementById('tipe').value;
    const fieldReguler = document.getElementById('field_reguler');
    const fieldMember = document.getElementById('field_member');
    
    if (tipe === 'reguler') {
        fieldReguler.style.display = 'block';
        fieldMember.style.display = 'none';
        
        // Set required
        document.getElementById('harga_per_jam').required = true;
        document.getElementById('paket_jam').required = false;
        document.getElementById('paket_harga').required = false;
        
    } else if (tipe === 'member') {
        fieldReguler.style.display = 'none';
        fieldMember.style.display = 'block';
        
        // Set required
        document.getElementById('harga_per_jam').required = false;
        document.getElementById('paket_jam').required = true;
        document.getElementById('paket_harga').required = true;
        
    } else {
        fieldReguler.style.display = 'none';
        fieldMember.style.display = 'none';
    }
}

function hitungHargaPerJam() {
    const jam = parseFloat(document.getElementById('paket_jam').value) || 0;
    const harga = parseFloat(document.getElementById('paket_harga').value) || 0;
    
    if (jam > 0 && harga > 0) {
        const hargaPerJam = harga / jam;
        document.getElementById('result_harga_per_jam').textContent = 
            'Rp ' + hargaPerJam.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        document.getElementById('info_harga_per_jam').style.display = 'block';
    } else {
        document.getElementById('info_harga_per_jam').style.display = 'none';
    }
}
</script>