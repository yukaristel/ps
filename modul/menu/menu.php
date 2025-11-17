<?php
/**
 * =============================================
 * SIDEBAR MENU
 * File: modul/menu/menu.php
 * =============================================
 */

// Get current page
$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<div class="sidebar">
    
    <!-- DASHBOARD -->
    <div class="menu-section">
        <div class="menu-title">Main Menu</div>
        <a href="index.php?page=dashboard" class="menu-item <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </div>

    <!-- TRANSAKSI -->
    <div class="menu-section">
        <div class="menu-title">Transaksi</div>
        
        <a href="index.php?page=list_rental" class="menu-item <?php echo ($current_page == 'list_rental' || $current_page == 'mulai_rental' || $current_page == 'selesai_rental') ? 'active' : ''; ?>">
            <i class="fas fa-clock"></i>
            <span>Rental Aktif</span>
        </a>
        
        <a href="index.php?page=mulai_rental" class="menu-item">
            <i class="fas fa-play-circle"></i>
            <span>Mulai Rental</span>
        </a>
    </div>

    <!-- MASTER DATA -->
    <div class="menu-section">
        <div class="menu-title">Master Data</div>
        
        <a href="index.php?page=list_ps" class="menu-item <?php echo ($current_page == 'list_ps' || $current_page == 'tambah_ps' || $current_page == 'edit_ps') ? 'active' : ''; ?>">
            <i class="fas fa-gamepad"></i>
            <span>PS Station</span>
        </a>
        
        <a href="index.php?page=list_member" class="menu-item <?php echo ($current_page == 'list_member' || $current_page == 'tambah_member' || $current_page == 'edit_member' || $current_page == 'topup_member' || $current_page == 'history_topup') ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span>Member</span>
        </a>
        
        <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="index.php?page=list_pricing" class="menu-item <?php echo ($current_page == 'list_pricing' || $current_page == 'tambah_pricing' || $current_page == 'edit_pricing') ? 'active' : ''; ?>">
            <i class="fas fa-tags"></i>
            <span>Pricing</span>
        </a>
        <?php endif; ?>
    </div>

    <!-- LAPORAN -->
    <div class="menu-section">
        <div class="menu-title">Laporan</div>
        
        <a href="index.php?page=laporan_harian" class="menu-item <?php echo ($current_page == 'laporan_harian') ? 'active' : ''; ?>">
            <i class="fas fa-file-alt"></i>
            <span>Laporan Harian</span>
        </a>
        
        <a href="index.php?page=laporan_bulanan" class="menu-item <?php echo ($current_page == 'laporan_bulanan') ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Laporan Bulanan</span>
        </a>
    </div>

</div>