<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SPMB - <?php echo $pageTitle ?? 'Aplikasi Pendaftaran'; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            --glass-bg: #ffffff;
            --sidebar-width: 260px;
            --navbar-height: 70px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding-top: var(--navbar-height);
            overflow-x: hidden;
        }

        /* ================= NAVBAR ================= */
        .navbar-top {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            height: var(--navbar-height);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050; /* Lebih tinggi dari sidebar */
            padding: 0 1rem;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            font-weight: 800;
            font-size: 1.1rem;
            color: #1e293b !important;
        }

        .navbar-logo {
            height: 32px;
            margin-right: 10px;
        }

        /* Hamburger Button Custom */
        .btn-toggle-sidebar {
            border: none;
            background: #f1f5f9;
            color: #1e40af;
            padding: 8px 12px;
            border-radius: 8px;
            margin-right: 10px;
            display: none; /* Sembunyi di desktop */
        }

        .user-profile-section {
            background: #f8fafc;
            padding: 4px 12px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-text {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
        }

        /* ================= LAYOUT & SIDEBAR ================= */
        .sidebar-full {
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: #fff;
            border-right: 1px solid #e2e8f0;
            z-index: 1040;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .content-area {
            margin-left: var(--sidebar-width);
            padding: 25px;
            min-height: calc(100vh - var(--navbar-height));
            transition: all 0.3s ease;
        }

        /* Overlay untuk menutup sidebar saat klik di luar (HP) */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1035;
        }

        /* ================= RESPONSIVE ================= */
        @media(max-width: 991px) {
            .btn-toggle-sidebar { display: block; } /* Muncul di HP */
            
            .sidebar-full {
                transform: translateX(-100%); /* Sembunyi ke kiri */
            }

            .content-area {
                margin-left: 0;
                padding: 15px;
            }

            /* Saat Sidebar Terbuka */
            .sidebar-open .sidebar-full {
                transform: translateX(0);
            }
            .sidebar-open .sidebar-overlay {
                display: block;
            }
            
            .navbar-brand span { font-size: 0.9rem; }
            .navbar-brand small { display: none; } /* Sembunyi teks kecil di HP agar muat */
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <nav class="navbar navbar-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            
            <div class="d-flex align-items-center">
                <button class="btn-toggle-sidebar" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>

                <a class="navbar-brand" href="dashboard.php">
                    <img src="logo_cahaya.png" class="navbar-logo">
                    <div class="d-flex flex-column" style="line-height:1.2;">
                        <span>SPMB ONLINE</span>
                        <small style="font-size:0.65rem;color:#64748b;">SMK CAHAYA</small>
                    </div>
                </a>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-profile-section">
                    <div style="width:28px;height:28px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-user text-secondary" style="font-size: 0.8rem;"></i>
                    </div>
                    <div class="d-flex flex-column d-none d-sm-flex"> <span class="navbar-text">
                            <b><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></b>
                        </span>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </nav>

    <script>
        // Fungsi sakti untuk buka tutup sidebar di HP
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }
    </script>

    <div class="main-wrapper">