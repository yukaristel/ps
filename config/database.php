<?php
/**
 * =============================================
 * KONFIGURASI KONEKSI DATABASE
 * File: config/database.php
 * =============================================
 */

// Set timezone ke WIB (Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi Database
define('DB_HOST', '103.112.245.8');
define('DB_USER', 'sinkrone_sinkrone');
define('DB_PASS', 'sinkrone_sinkrone');
define('DB_NAME', 'sinkrone_rental_ps');

// Membuat koneksi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset UTF-8
mysqli_set_charset($conn, "utf8");

/**
 * Fungsi untuk mencegah SQL Injection
 * @param string $data - Data yang akan di-escape
 * @return string - Data yang sudah aman
 */
function escape($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

/**
 * Fungsi untuk menjalankan query SELECT
 * @param string $query - Query SQL
 * @return array|false - Hasil query dalam bentuk array
 */
function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        // Log error untuk debugging
        error_log("Query Error: " . mysqli_error($conn));
        return false;
    }
    
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    
    return $rows;
}

/**
 * Fungsi untuk menjalankan query INSERT, UPDATE, DELETE
 * @param string $query - Query SQL
 * @return bool - TRUE jika berhasil, FALSE jika gagal
 */
function execute($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        // Log error untuk debugging
        error_log("Execute Error: " . mysqli_error($conn));
        return false;
    }
    
    return true;
}

/**
 * Fungsi untuk mendapatkan ID terakhir setelah INSERT
 * @return int - Last insert ID
 */
function getLastId() {
    global $conn;
    return mysqli_insert_id($conn);
}

/**
 * Fungsi untuk mendapatkan jumlah rows yang terpengaruh
 * @return int - Affected rows
 */
function getAffectedRows() {
    global $conn;
    return mysqli_affected_rows($conn);
}

/**
 * Fungsi untuk memulai transaction
 */
function beginTransaction() {
    global $conn;
    mysqli_begin_transaction($conn);
}

/**
 * Fungsi untuk commit transaction
 */
function commit() {
    global $conn;
    mysqli_commit($conn);
}

/**
 * Fungsi untuk rollback transaction
 */
function rollback() {
    global $conn;
    mysqli_rollback($conn);
}

/**
 * Fungsi untuk format rupiah
 * @param float $angka - Angka yang akan diformat
 * @return string - Format rupiah
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Fungsi untuk format tanggal Indonesia
 * @param string $tanggal - Tanggal format Y-m-d atau datetime
 * @return string - Format tanggal Indonesia
 */
function formatTanggal($tanggal) {
    if (empty($tanggal) || $tanggal == '0000-00-00' || $tanggal == '0000-00-00 00:00:00') {
        return '-';
    }
    
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($tanggal);
    $hari = date('d', $timestamp);
    $bulanAngka = date('n', $timestamp);
    $tahun = date('Y', $timestamp);
    
    return $hari . ' ' . $bulan[$bulanAngka] . ' ' . $tahun;
}

/**
 * Fungsi untuk format datetime Indonesia
 * @param string $datetime - Datetime format Y-m-d H:i:s
 * @return string - Format datetime Indonesia
 */
function formatDateTime($datetime) {
    if (empty($datetime) || $datetime == '0000-00-00 00:00:00') {
        return '-';
    }
    
    $timestamp = strtotime($datetime);
    return formatTanggal($datetime) . ' ' . date('H:i', $timestamp);
}

/**
 * Fungsi untuk validasi input
 * @param string $data - Data yang akan divalidasi
 * @return string - Data yang sudah dibersihkan
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Fungsi untuk redirect dengan pesan
 * @param string $url - URL tujuan
 * @param string $message - Pesan yang akan ditampilkan
 * @param string $type - Tipe pesan (success, error, warning, info)
 */
function redirect($url, $message = '', $type = 'success') {
    if (!empty($message)) {
        $_SESSION['alert'] = [
            'message' => $message,
            'type' => $type
        ];
    }
    header("Location: $url");
    exit();
}

/**
 * Fungsi untuk menampilkan alert
 * @return string - HTML alert
 */
function showAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        $type = $alert['type'];
        $message = $alert['message'];
        
        // Warna berdasarkan tipe
        $colors = [
            'success' => 'green',
            'error' => 'red',
            'warning' => 'orange',
            'info' => 'blue'
        ];
        
        $color = $colors[$type] ?? 'gray';
        
        echo "<div style='padding: 15px; margin-bottom: 20px; border-radius: 5px; background-color: {$color}; color: white;'>
                <strong>" . ucfirst($type) . ":</strong> {$message}
              </div>";
        
        unset($_SESSION['alert']);
    }
}

// Testing koneksi (opsional - bisa di-comment setelah sukses)
// echo "Koneksi database berhasil!";
?>