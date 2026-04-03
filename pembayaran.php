<?php
require_once 'config.php';
require_once 'midtrans_config.php'; 

// Keamanan & Otorisasi
checkAuth();
if (!isSiswa()) {
    header("Location: dashboard.php");
    exit;
}

$conn = connectDB();
$currentUser = getCurrentUser();
$id_user = $currentUser['id_user'];
$student_data = getStudentFullData($conn, $id_user);

// --- INISIALISASI VARIABEL AWAL ---
$pageTitle = "Pembayaran Administrasi";
$message = ''; 
$status_verifikasi = $student_data['status_verifikasi'] ?? 'Menunggu Verifikasi';
$status_pembayaran = $student_data['status_pembayaran'] ?? 'Belum Bayar'; 
$id_pendaftar = $student_data['id_pendaftar'] ?? null;

// --- FIX: LOGIKA OTOMATIS LUNAS UNTUK LOCALHOST ---
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        // Update database secara instan saat user kembali ke halaman ini
        $sql_lunas = "UPDATE pendaftar SET status_pembayaran = 'Lunas' WHERE id_pendaftar = ?";
        $stmt_lunas = $conn->prepare($sql_lunas);
        $stmt_lunas->bind_param("i", $id_pendaftar);
        
        if ($stmt_lunas->execute()) {
            $status_pembayaran = 'Lunas'; // Update variabel biar tampilan langsung berubah
            $message = "<div class='alert alert-success shadow-sm'><i class='fas fa-check-circle me-2'></i>Pembayaran Berhasil! Status Anda telah diperbarui menjadi <strong>Lunas</strong>.</div>";
        }
    } elseif ($_GET['status'] == 'pending') {
        $message = "<div class='alert alert-warning shadow-sm'><i class='fas fa-clock me-2'></i>Pembayaran tertunda. Silakan selesaikan transaksi Anda segera.</div>";
    }
}

$conn->close();
include 'includes/header.php';
include 'includes/sidebar_siswa.php';
?>

<main class="content-area">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="d-flex align-items-center mb-4">
                    <i class="fas fa-file-invoice-dollar fa-2x text-primary me-3"></i>
                    <h3 class="mb-0 fw-bold">Metode Pembayaran Administrasi</h3>
                </div>
                
                <?php echo $message; ?>

                <?php if ($status_verifikasi != 'Diverifikasi'): ?>
                    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="card-body p-5 text-center">
                            <!-- <img src="smk_cahaya.png" alt="Menunggu" style="height: 200px;" class="mb-4"> -->
                            <h4 class="fw-bold text-dark">Pendaftaran Belum Diverifikasi</h4>
                            <p class="text-muted mx-auto" style="max-width: 550px;">
                                Mohon maaf, Anda belum dapat melakukan pembayaran. Silakan tunggu hingga berkas pendaftaran Anda diverifikasi oleh Admin.
                            </p>
                            <div class="badge bg-warning text-dark px-4 py-2 rounded-pill fs-6 shadow-sm">
                                Status: <?php echo htmlspecialchars($status_verifikasi); ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>

                    <div class="card shadow-sm mb-4 border-0" style="border-radius: 15px; background: #f8fafc;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-uppercase small fw-bold text-muted mb-1">Status Pembayaran</p>
                                <?php 
                                $badge_class = ($status_pembayaran == 'Lunas') ? 'bg-success' : (($status_pembayaran == 'Menunggu Verifikasi') ? 'bg-warning text-dark' : 'bg-danger');
                                ?>
                                <h4 class="fw-bold mb-0"><span class="badge <?php echo $badge_class; ?> rounded-pill"><?php echo htmlspecialchars($status_pembayaran); ?></span></h4>
                            </div>
                            <div class="text-end">
                                <p class="text-uppercase small fw-bold text-muted mb-1">Total Biaya</p>
                                <h4 class="fw-bold text-primary mb-0">Rp 150.000</h4>
                            </div>
                        </div>
                    </div>

                    <?php if ($status_pembayaran == 'Belum Bayar'): ?>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border-0" style="border-radius: 20px;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3 mx-auto bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 25px;">
                                        <i class="fas fa-university fa-2x"></i>
                                    </div>
                                    <h5 class="fw-bold">Pembayaran Tunai</h5>
                                    <p class="text-muted small mb-4">Bayar langsung melalui loket Tata Usaha (TU) SMK Cahaya secara tunai.</p>
                                    <div class="p-3 rounded-3 bg-light text-start mb-3 border small">
                                        <i class="fas fa-clock me-1 text-muted"></i> <strong>08.00 - 14.00 WIB</strong>
                                    </div>
                                    <p class="text-danger small fw-bold"><i class="fas fa-info-circle"></i> Harap bawa Bukti Pendaftaran.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border-0" style="border-radius: 20px;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3 mx-auto bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 25px;">
                                        <i class="fas fa-bolt fa-2x"></i>
                                    </div>
                                    <h5 class="fw-bold">Pembayaran Online</h5>
                                    <p class="text-muted small mb-4">Bayar instan via GoPay, ShopeePay, atau Transfer Bank secara otomatis.</p>
                                   <!-- <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Logo_midtrans.png/1200px-Logo_midtrans.png" alt="Midtrans" style="max-height: 30px; margin-bottom: 20px;" class="opacity-75"> -->
                                    <button class="btn btn-success btn-lg w-100 rounded-pill fw-bold py-3" id="pay-button">
                                        <i class="fas fa-credit-card me-2"></i> Bayar Sekarang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                        <div class="card border-0 shadow-sm text-center p-5" style="border-radius: 20px;">
                            <div class="display-1 text-success mb-3 opacity-25">
                                <i class="fas <?php echo ($status_pembayaran == 'Lunas') ? 'fa-check-circle' : 'fa-clock'; ?>"></i>
                            </div>
                            <h4 class="fw-bold"><?php echo ($status_pembayaran == 'Lunas') ? 'Pembayaran Selesai' : 'Sedang Diproses'; ?></h4>
                            <p class="text-muted">Status: <strong><?php echo $status_pembayaran; ?></strong>. Silakan kembali ke Dashboard.</p>
                            <a href="dashboard.php" class="btn btn-outline-primary rounded-pill px-4 mt-2">Kembali ke Dashboard</a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="Mid-client-jin-NQ3HF0IQCamS"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Proses...';

        $.ajax({
            url: 'ajax_snap_token.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.token) {
                    snap.pay(response.token, {
                      onSuccess: function(result){ window.location.href = "pembayaran.php?status=success"; },
                      onPending: function(result){ window.location.href = "pembayaran.php?status=pending"; },
                      onError: function(result){ alert("Transaksi gagal!"); btn.disabled = false; btn.innerHTML = 'Bayar Sekarang'; },
                      onClose: function(){ btn.disabled = false; btn.innerHTML = 'Bayar Sekarang'; }
                    });
                } else {
                    alert('Gagal ambil token: ' + response.message);
                    btn.disabled = false;
                }
            },
            error: function() { alert('Koneksi server bermasalah!'); btn.disabled = false; }
        });
    };
</script>
<?php include 'includes/footer.php'; ?>