<?php
/**
 * =============================================
 * FORM MULAI RENTAL
 * File: modul/rental/mulai_rental.php
 * =============================================
 */

// Ambil PS yang tersedia
$ps_tersedia = query("
    SELECT * FROM ps_stations 
    WHERE status = 'tersedia' 
    ORDER BY nomor_ps ASC
");

// Ambil harga reguler aktif
$harga_reguler = query("
    SELECT * FROM pricing 
    WHERE tipe = 'reguler' AND is_active = TRUE 
    ORDER BY id DESC LIMIT 1
");

// Ambil daftar member
$members = query("
    SELECT * FROM members 
    ORDER BY nama ASC
");
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-play-circle"></i> Mulai Rental
    </h1>
    <a href="index.php?page=list_rental" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<?php if (count($ps_tersedia) == 0): ?>
<!-- Tidak ada PS tersedia -->
<div class="card">
    <div style="text-align: center; padding: 60px; color: #9ca3af;">
        <i class="fas fa-exclamation-triangle" style="font-size: 64px; margin-bottom: 20px; color: #f59e0b;"></i>
        <h3 style="color: #6b7280; margin-bottom: 12px;">Tidak Ada PS Tersedia</h3>
        <p style="margin-bottom: 24px;">Semua PS sedang dipakai atau maintenance</p>
        <a href="index.php?page=list_rental" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<?php elseif (count($harga_reguler) == 0): ?>
<!-- Harga reguler belum diset -->
<div class="card">
    <div style="text-align: center; padding: 60px; color: #9ca3af;">
        <i class="fas fa-exclamation-triangle" style="font-size: 64px; margin-bottom: 20px; color: #ef4444;"></i>
        <h3 style="color: #6b7280; margin-bottom: 12px;">Harga Belum Diatur</h3>
        <p style="margin-bottom: 24px;">Harga rental reguler belum diatur oleh admin</p>
        <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="index.php?page=tambah_pricing" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Harga
        </a>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Mulai Rental Baru</h3>
    </div>

    <form action="config/rental_proses.php?action=mulai" method="POST" id="formRental">
        
        <!-- PILIH PS STATION -->
        <div class="form-group">
            <label class="form-label">Pilih PS Station <span style="color: red;">*</span></label>
            <select name="ps_station_id" class="form-control" required>
                <option value="">-- Pilih PS --</option>
                <?php foreach ($ps_tersedia as $ps): ?>
                <option value="<?php echo $ps['id']; ?>">
                    <?php echo htmlspecialchars($ps['nomor_ps']); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <small style="color: #6b7280;">
                Tersedia: <?php echo count($ps_tersedia); ?> PS
            </small>
        </div>

        <!-- TIPE CUSTOMER -->
        <div class="form-group">
            <label class="form-label">Tipe Customer <span style="color: red;">*</span></label>
            <select name="tipe_customer" id="tipe_customer" class="form-control" required onchange="toggleMember()">
                <option value="">-- Pilih Tipe --</option>
                <option value="umum">Customer Umum</option>
                <option value="member">Member</option>
            </select>
        </div>

        <!-- PILIH MEMBER (hanya muncul jika tipe = member) -->
        <div id="field_member" style="display: none;">
            <div class="form-group">
                <label class="form-label">Pilih Member <span style="color: red;">*</span></label>
                <select name="member_id" id="member_id" class="form-control" onchange="cekSaldoMember()">
                    <option value="">-- Pilih Member --</option>
                    <?php foreach ($members as $m): ?>
                    <option value="<?php echo $m['id']; ?>" 
                            data-nama="<?php echo htmlspecialchars($m['nama']); ?>"
                            data-telp="<?php echo htmlspecialchars($m['no_telp']); ?>"
                            data-saldo="<?php echo $m['saldo_jam']; ?>">
                        <?php echo htmlspecialchars($m['nama']); ?> - 
                        Saldo: <?php echo number_format($m['saldo_jam'], 1); ?> jam
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Info Saldo Member -->
            <div id="info_saldo" style="display: none; background: #eff6ff; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                <h4 style="margin-bottom: 12px; color: #1e40af;">Info Member:</h4>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>Nama:</span>
                    <strong id="member_nama">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>No. Telp:</span>
                    <strong id="member_telp">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding-top: 8px; border-top: 1px solid #bfdbfe;">
                    <span>Saldo Jam:</span>
                    <strong id="member_saldo" style="color: #2563eb; font-size: 18px;">-</strong>
                </div>
            </div>

            <div id="alert_saldo_habis" style="display: none; background: #fee2e2; padding: 16px; border-radius: 8px; margin-bottom: 20px; color: #991b1b;">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Perhatian:</strong> Saldo jam member tidak mencukupi! Silakan top-up terlebih dahulu.
            </div>
        </div>

        <!-- INFO HARGA -->
        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h4 style="margin-bottom: 16px; color: #1f2937;">Informasi Harga:</h4>
            <div id="info_harga_umum">
                <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                    <span>Harga per Jam (Umum):</span>
                    <strong style="color: #2563eb; font-size: 18px;">
                        <?php echo formatRupiah($harga_reguler[0]['harga_per_jam']); ?>
                    </strong>
                </div>
            </div>
            <div id="info_harga_member" style="display: none;">
                <div style="padding: 12px 0;">
                    <span style="color: #10b981;">
                        <i class="fas fa-check-circle"></i> Member menggunakan saldo jam, tidak dikenakan biaya
                    </span>
                </div>
            </div>
        </div>

        <input type="hidden" name="harga_per_jam" value="<?php echo $harga_reguler[0]['harga_per_jam']; ?>">

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn btn-success" id="btnSubmit">
                <i class="fas fa-play-circle"></i> Mulai Rental
            </button>
            <a href="index.php?page=list_rental" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>

    </form>
</div>

<script>
function toggleMember() {
    const tipe = document.getElementById('tipe_customer').value;
    const fieldMember = document.getElementById('field_member');
    const memberId = document.getElementById('member_id');
    const infoHargaUmum = document.getElementById('info_harga_umum');
    const infoHargaMember = document.getElementById('info_harga_member');
    
    if (tipe === 'member') {
        fieldMember.style.display = 'block';
        memberId.required = true;
        infoHargaUmum.style.display = 'none';
        infoHargaMember.style.display = 'block';
    } else {
        fieldMember.style.display = 'none';
        memberId.required = false;
        document.getElementById('info_saldo').style.display = 'none';
        document.getElementById('alert_saldo_habis').style.display = 'none';
        infoHargaUmum.style.display = 'block';
        infoHargaMember.style.display = 'none';
    }
}

function cekSaldoMember() {
    const select = document.getElementById('member_id');
    const option = select.options[select.selectedIndex];
    const btnSubmit = document.getElementById('btnSubmit');
    
    if (option.value) {
        const nama = option.dataset.nama;
        const telp = option.dataset.telp;
        const saldo = parseFloat(option.dataset.saldo);
        
        document.getElementById('member_nama').textContent = nama;
        document.getElementById('member_telp').textContent = telp;
        document.getElementById('member_saldo').textContent = saldo.toFixed(1) + ' jam';
        document.getElementById('info_saldo').style.display = 'block';
        
        // Cek apakah saldo cukup (minimal 0.5 jam)
        if (saldo < 0.5) {
            document.getElementById('alert_saldo_habis').style.display = 'block';
            btnSubmit.disabled = true;
            btnSubmit.style.opacity = '0.5';
            btnSubmit.style.cursor = 'not-allowed';
        } else {
            document.getElementById('alert_saldo_habis').style.display = 'none';
            btnSubmit.disabled = false;
            btnSubmit.style.opacity = '1';
            btnSubmit.style.cursor = 'pointer';
        }
    } else {
        document.getElementById('info_saldo').style.display = 'none';
        document.getElementById('alert_saldo_habis').style.display = 'none';
        btnSubmit.disabled = false;
        btnSubmit.style.opacity = '1';
    }
}
</script>

<?php endif; ?>