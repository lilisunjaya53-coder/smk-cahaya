<?php
require_once 'config.php';
$error = [];
$success_trigger = false; // Variabel pemicu pop-up sukses
$conn = connectDB();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $role_name = 'siswa'; 
    $id_role = getRoleIdByName($conn, $role_name); 

    try {
        if (empty($username) || empty($password) || empty($nama_lengkap) || !$id_role) {
            throw new Exception("Semua bidang wajib diisi.");
        }
        if (strlen($password) < 6) {
            throw new Exception("Password minimal 6 karakter.");
        }

        $stmt_check = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->bind_result($user_count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($user_count > 0) {
            throw new Exception("Username '{$username}' sudah digunakan.");
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $conn->begin_transaction();

        $sql_user = "INSERT INTO users (username, password, nama_lengkap, id_pendaftar) VALUES (?, ?, ?, NULL)";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("sss", $username, $hashed_password, $nama_lengkap);

        if (!$stmt_user->execute()) {
            throw new Exception("Gagal membuat akun.");
        }
        $user_id = $conn->insert_id;
        $stmt_user->close();

        $sql_role = "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)";
        $stmt_role = $conn->prepare($sql_role);
        $stmt_role->bind_param("ii", $user_id, $id_role);

        if (!$stmt_role->execute()) {
            throw new Exception("Gagal menetapkan peran.");
        }
        $stmt_role->close();
        
        $conn->commit();
        
        // JANGAN REDIRECT DI SINI, TAPI SET VARIABEL SUKSES
        $success_trigger = true;

    } catch (Exception $e) {
        $conn->rollback();
        $error[] = $e->getMessage();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun - PPDB SMK Cahaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --reg-primary: #6366f1;
            --reg-dark: #4338ca;
            --reg-bg: #f5f3ff;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--reg-bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.15) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .register-container { width: 100%; max-width: 450px; }

        .register-card {
            background: #ffffff;
            border: none;
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(99, 102, 241, 0.25);
            padding: 45px;
            position: relative;
            overflow: hidden;
        }

        .header-section h4 { font-weight: 800; color: #1e1b4b; margin-bottom: 10px; }
        .header-section p { color: #6b7280; font-size: 0.95rem; }

        .form-label { font-weight: 700; color: #4338ca; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 8px; }

        .form-control {
            border-radius: 14px;
            padding: 12px 18px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            border-color: var(--reg-primary);
        }

        .btn-register {
            background: linear-gradient(135deg, var(--reg-primary) 0%, var(--reg-dark) 100%);
            color: white;
            border: none;
            border-radius: 14px;
            padding: 15px;
            font-weight: 700;
            width: 100%;
            margin-top: 20px;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
            transition: all 0.3s ease;
        }

        .footer-link { text-align: center; margin-top: 30px; font-size: 0.9rem; color: #6b7280; }
        .footer-link a { color: var(--reg-primary); text-decoration: none; font-weight: 800; }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-card">
        <div class="header-section text-center mb-5">
            <div class="mb-3">
                <span class="p-3 d-inline-block shadow-sm" style="background: #f5f3ff; border-radius: 20px;">
                    <i class="fas fa-user-plus fa-2x text-primary"></i>
                </span>
            </div>
            <h4>Buat Akun Baru</h4>
            <p>Untuk bergabung di SMK Cahaya</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-4 d-flex align-items-center" style="border-radius: 16px; border:none; background:#fef2f2; color:#991b1b; font-size:0.85rem; padding:15px;">
                <i class="fas fa-circle-exclamation me-2"></i>
                <div><?php foreach ($error as $err): echo htmlspecialchars($err); endforeach; ?></div>
            </div>
        <?php endif; ?>

        <form action="register_akun.php" method="post">
            <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap Siswa</label>
                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" placeholder="Masukan nama sesuai ijazah" required value="<?php echo htmlspecialchars($_POST['nama_lengkap'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Gunakan username untuk login" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password Keamanan</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Minimal 6 karakter" required>
            </div>

            <button type="submit" class="btn-register">
                Buat Akun Sekarang <i class="fas fa-chevron-right ms-2 small"></i>
            </button>
        </form>

        <div class="footer-link">
            Sudah terdaftar? <a href="login.php">Masuk Akun</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    <?php if ($success_trigger): ?>
    Swal.fire({
        title: 'Berhasil Terdaftar!',
        text: 'Akun Anda telah dibuat.',
        icon: 'success',
        width: '320px', 
        padding: '1.25rem', 
        confirmButtonColor: '#6366f1',
        confirmButtonText: 'Login Sekarang',
        customClass: {
            title: 'fs-5 fw-bold', 
            htmlContainer: 'small text-muted' 
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "login.php";
        }
    });
    <?php endif; ?>
</script>

</body>
</html>