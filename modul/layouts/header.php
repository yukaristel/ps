<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental PlayStation - Management System</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="style/css.css">
    
    <!-- Font Awesome (untuk icon) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    
    <?php if (isset($_SESSION['user_id']) && $page != 'login'): ?>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <i class="fas fa-gamepad"></i>
                <span>Rental PS</span>
            </div>
            
            <div class="nav-right">
                <span class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>
                    <span class="badge-role"><?php echo strtoupper($_SESSION['role']); ?></span>
                </span>
                <a href="config/auth_proses.php?action=logout" class="btn-logout" onclick="return confirm('Yakin ingin logout?')">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- SIDEBAR MENU -->
    <?php include 'modul/menu/menu.php'; ?>
    
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="content-wrapper">
            <?php showAlert(); ?>
    <?php else: ?>
    <!-- Login page tidak ada navbar/sidebar -->
    <div class="login-wrapper">
    <?php endif; ?>