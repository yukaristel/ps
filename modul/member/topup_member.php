<?php
/**
 * =============================================
 * FORM TOP-UP SALDO MEMBER
 * File: modul/member/topup_member.php
 * =============================================
 */

// Ambil ID member dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data member
$member = query("SELECT * FROM members WHERE id = $id");

if (count($member) == 0) {
    redirect('index.php?page=list_member', 'Data member tidak ditemukan!', 'error');
}

$member = $member[0];

// Ambil paket member yang aktif
$paket_member = query("
    SELECT * FROM pricing 
    WHERE tipe = 'member' AND is_active = TRUE 
    ORDER BY paket_jam ASC
");
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-plus-circle"></i> Top-up Saldo Member
    </h1>
    <a href="index.php?page=list_member" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
    
    <!-- Info Member -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Member</h3>
        </div>
        <div style="padding: 10px 0;">
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                <span><i class="fas fa-user"></i> Nama</span>
                <strong><?php echo htmlspecialchars($member['nama']); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                <span><i class="fas fa-phone"></i> No. Telp</span>
                <strong><?php echo htmlspecialchars($member['no_telp']); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px 0;">
                <span><i class="fas fa-clock"></i> Saldo Saat Ini</span>
                <strong style="color: #2563eb; font-size: 18px;">
                    <?php echo number_format($member['saldo_jam'], 1); ?> Jam
                </strong>
            </div>
        </div>
    </div>

    <!-- Total Deposit -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Total Deposit</h3>
        </div>
        <div style="padding: 20px 0; text-align: center;">
            <div style="font-size: 32px; font-weight: 700; color: #10b981; margin-bottom: 8px;">
                <?php echo formatRupiah($member['total_deposit']); ?>
            </div>
            <p style="color: #6b7280; margin: 0;">Total yang sudah di-top-up</p>
        </div>
    </div>

</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pilih Paket Top-up</h3>
    </div>

    <form action="config/topup_proses.php?action=topup" method="POST" id="formTopup">
        
        <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">

        <?php if (count($paket_member) > 0): ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
            
            <?php foreach ($paket_member as $paket): ?>
            <label class="paket-card" style="cursor: pointer; border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; text-align: center; transition: all 0.3s;">
                <input 
                    type="radio" 
                    name="paket_id" 
                    value="<?php echo $paket['id']; ?>" 
                    data-jam="<?php echo $paket['paket_jam']; ?>"
                    data-harga="<?php echo $paket['paket_harga']; ?>"
                    required
                    style="display: none;"
                >
                <div style="font-size: 32px; font-weight: 700; color: #2563eb; margin-bottom: 8px;">
                    <?php echo $paket['paket_jam']; ?> Jam
                </div>
                <div style="font-size: 18px; font-weight: 600; color: #10b981;">
                    <?php echo formatRupiah($paket['paket_harga']); ?>
                </div>
                <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #e5e7eb; font-size: 13px; color: #6b7280;">
                    <?php echo formatRupiah($paket['paket_harga'] / $paket['paket_jam']); ?> / jam
                </div>
            </label>
            <?php endforeach; ?>

        </div>

        <div id="ringkasan" style="background: #eff6ff; padding: 20px; border-radius: 8px; margin-bottom: 20px; display: none;">
            <h4 style="margin-bottom: 16px; color: #1e40af;">Ringkasan Top-up:</h4>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span>Paket Dipilih:</span>
                <strong id="paket_terpilih">-</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span>Saldo Saat Ini:</span>
                <strong><?php echo number_format($member['saldo_jam'], 1); ?> Jam</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 1px solid #bfdbfe;">
                <span style="font-weight: 600;">Saldo Setelah Top-up:</span>
                <strong style="color: #2563eb; font-size: 18px;" id="saldo_baru">-</strong>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check-circle"></i> Proses Top-up
            </button>
            <a href="index.php?page=list_member" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>

        <?php else: ?>
        
        <div style="text-align: center; padding: 40px; color: #9ca3af;">
            <i class="fas fa-exclamation-circle" style="font-size: 48px; margin-bottom: 16px;"></i>
            <p>Belum ada paket member yang tersedia!</p>
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <a href="index.php?page=tambah_pricing" class="btn btn-primary" style="margin-top: 16px;">
                <i class="fas fa-plus"></i> Tambah Paket
            </a>
            <?php endif; ?>
        </div>

        <?php endif; ?>

    </form>
</div>

<script>
// JavaScript untuk interaktif pemilihan paket
document.addEventListener('DOMContentLoaded', function() {
    const paketCards = document.querySelectorAll('.paket-card');
    const ringkasan = document.getElementById('ringkasan');
    const saldoSekarang = <?php echo $member['saldo_jam']; ?>;
    
    paketCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove active class from all
            paketCards.forEach(c => {
                c.style.borderColor = '#e5e7eb';
                c.style.background = 'white';
            });
            
            // Add active class to clicked
            this.style.borderColor = '#2563eb';
            this.style.background = '#eff6ff';
            
            // Get data
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            const jam = parseFloat(radio.dataset.jam);
            const harga = parseFloat(radio.dataset.harga);
            const saldoBaru = saldoSekarang + jam;
            
            // Update ringkasan
            document.getElementById('paket_terpilih').textContent = jam + ' Jam - Rp ' + harga.toLocaleString('id-ID');
            document.getElementById('saldo_baru').textContent = saldoBaru.toFixed(1) + ' Jam';
            ringkasan.style.display = 'block';
        });
    });
});
</script>