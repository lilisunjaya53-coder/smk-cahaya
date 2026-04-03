<?php
require_once 'config.php';

// 1. Pengecekan Autentikasi
checkAuth(); 
$currentUser = getCurrentUser(); 

if (!$currentUser) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Logika Logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}
$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$roleName = isAdmin() ? "Admin" : (isKepsek() ? "Kepala Sekolah" : "Siswa");
$pageTitle = "Dashboard";
include 'includes/header.php';
?>

<?php 

if (isAdmin() || isKepsek()): 
    $conn_stats = connectDB();
    
    $sql_accepted_admin = "SELECT COUNT(id_pendaftar) AS total FROM pendaftar_status WHERE status_verifikasi = 'Diverifikasi'";
    $total_accepted_admin = $conn_stats->query($sql_accepted_admin)->fetch_assoc()['total'];

    $sql_pending_admin = "SELECT COUNT(id_pendaftar) AS total FROM pendaftar_status WHERE status_verifikasi = 'Menunggu Verifikasi'";
    $total_pending_admin = $conn_stats->query($sql_pending_admin)->fetch_assoc()['total'];

    // QUERY TAMBAHAN: MENGHITUNG SISWA DITOLAK
    $sql_rejected_admin = "SELECT COUNT(id_pendaftar) AS total FROM pendaftar_status WHERE status_verifikasi = 'Ditolak'";
    $total_rejected_admin = $conn_stats->query($sql_rejected_admin)->fetch_assoc()['total'];

    $sql_total_all = "SELECT COUNT(id_pendaftar) AS total FROM pendaftar";
    $total_all_students = $conn_stats->query($sql_total_all)->fetch_assoc()['total'];
    $conn_stats->close();

    $conn_chart = connectDB();
    $jurusan_labels = [];
    $jurusan_data = [];

    $sql_chart = "SELECT 
                    j.singkatan AS jurusan, 
                    COUNT(p.id_pendaftar) AS total_jurusan
                  FROM pendaftar p
                  JOIN pendaftar_status ps ON p.id_pendaftar = ps.id_pendaftar
                  JOIN jurusan j ON p.id_jurusan_pilihan = j.id_jurusan
                  WHERE ps.status_verifikasi = 'Diverifikasi'
                  GROUP BY j.singkatan
                  ORDER BY total_jurusan DESC";

    $result_chart = $conn_chart->query($sql_chart);

    if ($result_chart && $result_chart->num_rows > 0) {
        while ($row = $result_chart->fetch_assoc()) {
            $jurusan_labels[] = $row['jurusan'];
            $jurusan_data[] = (int)$row['total_jurusan'];
        }
    }
    $conn_chart->close();
    
    $json_labels = json_encode($jurusan_labels);
    $json_data = json_encode($jurusan_data);

    include 'includes/sidebar_admin_kepsek.php';
