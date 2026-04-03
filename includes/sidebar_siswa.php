<?php
// includes/sidebar_siswa.php
if (!isset($current_page)) {
    $current_page = basename($_SERVER['PHP_SELF']); 
}
?>

<style>
    /* Styling tambahan agar sidebar lebih manis di mobile & desktop */
    .student-sidebar .list-group-item {
        border: none;
        padding: 12px 20px;
        margin: 4px 12px;
        border-radius: 12px !important;
        font-size: 0.9rem;
        font-weight: 500;
        color: #64748b;
        transition: all 0.2s ease;
    }

    .student-sidebar .list-group-item:hover:not(.disabled) {
        background-color: #f1f5f9;
        color: #1e40af;
        transform: translateX(5px);
    }

    .student-sidebar .list-group-item.active {
        background: var(--primary-gradient, linear-gradient(135deg, #1e40af 0%, #3b82f6 100%)) !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(30, 64, 175, 0.2);
    }

    .student-sidebar .list-group-item i {
        width: 20px;
        text-align: center;
    }

    .sidebar-full {
        padding-top: 15px; /* Jarak agar tidak mepet navbar */
    }

    @media (max-width: 991px) {
        .student-sidebar .list-group-item {
            margin: 4px 8px; /* Margin lebih kecil di HP */
            padding: 14px 15px; /* Area klik lebih besar di HP */
        }
    }
</style>

<div class="sidebar-full">
    <div class="list-group student-sidebar">
        
        <a href="dashboard.php" 
           class="list-group-item list-group-item-action <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
           <i class="fas fa-th-large me-2"></i> Dashboard
        </a>
        
        <?php if (isset($is_filled) && $is_filled): ?>
            <a href="#" 
               class="list-group-item disabled list-group-item-action d-flex justify-content-between align-items-center">
               <span>
                   <i class="fas fa-check-circle me-2 text-success"></i> Formulir Sudah Diisi 
               </span>
               <span class="badge bg-success rounded-pill">OK</span>
            </a>
        <?php else: ?>
            <a href="form_ppdb.php" 
               class="list-group-item list-group-item-action 
               <?php echo ($current_page == 'form_ppdb.php') ? 'active' : ''; ?>">
               <i class="fas fa-edit me-2"></i> Formulir Pendaftaran
            </a>
        <?php endif; ?>
        
        <a href="riwayat_pendaftaran.php" 
           class="list-group-item list-group-item-action <?php echo ($current_page == 'riwayat_pendaftaran.php') ? 'active' : ''; ?>">
           <i class="fas fa-history me-2"></i> Riwayat Pendaftaran
        </a>

         <a href="pembayaran.php" 
           class="list-group-item list-group-item-action <?php echo ($current_page == 'pembayaran.php') ? 'active' : ''; ?>">
           <i class="fas fa-wallet me-2"></i> Pembayaran
        </a> 

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
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1e40af',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        width: '320px', 
        customClass: {
            title: 'fs-5',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Menutup sidebar otomatis di HP sebelum logout (opsional)
            if (document.body.classList.contains('sidebar-open')) {
                document.body.classList.remove('sidebar-open');
            }
            window.location.href = "dashboard.php?action=logout";
        }
    })
}
</script>