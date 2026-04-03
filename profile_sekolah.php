<?php
require_once 'config.php';

checkAuth();
$currentUser = getCurrentUser();

$pageTitle = "Profil Sekolah";
include 'includes/header.php';

// Deteksi Sidebar
if (isSiswa()) {
    $pendaftar_id = $currentUser['id_pendaftar'];
    $is_filled = !is_null($pendaftar_id);
    include 'includes/sidebar_siswa.php';
} else {
    include 'includes/sidebar_admin_kepsek.php';
}
?>

<style>
    :root {
        --sidebar-width: 260px;
        --accent-indigo: #6366f1;
    }

    .content-area {
        margin-left: var(--sidebar-width) !important;
        padding: 40px !important;
        background-color: #f8fafc;
        min-height: calc(100vh - 70px);
        box-sizing: border-box;
    }

    /* Hero Section - Foto Sekolah */
    .school-hero {
        position: relative;
        height: 450px;
        border-radius: 40px;
        overflow: hidden;
        margin-bottom: 40px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }

    .school-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hero-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 60px 40px;
        background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%);
        color: white;
    }

    /* Card Layout */
    .glass-card {
        background: white;
        border-radius: 30px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        padding: 35px;
        height: 100%;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    .misi-item {
        background: #f1f5f9;
        border-radius: 20px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        height: 100%;
    }

    .misi-item:hover {
        background: #ffffff;
        border-color: var(--accent-indigo);
        transform: scale(1.02);
    }

    .misi-number {
        width: 35px;
        height: 35px;
        background: var(--accent-indigo);
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        margin-bottom: 15px;
    }

    .list-contact {
        list-style: none;
        padding: 0;
    }

    .list-contact li {
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
        color: #64748b;
        display: flex;
        gap: 15px;
    }

    .list-contact li i {
        color: var(--accent-indigo);
        margin-top: 4px;
    }

    @media (max-width: 991px) {
        .content-area { margin-left: 0 !important; width: 100%; padding: 20px !important; }
        .school-hero { height: 300px; }
    }
</style>

<main class="content-area">
    <div class="container-fluid p-0">

        <div class="school-hero">
           <img src="smk_cahaya.png" class="navbar-logo">
            <div class="hero-overlay">
                <h6 class="text-uppercase fw-bold text-info mb-2" style="letter-spacing: 2px;">School Profile</h6>
                <h1 class="display-4 fw-800 m-0" style="letter-spacing: -2px;">Profil SMK Cahaya</h1>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="glass-card">
                    <div class="mb-5">
                        <h4 class="fw-800 text-dark mb-3"><i class="fas fa-eye me-2 text-primary"></i> Visi Sekolah</h4>
                        <p class="fs-5 text-muted shadow-none p-3 bg-light rounded-4 border-start border-4 border-primary">
                            "Menjadi lembaga pendidikan yang unggul dalam Imtaq dan Iptek serta berwawasan lingkungan."
                        </p>
                    </div>

                    <h4 class="fw-800 text-dark mb-4"><i class="fas fa-bullseye me-2 text-danger"></i> Misi Strategis</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="misi-item">
                                <div class="misi-number">1</div>
                                <p class="small fw-bold text-dark m-0">Melaksanakan pembelajaran dan bimbingan secara efektif.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="misi-item">
                                <div class="misi-number">2</div>
                                <p class="small fw-bold text-dark m-0">Menumbuhkan semangat berprestasi kepada seluruh warga sekolah.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="misi-item">
                                <div class="misi-number">3</div>
                                <p class="small fw-bold text-dark m-0">Mendorong dan membantu setiap peserta didik untuk mengenal potensi dan bakat dirinya sehingga dapat berkembang dengan baik.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="misi-item">
                                <div class="misi-number">4</div>
                                <p class="small fw-bold text-dark m-0">Menumbuhkan penghayatan terhadap ajaran agama yang di anut dan budaya bangsa sehingga menjadi sumber kearifan dalam bertindak.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="glass-card">
                    <h4 class="fw-800 text-dark mb-4">Informasi Kontak</h4>
                    <ul class="list-contact">
                        <li><i class="fas fa-map-marker-alt"></i> <span>Jl. KH Abdul Hamid KM 02 Ds. Situ ilir Kec. Pamijahan Kab. Bogor</span></li>
                        <li><i class="fas fa-phone-alt"></i> <span>+62 856-9155-2116 (Bu Amaliah)</span></li>
                         <li><i class="fas fa-phone-alt"></i> <span>+62 858-1070-9747 (Bu Putry)</span></li>
                        <li><i class="fas fa-envelope"></i> <span>smkcahaya855@gmail.com</span></li>
                        <li><i class="fas fa-clock"></i> <span>Senin - Jumat (07.00 - 15.30)</span></li>
                    </ul>

                    <div class="mt-4 pt-4 border-top">
                        <h6 class="fw-bold mb-3">Ikuti Media Sosial Kami</h6>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-primary rounded-circle" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-danger rounded-circle" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="btn btn-info rounded-circle text-white" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>