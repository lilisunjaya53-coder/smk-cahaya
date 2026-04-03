<?php
require_once 'config.php';
$error = '';

// Cek jika sudah login, alihkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong.";
    } else {
        $conn = connectDB();
        $sql = "SELECT u.id_user, u.password, r.role_name
                FROM users u
                JOIN user_roles ur ON u.id_user = ur.user_id
                JOIN roles r ON ur.role_id = r.id_role
                WHERE u.username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id_user'];
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $user['role_name'];
                    
                    // REDIRECT DENGAN PARAMETER LOGIN SUCCESS
                    header("Location: dashboard.php?login=success");
                    exit;
                } else {
                    $error = "Password salah.";
                }
            } else {
                $error = "Username tidak ditemukan.";
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PPDB SMK Cahaya</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Mengatur agar konten benar-benar di tengah layar tanpa terpotong */
        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center; 
            justify-content: center;
            background: linear-gradient(135deg, #4ce1dc 0%, #3b82f6 100%);
            font-family: 'Outfit', sans-serif;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 360px; /* Ukuran mungil & ringkas */
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 30px 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .login-header img {
            width: 65px;
            height: auto;
            margin-bottom: 15px;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
        }

        .main-title {
            color: #1e293b;
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .school-info {
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .badge-tahun {
            font-size: 0.65rem;
            background: #fef2f2;
            color: #ef4444;
            padding: 3px 12px;
            border-radius: 50px;
            font-weight: 800;
            display: inline-block;
            margin-top: 5px;
        }

        .form-group-custom {
            text-align: left;
            margin-bottom: 15px;
        }

        .form-group-custom label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
            margin-left: 4px;
            display: block;
        }

        .form-control-custom {
            width: 100%;
            padding: 10px 16px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-control-custom:focus {
            background: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            outline: none;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #4ce1dc, #3b82f6);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            margin-top: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-login:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);
        }

        .footer-link {
            margin-top: 20px;
            font-size: 0.85rem;
            color: #64748b;
        }

        .footer-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 700;
        }

        .alert {
            border-radius: 10px;
            font-size: 0.8rem;
            border: none;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <img src="logo_cahaya.png" alt="Logo SMK Cahaya" />
            <h1 class="main-title">SPMB Online</h1>
            <div class="school-info">
                SMK CAHAYA <br> 
                <span class="badge-tahun">2026 / 2027</span>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="post">
            <div class="form-group-custom">
                <label>Username</label>
                <input type="text" class="form-control-custom" name="username" placeholder="Masukkan username" required autofocus>
            </div>
            
            <div class="form-group-custom">
                <label>Password</label>
                <input type="password" class="form-control-custom" name="password" placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="btn-login">Masuk Sekarang</button>
        </form>

        <div class="footer-link">
            Belum punya akun? <a href="register_akun.php">Daftar Akun</a>
        </div>
    </div>
</div>

</body>
</html> 