?>
<style>
    :root {
        --sidebar-width: 260px;
        --grad-blue: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        --grad-green: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        --grad-orange: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        --grad-red: linear-gradient(135deg, #ef4444 0%, #f87171 100%); /* WARNA BARU */
    }

    .content-area.admin-panel { 
        margin-left: var(--sidebar-width) !important;
        padding: 40px !important; 
        background-color: #f8fafc;
        min-height: calc(100vh - 70px);
        width: calc(100% - var(--sidebar-width));
        box-sizing: border-box;
    }

    .admin-greeting { margin-bottom: 40px; }
    .admin-greeting h1 { font-weight: 800; font-size: 2.2rem; color: #1e293b; letter-spacing: -1px; }

    .premium-card {
        background: #ffffff; border: none; border-radius: 30px; padding: 35px;
        position: relative; overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .premium-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1); }
    .card-icon-wrapper { width: 65px; height: 65px; border-radius: 22px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 25px; color: white; }
    .bg-indigo { background: var(--grad-blue); box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.5); }
    .bg-emerald { background: var(--grad-green); box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.5); }
    .bg-amber { background: var(--grad-orange); box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.5); }
    .bg-rose { background: var(--grad-red); box-shadow: 0 10px 20px -5px rgba(239, 68, 68, 0.5); } /* STYLE BARU */
    .premium-card h6 { font-weight: 700; color: #94a3b8; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px; margin-bottom: 10px; }
    .premium-card h2 { font-weight: 900; font-size: 3rem; color: #0f172a; margin: 0; letter-spacing: -2px; }
    .card-decoration { position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(0,0,0,0.02); border-radius: 50%; }
    
    .login-role-text { font-size: 2.2rem; font-weight: 800; color: #1e293b; letter-spacing: -1.5px; line-height: 1.2; margin-bottom: 8px; }
    .login-role-badge { background: var(--grad-blue); -webkit-background-clip: text; -webkit-text-fill-color: transparent; position: relative; display: inline-block; animation: floatWelcome 3s ease-in-out infinite; }
    @keyframes floatWelcome { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
    .login-role-badge::after { content: '.'; -webkit-text-fill-color: #6366f1; }

    /* MEDIA QUERY RESPONSIVE FIX */
    @media (max-width: 991px) {
        .content-area.admin-panel, .content-area { margin-left: 0 !important; width: 100% !important; padding: 20px !important; }
        .login-role-text { font-size: 1.8rem; }
        .premium-card h2 { font-size: 2.5rem; }
        .admin-greeting { text-align: center; }
        .text-lg-end { text-align: center !important; margin-top: 15px; }
    }
</style>

<main class="content-area admin-panel">
    <div class="container-fluid p-0">
        <div class="admin-greeting mb-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <p class="login-role-text">Anda login sebagai <span class="login-role-badge"><?= $roleName ?></span></p>
                    <p class="lead text-muted mb-0 fw-medium">Berikut adalah ringkasan data strategis sistem <span class="fw-bold text-dark">SPMB</span> hari ini.</p>
                </div>
                <div class="col-lg-4 text-lg-end d-none d-lg-block">
                    <div class="d-inline-flex align-items-center p-3 bg-white shadow-sm rounded-4 border">
                        <div class="me-3 text-start">
                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px;">Current Date</small>
                            <span class="fw-bold text-dark"><?php echo date('l, d M Y'); ?></span>
                        </div>
                        <div class="p-2 bg-primary bg-opacity-10 rounded-3 text-primary"><i class="fas fa-calendar-check fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-3"> <div class="premium-card">
                    <div class="card-decoration"></div>
                    <div class="card-icon-wrapper bg-emerald"><i class="fas fa-user-check"></i></div>
                    <h6>Verified Students</h6>
                    <h2><?php echo $total_accepted_admin; ?></h2>
                    <div class="mt-3 small fw-bold text-success"><i class="fas fa-chart-line me-1"></i> Data sudah valid</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="premium-card">
                    <div class="card-decoration"></div>
                    <div class="card-icon-wrapper bg-amber"><i class="fas fa-clock-rotate-left"></i></div>
                    <h6>Pending Approval</h6>
                    <h2><?php echo $total_pending_admin; ?></h2>
                    <div class="mt-3 small fw-bold text-warning"><i class="fas fa-exclamation-triangle me-1"></i> Perlu verifikasi</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="premium-card">
                    <div class="card-decoration"></div>
                    <div class="card-icon-wrapper bg-rose"><i class="fas fa-user-xmark"></i></div>
                    <h6>Rejected / Revision</h6>
                    <h2><?php echo $total_rejected_admin; ?></h2>
                    <div class="mt-3 small fw-bold text-danger"><i class="fas fa-circle-exclamation me-1"></i> Tidak lolos/revisi</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="premium-card">
                    <div class="card-decoration"></div>
                    <div class="card-icon-wrapper bg-indigo"><i class="fas fa-users-viewfinder"></i></div>
                    <h6>Total Applicants</h6>
                    <h2><?php echo $total_all_students; ?></h2>
                    <div class="mt-3 small fw-bold text-primary"><i class="fas fa-database me-1"></i> Terdaftar di database</div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <div class="p-4 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3" style="background: #ffffff; border-radius: 25px; border: 1px dashed #cbd5e1;">
                    <div class="d-flex align-items-center">
                        <div class="me-3 p-3 bg-light rounded-circle text-primary d-none d-sm-block"><i class="fas fa-lightbulb"></i></div>
                        <div>
                            <span class="d-block fw-bold" style="color: #1e293b;">Tips Pengelolaan</span>
                            <small class="text-muted">Gunakan menu "Verifikasi Pendaftar" untuk memvalidasi berkas pendaftar baru.</small>
                        </div>
                    </div>
                    <a href="admin_verifikasi.php" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Buka Verifikasi</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php elseif (isSiswa()): 
    $pendaftar_id = $currentUser['id_pendaftar'];
    $is_filled = !is_null($pendaftar_id);
    $status_verifikasi = "Belum Mengisi Formulir";
    $no_pendaftaran = "N/A";

    if ($is_filled) {
        $conn_status = connectDB();
        $sql_status = "SELECT p.no_pendaftaran, ps.status_verifikasi FROM pendaftar p JOIN pendaftar_status ps ON p.id_pendaftar = ps.id_pendaftar WHERE p.id_pendaftar = ?";
        $stmt_status = $conn_status->prepare($sql_status);
        $stmt_status->bind_param("i", $pendaftar_id);
        $stmt_status->execute();
        $result_status = $stmt_status->get_result();
        if ($result_status->num_rows > 0) {
            $data_status = $result_status->fetch_assoc();
            $status_verifikasi = htmlspecialchars($data_status['status_verifikasi']);
            $no_pendaftaran = htmlspecialchars($data_status['no_pendaftaran']);
        }
        $stmt_status->close(); $conn_status->close();
    }
    include 'includes/sidebar_siswa.php';
?>

<style>
    :root { --primary-black: #0f172a; --accent-blue: #3b82f6; --accent-purple: #8b5cf6; --sidebar-width: 260px; }
    body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--primary-black); }
    .content-area { margin-left: var(--sidebar-width) !important; padding: 20px 30px !important; min-height: calc(100vh - 70px); box-sizing: border-box; }
    .welcome-main { font-size: 2.8rem; font-weight: 800; letter-spacing: -1.5px; line-height: 1.1; margin-bottom: 12px; background: linear-gradient(to right, #000 20%, var(--accent-blue) 40%, var(--accent-purple) 60%, #000 80%); background-size: 200% auto; -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: shine 6s linear infinite; }
    @keyframes shine { 0% { background-position: -200% center; } 100% { background-position: 200% center; } }
    .btn-pill { background: var(--primary-black); color: white; border-radius: 100px; padding: 14px 30px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; font-size: 0.8rem; }
    .step-num { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; transition: all 0.3s ease; }

    @media (max-width: 991px) {
        .content-area { margin-left: 0 !important; width: 100% !important; padding: 20px !important; }
        .welcome-main { font-size: 2rem; }
    }
</style>

<main class="content-area">
    <div class="container-fluid">
        <div class="welcome-container mt-4">
            <h1 class="welcome-main">Selamat Datang di <br> SMK Cahaya.</h1>
            <p class="text-muted">Pantau proses pendaftaranmu secara real-time dan pastikan berkas lengkap.</p>
        </div>

        <div class="row mt-4 g-3">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm p-3" style="background: #eff6ff; border-radius: 15px;">
                    <div class="d-flex align-items-center">
                        <div class="icon-box me-3 bg-white p-2 rounded-circle shadow-sm"><i class="fas fa-calendar-alt text-primary"></i></div>
                        <div><small class="text-muted d-block">Tahun Ajaran</small><span class="fw-bold">2026 / 2027</span></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm p-3" style="background: #f0fdf4; border-radius: 15px;">
                    <div class="d-flex align-items-center">
                        <div class="icon-box me-3 bg-white p-2 rounded-circle shadow-sm"><i class="fas fa-bullhorn text-success"></i></div>
                        <div><small class="text-muted d-block">Gelombang</small><span class="fw-bold">Gelombang 1 (Aktif)</span></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm p-3" style="background: #fff7ed; border-radius: 15px;">
                    <div class="d-flex align-items-center">
                        <div class="icon-box me-3 bg-white p-2 rounded-circle shadow-sm"><i class="fas fa-id-card text-warning"></i></div>
                        <div><small class="text-muted d-block">ID Pendaftaran</small><span class="fw-bold">#<?php echo str_replace('PPDB', 'SPMB', $no_pendaftaran ?? 'N/A'); ?></span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="status-section mt-5 pt-4 border-top">
            <div class="row g-4">
                <div class="col-12 col-lg-7">
                    <div class="p-4 bg-white shadow-sm border" style="border-radius: 20px;">
                        <h5 class="text-muted mb-3 fs-6 fw-bold text-uppercase" style="letter-spacing: 1px;">Status Saat Ini</h5>
                        <?php 
                        $display_status = $status_verifikasi;
                        $status_color = "#64748b";
                        if ($status_verifikasi == 'Diverifikasi') { $display_status = 'DITERIMA'; $status_color = "#3b82f6"; }
                        elseif ($status_verifikasi == 'Ditolak') { $display_status = 'DITOLAK'; $status_color = "#ef4444"; }
                        elseif ($status_verifikasi == 'Menunggu Verifikasi' || !$is_filled) { $display_status = 'MENUNGGU'; $status_color = "#f59e0b"; }
                        ?>
                        <h2 class="fw-bold mb-3" style="font-size: 2.2rem; color: <?php echo $status_color; ?>;"><?php echo $display_status; ?></h2>
                        <p class="text-muted mb-4">
                            <?php 
                            if ($status_verifikasi == 'Diverifikasi') echo "Selamat! Seluruh proses pendaftaran Anda telah selesai dan Anda dinyatakan <strong>Diterima</strong> di SMK Cahaya.";
                            elseif ($status_verifikasi == 'Ditolak') echo "Mohon maaf, pendaftaran Anda belum dapat kami setujui saat ini. Silakan hubungi admin <strong>SPMB</strong> untuk informasi lebih lanjut.";
                            elseif (!$is_filled) echo "Segera lengkapi formulir pendaftaran <strong>SPMB</strong> Anda untuk melanjutkan ke tahap verifikasi.";
                            else echo "Data pendaftaranmu sedang diperiksa oleh panitia. Mohon cek berkala halaman ini untuk hasil seleksi.";
                            ?>
                        </p>
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <?php if (!$is_filled): ?>
                                <a href="form_ppdb.php" class="btn btn-pill btn-lg w-100 shadow-sm justify-content-center">Isi Formulir SPMB Sekarang</a>
                            <?php else: ?>
                                <a href="riwayat_pendaftaran.php" class="btn btn-pill w-100 justify-content-center">Detail Berkas</a>
                                <?php if ($status_verifikasi == 'Diverifikasi'): ?>
                                <a href="cetak_bukti.php" class="btn btn-outline-primary rounded-pill px-4 justify-content-center"><i class="fas fa-print"></i></a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="p-4 bg-white shadow-sm border" style="border-radius: 20px;">
                        <h5 class="fw-bold mb-4">Alur Pendaftaran</h5>
                        <div class="timeline-simple">
                            <div class="d-flex mb-3 align-items-center">
                                <div class="step-num me-3 <?php echo $is_filled ? 'bg-success text-white' : 'bg-primary text-white'; ?>"><?php echo $is_filled ? '<i class="fas fa-check"></i>' : '1'; ?></div>
                                <div><h6 class="mb-0 fw-bold">Pengisian Formulir</h6><small class="<?php echo $is_filled ? 'text-success' : 'text-muted'; ?>">Lengkapi data profil diri.</small></div>
                            </div>
                            <div class="d-flex mb-3 align-items-center">
                                <div class="step-num me-3 <?php echo ($status_verifikasi == 'Diverifikasi') ? 'bg-success text-white' : (($status_verifikasi == 'Ditolak') ? 'bg-danger text-white' : 'bg-light text-muted'); ?>"><?php if ($status_verifikasi == 'Diverifikasi') echo '<i class="fas fa-check"></i>'; elseif ($status_verifikasi == 'Ditolak') echo '<i class="fas fa-times"></i>'; else echo '2'; ?></div>
                                <div><h6 class="mb-0 fw-bold">Verifikasi Panitia</h6><small class="<?php if ($status_verifikasi == 'Diverifikasi') echo 'text-success'; elseif ($status_verifikasi == 'Ditolak') echo 'text-danger'; else echo 'text-muted'; ?>">Validasi berkas admin SPMB.</small></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="step-num me-3 <?php echo ($status_verifikasi == 'Diverifikasi') ? 'bg-success text-white' : 'bg-light text-muted'; ?>"><?php echo ($status_verifikasi == 'Diverifikasi') ? '<i class="fas fa-check"></i>' : '3'; ?></div>
                                <div><h6 class="mb-0 fw-bold">Selesai</h6><small class="<?php echo ($status_verifikasi == 'Diverifikasi') ? 'text-success' : 'text-muted'; ?>">Cetak bukti pendaftaran.</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('login') === 'success') {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
            Toast.fire({ icon: 'success', title: 'Berhasil masuk!', text: 'Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['username']); ?>', background: '#f0fdf4', color: '#166534', iconColor: '#22c55e' });
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>

<?php include 'includes/footer.php'; ?>