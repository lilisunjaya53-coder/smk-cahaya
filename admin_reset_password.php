<?php
require_once 'config.php';

// Keamanan & Otorisasi: Hanya Admin yang Boleh Mengakses
checkAuth(); 
if (!isAdmin()) {
    header("Location: dashboard.php");
    exit;
}

$conn = connectDB();
$pageTitle = "Reset Password Siswa";
$message = '';

$user_id = (int)($_GET['user_id'] ?? 0);

// --- 1. Ambil Data User Target ---
if ($user_id === 0) {
    $message = "<div class='alert alert-danger'>ID Pengguna tidak valid.</div>";
    $target_user = false;
} else {
    $sql_user = "SELECT username, nama_lengkap FROM users WHERE id_user = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $target_user = $stmt_user->get_result()->fetch_assoc();
    $stmt_user->close();

    if (!$target_user) {
        $message = "<div class='alert alert-danger'>Akun pengguna tidak ditemukan.</div>";
    }
}

// --- 2. Logika Pemrosesan Form POST ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && $target_user) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_password) || $new_password !== $confirm_password || strlen($new_password) < 6) {
        $message = "<div class='alert alert-danger'>Password baru tidak cocok atau terlalu pendek (minimal 6 karakter).</div>";
    } else {
        // Hashing password baru
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $sql_update = "UPDATE users SET password = ? WHERE id_user = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $hashed_password, $user_id);

        if ($stmt_update->execute()) {
            $message = "<div class='alert alert-success'>Password untuk **" . htmlspecialchars($target_user['username']) . "** berhasil direset!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Gagal mengupdate database: " . $stmt_update->error . "</div>";
        }
        $stmt_update->close();
    }
}
$conn->close();
include 'includes/header.php';
include 'includes/sidebar_admin_kepsek.php';
?>

<main class="content-area full-width"> 
<div class="content-wrapper">

<h2 class="mb-4 text-primary">Reset Password Siswa</h2>

<?php echo $message; ?>

<?php if ($target_user): ?>
    <div class="card shadow-sm col-lg-6 mx-auto">
        <div class="card-header bg-info text-white">
            Reset Akun: <strong><?php echo htmlspecialchars($target_user['nama_lengkap']); ?></strong>
        </div>
        <div class="card-body">
            <p>Username: `<?php echo htmlspecialchars($target_user['username']); ?>`</p>
            <hr>
            <form action="admin_reset_password.php?user_id=<?php echo $user_id; ?>" method="post">
                
                <div class="mb-3">
                    <label for="new_password" class="form-label">Password Baru (Min. 6 Karakter)</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
                
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-danger">Reset Password Sekarang</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="mt-4 text-center">
    <a href="admin_users.php" class="btn btn-secondary">Kembali ke Manajemen Pengguna</a>
</div>

</div>
</main>
<?php include 'includes/footer.php'; ?>