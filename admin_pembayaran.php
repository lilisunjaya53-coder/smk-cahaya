<?php
require_once 'config.php';

checkAuth();
if (!isAdmin()) {
    header("Location: dashboard.php");
    exit;
}

$conn = connectDB();
$message = '';
$pageTitle = "Verifikasi Pembayaran";

// --- PROSES UPDATE STATUS OLEH ADMIN ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $id_pendaftar = (int)$_POST['id_pendaftar'];
    $action = $_POST['action'];

    if ($action == 'terima') {
        $sql = "UPDATE pendaftar_status SET status_pembayaran = 'Lunas' WHERE id_pendaftar = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_pendaftar);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success shadow-sm'><i class='fas fa-check-circle me-2'></i>Pembayaran berhasil diverifikasi menjadi <strong>Lunas</strong>.</div>";
        }
    } elseif ($action == 'tolak') {
        $sql = "UPDATE pendaftar_status SET status_pembayaran = 'Belum Bayar' WHERE id_pendaftar = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_pendaftar);
        $stmt->execute();
        $sql2 = "UPDATE pendaftar SET bukti_pembayaran = NULL WHERE id_pendaftar = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $id_pendaftar);
        $stmt2->execute();
        $message = "<div class='alert alert-warning shadow-sm'><i class='fas fa-times-circle me-2'></i>Pembayaran ditolak. Siswa harus mengulang proses pembayaran.</div>";
    }
}

$sql = "SELECT p.id_pendaftar, p.no_pendaftaran, p.nama_lengkap, ps.status_pembayaran
        FROM pendaftar p
        LEFT JOIN pendaftar_status ps ON p.id_pendaftar = ps.id_pendaftar
        ORDER BY FIELD(ps.status_pembayaran, 'Menunggu Verifikasi', 'Belum Bayar', 'Lunas'), p.nama_lengkap ASC";

$result = $conn->query($sql);
$pembayaran_list = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        error_log(print_r($row, true));
        $pembayaran_list[] = $row;
    }
}

$conn->close();
include 'includes/header.php';
include 'includes/sidebar_admin_kepsek.php';
?>

<style>
    /* Tambahan warna Indigo khusus untuk Midtrans bray */
    .bg-indigo-soft {
        background-color: rgba(99, 102, 241, 0.1);
        color: #6366f1;
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    .bg-amber-soft {
        background-color: rgba(245, 158, 11, 0.1);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }
</style>

<main class="content-area">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-15">
                <div class="content-wrapper px-4 py-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="fw-bold mb-1">Manajemen Pembayaran</h2>
                            <p class="text-muted small">Kelola verifikasi pembayaran online dan pelunasan tunai di sekolah.</p>
                        </div>

                    </div>

                    <?php echo $message; ?>

                    <div class="card shadow-sm border-0" style="border-radius: 15px;">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">No</th>
                                            <th>No. Pendaftaran</th>
                                            <th>Nama Lengkap</th>
                                            <th>Metode</th>
                                            <th>Status</th>
                                            <th class="text-center pe-4">Tindakan Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($pembayaran_list)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-5 text-muted">Belum ada data pendaftar.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php $no = 1;
                                            foreach ($pembayaran_list as $row): ?>
                                                <tr>
                                                    <td class="ps-4"><?php echo $no++; ?></td>
                                                    <td><span class="fw-bold text-primary"><?php echo htmlspecialchars($row['no_pendaftaran']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                                    <td>
                                                        <?php if ($row['status_pembayaran']): ?>
                                                            <span class="badge bg-amber-soft px-3 py-2 rounded-pill">
                                                                <i class="fas fa-money-bill-wave me-1"></i> Tunai (Sekolah)
                                                            </span>
                                                        <?php elseif ($row['status_pembayaran'] == 'Lunas'): ?>
                                                            <span class="badge bg-indigo-soft px-3 py-2 rounded-pill">
                                                                <i class="fas fa-bolt me-1"></i> Online (Midtrans)
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-amber-soft px-3 py-2 rounded-pill">
                                                                <i class="fas fa-university me-1"></i> Tunai (Sekolah)
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $s = $row['status_pembayaran'];
                                                        $cls = ($s == 'Lunas') ? 'bg-success' : (($s == 'Menunggu Verifikasi') ? 'bg-warning text-dark' : 'bg-danger');
                                                        ?>
                                                        <span class="badge <?php echo $cls; ?> rounded-pill px-3"><?php echo $s; ?></span>
                                                    </td>
                                                    <td class="text-center pe-4">
                                                        <?php if ($row['status_pembayaran'] != 'Lunas'): ?>
                                                            <form method="post" class="d-inline">
                                                                <input type="hidden" name="id_pendaftar" value="<?php echo $row['id_pendaftar']; ?>">
                                                                <button type="submit" name="action" value="terima" class="btn btn-sm btn-success px-3 rounded-pill fw-bold" onclick="return confirm('Konfirmasi pelunasan untuk pendaftar ini?');">
                                                                    <i class="fas fa-check me-1"></i> Lunasi
                                                                </button>
                                                                <?php if ($row['status_pembayaran']): ?>
                                                                    <button type="submit" name="action" value="tolak" class="btn btn-sm btn-danger px-3 rounded-pill fw-bold" onclick="return confirm('Tolak bukti pembayaran ini?');">
                                                                        <i class="fas fa-times me-1"></i> Tolak
                                                                    </button>
                                                                <?php endif; ?>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="text-success fw-bold small"><i class="fas fa-check-double"></i> Selesai</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include 'includes/footer.php'; ?>