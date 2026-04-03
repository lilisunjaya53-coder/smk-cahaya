<?php
// includes/sidebar_admin_kepsek.php
if (!isset($_SESSION['role'])) {
    return;
}

$role = $_SESSION['role'];
$current_page = basename($_SERVER['PHP_SELF']);

if ($role === 'Admin') {
    $menu_items = [
        ['Dashboard', 'dashboard.php', 'fas fa-th-large'],
        ['Verifikasi Pendaftar', 'admin_verifikasi.php', 'fas fa-user-check'],
        ['Verifikasi Pembayaran', 'admin_pembayaran.php', 'fas fa-money-check-alt'], // Menu baru bray
        ['Laporan & Statistik', 'admin_laporan.php', 'fas fa-chart-bar'],
        ['Manajemen Pengguna', 'admin_users.php', 'fas fa-users'],
    ];
} elseif ($role === 'Kepsek') {
    $menu_items = [
        ['Dashboard', 'dashboard.php', 'fas fa-th-large'],
   
        ['Lihat Laporan', 'admin_laporan.php', 'fas fa-file-alt'],
    ];
} else {
    return; 
}
?>

<style>
    /* Styling agar sidebar serasi dan responsive */
    .admin-sidebar .list-group-item {
        border: none;
        padding: 12px 20px;
        margin: 4px 12px;
        border-radius: 12px !important;
        font-size: 0.9rem;
        font-weight: 500;
        color: #475569;
        transition: all 0.2s ease;
    }

    .admin-sidebar .list-group-item:hover {
        background-color: #f1f5f9;
        color: #1e40af;
        transform: translateX(5px);
    }

    .admin-sidebar .list-group-item.active {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(30, 64, 175, 0.2);
    }

    .admin-sidebar .list-group-item i {
        width: 20px;
        text-align: center;
    }

    .sidebar-full {
        padding-top: 15px; 
    }

    @media (max-width: 991px) {
        .admin-sidebar .list-group-item {
            margin: 4px 8px;
            padding: 14px 15px;
        }
    }
</style>

<div class="sidebar-full">
    <div class="list-group admin-sidebar">
        <?php foreach ($menu_items as $item): ?>
            <a href="<?php echo $item[1]; ?>" 
               class="list-group-item list-group-item-action <?php echo ($current_page === basename($item[1])) ? 'active' : ''; ?>">
               <i class="<?php echo $item[2]; ?> me-2"></i> <?php echo $item[0]; ?>
            </a>
        <?php endforeach; ?>

        <div class="logout-item mt-3">
            <a href="javascript:void(0)" onclick="confirmLogout()" class="list-group-item list-group-item-action text-danger">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmLogout() {
    Swal.fire({
        title: 'Yakin ingin keluar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1e40af',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        width: '300px',
        customClass: {
            title: 'fs-5 fw-bold',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            if (document.body.classList.contains('sidebar-open')) {
                document.body.classList.remove('sidebar-open');
            }
            window.location.href = "dashboard.php?action=logout";
        }
    })
}
</script>