<?php
// Pengaturan Koneksi Basis Data
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', '');     
define('DB_NAME', 'ppdb_smk_cahaya'); // Pastikan ini nama database yang benar

// Mulai sesi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Fungsi koneksi basis data menggunakan MySQLi
 */
function connectDB() {
    // Membuat koneksi
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Memeriksa koneksi
    if ($conn->connect_error) {
        // Hentikan eksekusi dan tampilkan pesan error
        die("Koneksi gagal: " . $conn->connect_error);
    }
    return $conn;
}

// Tambahkan fungsi untuk cek status login dan role
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function isAdmin() {
    // Cek apakah user sudah login dan role-nya adalah Admin
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin');
}

function isSiswa() {
    // Cek apakah user sudah login dan role-nya adalah Siswa
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'Siswa');
}
function isKepsek() {
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'Kepsek'); // Harus 'Kepsek'
}
// ... (Lanjutan dari fungsi connectDB(), checkAuth(), dll. di config.php) ...

/**
 * Fungsi untuk mendapatkan ID Role dari nama peran
 */
function getRoleIdByName($conn, $roleName) {
    $sql = "SELECT id_role FROM roles WHERE role_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $roleName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $stmt->close();
        return $row['id_role'];
    }
    $stmt->close();
    return null; // Jika role tidak ditemukan
}
function isPendaftarFormFilled() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Siswa') {
        return false;
    }
    
    $conn = connectDB();
    $user_id = $_SESSION['user_id'];
    
    // Ambil id_pendaftar dari tabel users
    $sql = "SELECT id_pendaftar FROM users WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    // Jika id_pendaftar BUKAN NULL, berarti form sudah diisi
    return $user && !is_null($user['id_pendaftar']);
}
// config.php (Versi Terbaru dan Lebih Kuat)

function generateNoPendaftaran($conn) {
    
    $year_prefix = date("Y");
    $prefix = "PPDB-" . $year_prefix;
    
    // 1. Cari nomor pendaftaran terakhir yang memiliki prefix tahun saat ini
    $sql = "SELECT no_pendaftaran FROM pendaftar 
            WHERE no_pendaftaran LIKE ? 
            ORDER BY no_pendaftaran DESC 
            LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    $search_pattern = $prefix . '-%';
    $stmt->bind_param("s", $search_pattern);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    
    $last_id_number = 0;

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_no = $row['no_pendaftaran']; // Contoh: PPDB-2025-0001
        
        // Ekstrak angka urutan dari belakang (e.g., mengambil 0001)
        // Memastikan nomor urut diambil dengan benar
        $parts = explode('-', $last_no);
        $last_id_number = (int)end($parts); // Mengambil angka 1
    }

    // 2. Tentukan nomor urut berikutnya
    $next_id_number = $last_id_number + 1; 
    
    // 3. Format nomor urut menjadi 4 digit
    $no_urut = str_pad($next_id_number, 4, '0', STR_PAD_LEFT);
    
    // 4. Gabungkan
    return $prefix . "-" . $no_urut;
}
function getPendaftarDetail($conn, $id_pendaftar) {
    $sql = "SELECT * FROM pendaftar WHERE id_pendaftar = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pendaftar);
    $stmt->execute();
    $result = $stmt->get_result();
    $detail = $result->fetch_assoc();
    $stmt->close();
    return $detail;
}
function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $conn = connectDB();
    $user_id = $_SESSION['user_id'];
    
    // Ambil data user, termasuk id_pendaftar
    $sql = "SELECT id_user, username, nama_lengkap, id_pendaftar FROM users WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $user;
}
function getStudentFullData($conn, $id_user) {
    // Query JOIN untuk mengambil data user, pendaftar, dan status verifikasi
    $sql = "SELECT u.username, u.nama_lengkap AS user_nama, 
                   p.*, j.nama_keahlian FROM users u
            JOIN pendaftar p ON u.id_pendaftar = p.id_pendaftar
            JOIN jurusan j ON j.id_jurusan = p.id_jurusan_pilihan
            WHERE u.id_user = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    
    return $data;
}
?>