<?php
require_once 'config.php';

// 1. Keamanan & Otorisasi
checkAuth(); 
if (!isSiswa()) {
    header("Location: dashboard.php");
    exit;
}

$conn = connectDB();
$id_user = $_SESSION['user_id'];

// 2. Ambil semua data siswa
$student_data = getStudentFullData($conn, $id_user);
$conn->close();

if (!$student_data) {
    // Siswa belum mengisi form atau data tidak ditemukan
    header("Location: dashboard.php");
    exit;
}

// 3. Batasan Cetak (Hanya jika status DIVERIFIKASI)
if ($student_data['status_verifikasi'] !== 'Diverifikasi') {
    // Alihkan atau tampilkan pesan error
    header("Location: dashboard.php?error=not_verified");
    exit;
}

$pageTitle = "Cetak Bukti PPDB";
// Kita tidak menggunakan header/footer biasa karena kita butuh kontrol penuh atas CSS cetak
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin: 0; padding: 20px; font-family: sans-serif; background-color: #f8f9fa; }
        .bukti-container { 
            width: 800px; 
            margin: 0 auto; 
            padding: 30px; 
            border: 1px solid #dee2e6; 
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header-bukti { border-bottom: 3px double #333; padding-bottom: 10px; margin-bottom: 20px; }
        .data-label { font-weight: bold; width: 40%; }
        .data-value { width: 60%; }

        @media print {
            body { background: none; }
            .bukti-container { border: none; box-shadow: none; padding: 0; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

<div class="container bukti-container">
    
    <div class="text-center header-bukti">
        <h3 class="mb-1 text-primary">BUKTI PENERIMAAN PESERTA DIDIK BARU</h3>
        <h5 class="mb-0">Tahun Pelajaran 2024-2025</h5>
    </div>

    <div class="alert alert-success text-center">
        <h4 class="mb-1">SELAMAT! DATA ANDA TELAH DIVERIFIKASI DAN DITERIMA.</h4>
        <p class="mb-0">Nomor Pendaftaran Resmi Anda: <strong><?php echo htmlspecialchars($student_data['no_pendaftaran']); ?></strong></p>
    </div>

    <h6 class="mt-4 mb-3 text-secondary">A. Data Calon Peserta Didik</h6>
    <table class="table table-sm table-borderless">
        <tr><td class="data-label">Nama Lengkap</td><td class="data-value">: <?php echo htmlspecialchars($student_data['nama_lengkap']); ?></td></tr>
        <tr><td class="data-label">NISN</td><td>: <?php echo htmlspecialchars($student_data['nisn']); ?></td></tr>
        <tr><td class="data-label">Tanggal Lahir</td><td>: <?php echo date('d M Y', strtotime($student_data['tgl_lahir'])); ?></td></tr>
        <tr><td class="data-label">Jenis Kelamin</td><td>: <?php echo htmlspecialchars($student_data['jenis_kelamin']); ?></td></tr>
        <tr><td class="data-label">Asal Sekolah</td><td>: <?php echo htmlspecialchars($student_data['asal_sekolah']); ?></td></tr>
        <tr><td class="data-label">Alamat Siswa</td><td>: <?php echo nl2br(htmlspecialchars($student_data['alamat_siswa'])); ?></td></tr>
    </table>

    <h6 class="mt-4 mb-3 text-secondary">C. Pilihan Kompetensi Keahlian</h6>
    <div class="p-3 border rounded text-center bg-light">
        <p class="lead mb-0">Jurusan yang Diterima:</p>
        <h4 class="text-danger fw-bold"><?php echo htmlspecialchars($student_data['nama_keahlian']); ?></h4>
    </div>

    <div class="row mt-5">
        <div class="col-6">
            <p class="mb-1">Tangerang, <?php echo date('d M Y'); ?></p>
            <p class="mb-1">Tanda Tangan Siswa</p>
            <br><br><br>
            <p>( <?php echo htmlspecialchars($student_data['nama_lengkap']); ?> )</p>
        </div>
        <div class="col-6 text-end">
            <p class="mb-1">Panitia PPDB,</p>
            <p class="mb-1">Kepala Sekolah</p>
            <br><br><br>
            <p>( Nama Kepala Sekolah )</p>
        </div>
    </div>

    <div class="text-center mt-4">
        <button class="btn btn-primary btn-lg btn-print" onclick="window.print()">Cetak Bukti Penerimaan</button>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-print">Kembali ke Dashboard</a>
    </div>
</div>

<script>
    // Memastikan dialog cetak muncul otomatis saat halaman dimuat
    // window.onload = function() {
    //     window.print();
    // }
</script>
</body>
</html>