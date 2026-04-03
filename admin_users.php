<?php
require_once 'config.php';

// Keamanan & Otorisasi: Hanya Admin yang Boleh Mengakses
checkAuth(); 
if (!isAdmin()) {
    header("Location: dashboard.php");
    exit;
}

$conn = connectDB();
$pageTitle = "Manajemen Akun Siswa";
$message = '';

// Ambil ID Role Siswa (Diasumsikan ID 2 dari setup awal)
$sql_role_id = "SELECT id_role FROM roles WHERE role_name = 'Siswa'";
$role_siswa_result = $conn->query($sql_role_id)->fetch_assoc();
$role_siswa_id = $role_siswa_result['id_role'] ?? 0;

// Logika Pengambilan Data Akun Siswa
// JOIN users, user_roles, dan pendaftar
$sql = "SELECT 
            u.id_user, u.username, u.nama_lengkap, u.id_pendaftar,
            p.no_pendaftaran, ps.status_verifikasi
        FROM users u
        JOIN user_roles ur ON u.id_user = ur.user_id
        LEFT JOIN pendaftar p ON u.id_pendaftar = p.id_pendaftar
        LEFT JOIN pendaftar_status ps ON u.id_pendaftar = ps.id_pendaftar
        WHERE ur.role_id = ?
        ORDER BY u.username ASC"; 

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $role_siswa_id);
$stmt->execute();
$result = $stmt->get_result();
$data_users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

include 'includes/header.php';
include 'includes/sidebar_admin_kepsek.php';
// Karena ini laman Admin, kita bisa menggunakan layout full-width tanpa sidebar fixed Siswa
?>

<main class="content-area full-width"> 
<div class="content-wrapper">

<h2 class="mb-4 text-primary">Manajemen Akun Siswa Pendaftar</h2>

<?php echo $message; ?>

<?php if (empty($data_users)): ?>
    <div class="alert alert-warning text-center">
        <h4 class="alert-heading">Data Akun Kosong!</h4>
        Belum ada akun siswa yang terdaftar.
    </div>
<?php else: ?>
    <div class="table-responsive bg-white p-3 rounded shadow-sm">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>No.</th>
                    <th>Username (Login)</th>
                    <th>Nama Lengkap</th>
                    <th>Status Formulir</th>
                    <th>No. Pendaftaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($data_users as $user): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                    <td>
                        <?php 
                        $status = $user['id_pendaftar'] ? ($user['status_verifikasi'] ?? 'Formulir Terkirim') : 'Belum Mengisi Form';
                        $badge_class = 'bg-secondary';
                        if ($user['id_pendaftar']) {
                            if (strpos($status, 'Diverifikasi') !== false || strpos($status, 'Diterima') !== false) $badge_class = 'bg-success';
                            else if (strpos($status, 'Ditolak') !== false) $badge_class = 'bg-danger';
                            else if (strpos($status, 'Menunggu') !== false) $badge_class = 'bg-warning';
                            else $badge_class = 'bg-info';
                        }
                        ?>
                        <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($status); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($user['no_pendaftaran'] ?? 'N/A'); ?></td>
                    <td>
<a href="admin_reset_password.php?user_id=<?php echo $user['id_user']; ?>" 
       class="btn btn-sm btn-info text-white">
        Reset Password
    </a>                        <?php if ($user['id_pendaftar']): ?>
                        <a href="admin_verifikasi.php?view_id=<?php echo $user['id_pendaftar']; ?>" class="btn btn-sm btn-primary">
                            Lihat Formulir
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

</div>
</main>
<?php include 'includes/footer.php'; ?>