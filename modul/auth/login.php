<?php
/**
 * =============================================
 * HALAMAN LOGIN
 * File: modul/auth/login.php
 * =============================================
 */

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    redirect('index.php?page=dashboard');
}
?>

<div class="login-card">
    <div class="login-header">
        <i class="fas fa-gamepad"></i>
        <h2>Rental PlayStation</h2>
        <p>Management System</p>
    </div>

    <?php showAlert(); ?>

    <form action="config/auth_proses.php?action=login" method="POST">
        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-user"></i> Username
            </label>
            <input 
                type="text" 
                name="username" 
                class="form-control" 
                placeholder="Masukkan username"
                required 
                autocomplete="username"
            >
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-lock"></i> Password
            </label>
            <input 
                type="password" 
                name="password" 
                class="form-control" 
                placeholder="Masukkan password"
                required
                autocomplete="current-password"
            >
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 13px; color: #6b7280;">
        <strong>Default Login:</strong><br>
        Admin: <code>admin / admin123</code><br>
        Karyawan: <code>karyawan1 / admin123</code>
    </div>
</div>