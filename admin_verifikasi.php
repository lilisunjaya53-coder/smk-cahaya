<?php
// =========================================================================
// 1. Logika PHP Awal & Keamanan
// =========================================================================
require_once 'config.php';

checkAuth(); 
if (!isAdmin()) {
    header("Location: dashboard.php");
    exit;
}

$conn = connectDB();
$message = '';
$pageTitle = "Verifikasi Pendaftar";
$update_success = false;

// Logika Update Status - Ditambah validasi status baru bray
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $pendaftar_id = (int)($_POST['pendaftar_id'] ?? 0);
    $status_baru = $_POST['status_verifikasi'] ?? '';
    
    // Pastikan 'Diverifikasi Admin' masuk dalam list yang diizinkan bray
    $status_valid = ['Diverifikasi', 'Ditolak', 'Menunggu Verifikasi', 'Diverifikasi Admin'];
    
    if ($pendaftar_id > 0 && in_array($status_baru, $status_valid)) {
        $sql_update = "UPDATE pendaftar_status SET status_verifikasi = ? WHERE id_pendaftar = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $status_baru, $pendaftar_id);
        if ($stmt_update->execute()) { $update_success = true; }
        $stmt_update->close();
    }
}

// Ambil data dengan urutan: Menunggu -> Verif Admin -> Diterima -> Ditolak
$sql = "SELECT p.id_pendaftar, p.no_pendaftaran, p.nama_lengkap, ps.status_verifikasi, 
               j.singkatan AS pilihan_keahlian
        FROM pendaftar p
        LEFT JOIN pendaftar_status ps ON p.id_pendaftar = ps.id_pendaftar 
        LEFT JOIN jurusan j ON p.id_jurusan_pilihan = j.id_jurusan 
        ORDER BY FIELD(ps.status_verifikasi, 'Menunggu Verifikasi', 'Diverifikasi Admin', 'Diverifikasi', 'Ditolak'), 
                 p.tgl_daftar ASC"; 

$result = $conn->query($sql);
$data_pendaftar = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) { $data_pendaftar[] = $row; }
}
$conn->close(); 
?>

<?php include 'includes/header.php'; include 'includes/sidebar_admin_kepsek.php'; ?>

<style>
    .content-area { background-color: #f4f7fa; min-height: 100vh; padding: 40px 20px; }
    .table-main-container { background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .table-bordered { border: 2px solid #dee2e6 !important; }
    .table-bordered th, .table-bordered td { border: 1px solid #dee2e6 !important; padding: 12px 15px !important; }

    /* Judul Tabel Biru Tetap Sama bray */
    .table thead th {
        background-color: #4e73df !important;
        color: #ffffff !important;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
        text-align: center;
        vertical-align: middle;
    }

    .row-hover:hover { background-color: #f8f9fc; }
    .action-group { display: flex; align-items: center; gap: 8px; justify-content: center; }
</style>

<main class="content-area">
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="text-dark fw-bold mb-1">Verifikasi & Konfirmasi Pendaftar</h2>
            <p class="text-muted small">Kelola status kelulusan calon siswa baru SMK Cahaya.</p>
        </div>

        <?php echo $message; ?>

        <div class="table-main-container">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="50">No.</th>
                            <th width="160">No. Pendaftaran</th>
                            <th>Nama Lengkap Siswa</th>
                            <th width="140">Pilihan Jurusan</th>
                            <th width="180">Status Saat Ini</th>
                            <th width="320">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($data_pendaftar as $p): ?>
                        <tr class="row-hover">
                            <td class="text-center fw-bold text-muted"><?php echo $no++; ?></td>
                            <td class="text-center">
                                <span class="badge bg-light text-primary border px-2 py-2 w-100">
                                    <?php echo htmlspecialchars($p['no_pendaftaran']); ?>
                                </span>
                            </td>
                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($p['nama_lengkap']); ?></td>
                            <td class="text-center fw-bold text-secondary"><?php echo htmlspecialchars($p['pilihan_keahlian']); ?></td>
                            <td class="text-center">
                                <?php 
                                $status = $p['status_verifikasi'] ?? 'Menunggu Verifikasi';
                                $badge_class = 'bg-warning text-dark'; 
                                $display_text = $status;
                                
                                // Penambahan warna badge baru bray
                                if ($status == 'Diverifikasi Admin') { $badge_class = 'bg-info text-white'; }
                                elseif ($status == 'Diverifikasi') { $badge_class = 'bg-success'; $display_text = 'Diterima'; } 
                                elseif ($status == 'Ditolak') { $badge_class = 'bg-danger'; }
                                ?>
                                <span class="badge <?php echo $badge_class; ?> px-3 py-2 w-100 shadow-sm"><?php echo htmlspecialchars($display_text); ?></span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <button type="button" class="btn btn-sm btn-info text-white btn-show-detail" 
                                            data-id="<?php echo $p['id_pendaftar']; ?>">
                                        <i class="fas fa-search me-1"></i> Detail
                                    </button>
                                    
                                    <form method="POST" class="d-flex align-items-center gap-1 mb-0">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="pendaftar_id" value="<?php echo $p['id_pendaftar']; ?>">
                                        
                                        <select name="status_verifikasi" class="form-select form-select-sm border-primary" style="width: 155px;">
                                            <option value="Menunggu Verifikasi" <?php echo ($status == 'Menunggu Verifikasi') ? 'selected' : ''; ?>>Menunggu</option>
                                            <option value="Diverifikasi Admin" <?php echo ($status == 'Diverifikasi Admin') ? 'selected' : ''; ?>>Diverifikasi Admin</option> 
                                            <option value="Diverifikasi" <?php echo ($status == 'Diverifikasi') ? 'selected' : ''; ?>>Diterima</option> 
                                            <option value="Ditolak" <?php echo ($status == 'Ditolak') ? 'selected' : ''; ?>>Tolak</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-success px-3 shadow-sm">Update</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Detail Pendaftar Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDetailBody">
                <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    <?php if ($update_success): ?>
        Swal.fire({ title: 'Berhasil!', text: 'Status telah diperbarui.', icon: 'success', confirmButtonColor: '#4e73df' });
    <?php endif; ?>

    $('.btn-show-detail').on('click', function() {
        var pId = $(this).data('id');
        $('#detailModal').modal('show');
        $.ajax({ url: 'ajax_get_detail.php', type: 'GET', data: { id: pId }, success: function(res) { $('#modalDetailBody').html(res); } });
    });
});
</script>

<?php include 'includes/footer.php'; ?>