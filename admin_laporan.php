<?php
require_once 'config.php';

// Keamanan & Otorisasi
checkAuth(); 
if (!isAdmin() && !isKepsek()) {
    header("Location: dashboard.php");
    exit;
}

$conn = connectDB();
$pageTitle = "Laporan Statistik PPDB";

// =========================================================
// Logika 1: Statistik Pendaftar per Jurusan
// =========================================================
$sql_statistik = "SELECT j.singkatan AS pilihan_keahlian, 
                         COUNT(p.id_pendaftar) AS total_pendaftar
                  FROM pendaftar p
                  LEFT JOIN jurusan j ON p.id_jurusan_pilihan = j.id_jurusan
                  GROUP BY j.singkatan
                  ORDER BY total_pendaftar DESC";

$result_statistik = $conn->query($sql_statistik);
$statistik_keahlian = [];
$total_keseluruhan = 0;

if ($result_statistik) {
    while ($row = $result_statistik->fetch_assoc()) {
        $statistik_keahlian[] = $row;
        $total_keseluruhan += $row['total_pendaftar'];
    }
}

// =========================================================
// Logika 2: Statistik Berdasarkan Status Verifikasi
// =========================================================
$sql_status = "SELECT status_verifikasi, 
                      COUNT(id_pendaftar) AS total_status
               FROM pendaftar_status
               GROUP BY status_verifikasi";
               
$result_status = $conn->query($sql_status);
$statistik_status = [];
if ($result_status) {
    while ($row = $result_status->fetch_assoc()) {
        $statistik_status[$row['status_verifikasi']] = $row['total_status'];
    }
}

$conn->close();
include 'includes/header.php';
include 'includes/sidebar_admin_kepsek.php'; 
?>

<main class="content-area">
<div class="container mt-4">
<div class="row justify-content-center">
<div class="col-lg-12"> <h2 class="mb-4 text-primary">Laporan & Statistik Pendaftaran SPMB</h2>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card shadow border-left-info py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Pendaftar</div>
                <div class="h3 mb-0 font-weight-bold text-gray-800"><?php echo $total_keseluruhan; ?> Siswa</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card shadow border-left-warning py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Verifikasi</div>
                <div class="h3 mb-0 font-weight-bold text-gray-800"><?php echo $statistik_status['Menunggu Verifikasi'] ?? 0; ?> Siswa</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card shadow border-left-success py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Diterima</div>
                <div class="h3 mb-0 font-weight-bold text-gray-800"><?php echo $statistik_status['Diverifikasi'] ?? 0; ?> Siswa</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card shadow border-left-danger py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ditolak / Revisi</div>
                <div class="h3 mb-0 font-weight-bold text-gray-800"><?php echo $statistik_status['Ditolak'] ?? 0; ?> Siswa</div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-7 mb-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Pendaftar Berdasarkan Jurusan</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Kompetensi Keahlian</th>
                            <th>Jumlah Pendaftar</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_keseluruhan > 0): ?>
                            <?php foreach ($statistik_keahlian as $data): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['pilihan_keahlian']); ?></td>
                                <td><?php echo $data['total_pendaftar']; ?></td>
                                <td>
                                    <?php 
                                        $persen = ($data['total_pendaftar'] / $total_keseluruhan) * 100;
                                        echo round($persen, 1) . '%';
                                    ?>
                                    <div class="progress mt-1">
                                      <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo round($persen, 0); ?>%" aria-valuenow="<?php echo round($persen, 0); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center">Belum ada data pendaftar.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-5 mb-4">
        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Ekspor Laporan (CSV)</h5>
            </div>
            <div class="card-body">
                <p>Pilih kategori data yang ingin Anda unduh:</p>
                
                <form action="admin_export.php" method="GET">
                    <input type="hidden" name="format" value="csv">
                    
                    <div class="mb-3">
                        <label class="form-label small font-weight-bold">Filter Berdasarkan Status:</label>
                        <select name="status" class="form-control" required>
                            <option value="all">Semua Pendaftar (Keseluruhan)</option>
                            <option value="Diverifikasi">Siswa Diterima / Sudah Verifikasi</option>
                            <option value="Menunggu Verifikasi">Siswa Belum Diverifikasi</option>
                            <option value="Ditolak">Siswa Ditolak / Revisi</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success btn-block w-100 shadow-sm">
                        <i class="fas fa-download me-2"></i> Unduh Data Pendaftar
                    </button>
                </form>
                
                <hr>
                <p class="text-muted small mt-2">
                    <i class="fas fa-info-circle"></i> File CSV akan berisi biodata lengkap termasuk data orang tua, sekolah asal, dan pilihan jurusan.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="mb-5">
    <button type="button" class="btn btn-secondary shadow-sm" onclick="window.location.href='dashboard.php';">
        &laquo; Kembali ke Dashboard
    </button>
</div>

</div>
</div>
</div>
</main>

<?php include 'includes/footer.php'; ?>