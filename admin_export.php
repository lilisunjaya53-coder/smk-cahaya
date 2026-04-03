<?php
require_once 'config.php';

// 1. Keamanan & Otorisasi
checkAuth(); 
if (!isAdmin() && !isKepsek()) {
    header("Location: dashboard.php");
    exit;
}

$conn = connectDB();

// 2. Ambil Parameter Filter
$status_filter = $_GET['status'] ?? 'all';
// Kita ubah ekstensi jadi .xls biar Excel otomatis ngerapiin sebagai tabel
$filename = "PPDB_Lengkap_" . str_replace(' ', '_', $status_filter) . "_" . date('Ymd_His') . ".xls";

// 3. Header untuk format Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// 4. Query Ambil SEMUA Kolom
$sql = "SELECT p.*, j.nama_keahlian 
        FROM pendaftar p 
        LEFT JOIN jurusan j ON p.id_jurusan_pilihan = j.id_jurusan";

if ($status_filter !== 'all') {
    $safe_status = $conn->real_escape_string($status_filter);
    $sql .= " WHERE p.status_verifikasi = '$safe_status'";
}

$sql .= " ORDER BY p.id_pendaftar ASC";
$result = $conn->query($sql);

// 5. Kita pakai format Table HTML (Excel bisa baca ini langsung jadi tabel rapi)
echo "<table border='1'>";
echo "<tr>";
// Header Manusiawi
$headers = [
    'ID', 'No. Daftar', 'Tgl Daftar', 'Nama Lengkap', 'Panggilan', 'NIK Siswa', 
    'NISN', 'L/P', 'Tempat Lahir', 'Tgl Lahir', 'Agama', 'Status Keluarga', 
    'Asal SD', 'Alamat SD', 'Asal SMP', 'Alamat SMP', 'WhatsApp', 'Alamat Domisili', 
    'Nama Ayah', 'NIK Ayah', 'Kerja Ayah', 'Nama Ibu', 'NIK Ibu', 'Kerja Ibu', 
    'Wali', 'Hubungan', 'ID Jurusan', 'File KK', 'File KTP', 'File Ijazah', 'File SKL', 'File Foto', 
    'Rekomendasi', 'Status Verifikasi', 'Catatan Admin', 'Jurusan Pilihan'
];
foreach($headers as $h) {
    echo "<th style='background-color: #4e73df; color: white;'>$h</th>";
}
echo "</tr>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            // Trik: Tambahkan style mso-number-format biar NIK/NISN gak berubah jadi 1.23E+15
            $style = "";
            if (is_numeric($value) && strlen($value) > 10) {
                $style = "style='mso-number-format:\"\@\"'"; // Paksa format Text di Excel
            }
            echo "<td $style>$value</td>";
        }
        echo "</tr>";
    }
}
echo "</table>";

$conn->close();
exit;