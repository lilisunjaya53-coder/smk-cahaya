<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMK Cahaya - Official Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <style>
        :root {
            --toska-main: #0d9488;
            --toska-dark: #0f766e;
            --toska-soft: #f0fdfa;
            --dark-navy: #0f172a;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; color: #334155; scroll-behavior: smooth; }

        /* Navbar Glassmorphism bray */
        .navbar {
            padding: 20px 0; background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px); border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .btn-portal-login {
            color: var(--toska-main) !important;
            border: 2px solid var(--toska-main) !important;
            font-weight: 800 !important;
            padding: 10px 25px !important;
            transition: 0.3s !important;
            text-transform: uppercase;
        }
        .btn-portal-login:hover {
            background-color: var(--toska-soft) !important;
            transform: translateY(-2px);
        }

        .btn-portal-daftar {
            background: linear-gradient(135deg, var(--toska-main), var(--toska-dark)) !important;
            color: white !important;
            border: none !important;
            font-weight: 800 !important;
            padding: 12px 30px !important;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(13, 148, 136, 0.4) !important;
            transition: 0.3s !important;
        }
        .btn-portal-daftar:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(13, 148, 136, 0.6) !important;
            color: white !important;
        }

        .section-tag { color: var(--toska-main); font-weight: 800; text-transform: uppercase; letter-spacing: 3px; font-size: 0.8rem; display: block; margin-bottom: 10px; }
        .section-title { font-weight: 800; color: var(--dark-navy); font-size: 2.8rem; margin-bottom: 40px; }

        /* Visi Misi Box bray */
        .vm-card {
            background: var(--toska-soft); border-radius: 30px; padding: 40px;
            border-left: 10px solid var(--toska-main); height: 100%;
        }

        /* Jurusan Cards bray */
        .jurusan-card {
            border: none; border-radius: 30px; background: white; padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;
            transition: 0.4s; height: 100%; text-align: center;
        }
        .jurusan-card:hover { transform: translateY(-15px); border-color: var(--toska-main); }
        .icon-circle { width: 60px; height: 60px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 20px; }

        /* Galeri Foto Gedung Sekolah bray */
        .photo-frame {
            border-radius: 40px; overflow: hidden; position: relative;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15); border: 10px solid white;
        }
        .photo-frame img { width: 100%; transition: 0.5s; }
        .photo-frame:hover img { transform: scale(1.05); }

        /* Section Kontak bray */
        .contact-box {
            background: #f8fafc; border-radius: 30px; padding: 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;
        }
        .contact-icon {
            width: 50px; height: 50px; background: var(--toska-main); color: white;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; margin-bottom: 15px;
        }

        footer { background: var(--dark-navy); color: rgba(255,255,255,0.7); padding: 80px 0 40px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand fw-800 d-flex align-items-center" href="#">
                <img src="logo_cahaya.png" height="50" class="me-3">
                <span style="color: var(--dark-navy);">SMK CAHAYA</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navPortal">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navPortal">
                <div class="ms-auto d-flex flex-column flex-lg-row gap-3 align-items-center">
                    <a href="login.php" class="btn btn-portal-login rounded-pill">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </a>
                    <a href="register_akun.php" class="btn btn-portal-daftar rounded-pill">
                        <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="py-5 mt-4">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6" data-aos="zoom-in">
                    <div class="photo-frame">
                        <img src="smk_cahaya.png" alt="Gedung SMK Cahaya">
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <span class="section-tag">Gedung Utama</span>
                    <h2 class="section-title text-start">Fasilitas Sekolah yang Modern & Nyaman</h2>
                    <p class="text-muted mb-4">Lingkungan belajar yang asri dan gedung yang representatif mendukung kenyamanan setiap siswa dalam menuntut ilmu di SMK Cahaya. Dilengkapi dengan area praktek yang luas sesuai standar industri.</p>
                    <div class="row g-3">
                        <div class="col-6"><i class="fas fa-check-circle text-success me-2"></i> Ruang Kelas</div>
                        <div class="col-6"><i class="fas fa-wifi text-success me-2"></i> WiFi Area</div>
                        <div class="col-6"><i class="fas fa-futbol text-success me-2"></i> Lapangan Olahraga</div>
                        <div class="col-6"><i class="fas fa-university text-success me-2"></i> Aula Serbaguna</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light" id="profil">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-tag">Landasan Kami</span>
                <h2 class="section-title">Visi & Misi Sekolah</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="vm-card shadow-sm">
                        <h3 class="fw-800 text-dark mb-4"><i class="fas fa-eye me-2 text-primary"></i> Visi</h3>
                        <p class="fs-5 text-muted">"Menjadi pusat pendidikan kejuruan yang menghasilkan lulusan unggul, mandiri, berkarakter, dan berdaya saing global di era digital."</p>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="vm-card shadow-sm">
                        <h3 class="fw-800 text-dark mb-4"><i class="fas fa-bullseye me-2 text-danger"></i> Misi</h3>
                        <ul class="list-unstyled d-flex flex-column gap-3 text-muted">
                            <li><i class="fas fa-check-circle text-success me-2"></i> Menyelenggarakan pembelajaran berbasis industri dan teknologi terkini.</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Mengembangkan potensi bakat dan kreativitas siswa secara maksimal.</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Menanamkan nilai kedisiplinan dan integritas moral yang kuat.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white" id="jurusan">
        <div class="container py-4">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-tag">Pilihan Jurusan</span>
                <h2 class="section-title">Program Keahlian Kami</h2>
            </div>
            
            <div class="row g-4 justify-content-center mb-4">
                <div class="col-lg-4 col-md-6" data-aos="zoom-in">
                    <div class="jurusan-card shadow-sm border-0 h-100 p-4">
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary mx-auto shadow-sm">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h5 class="fw-bold mt-3">MPLB</h5>
                        <p class="text-muted small">Manajemen Perkantoran & Layanan Bisnis.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="100">
                    <div class="jurusan-card shadow-sm border-0 h-100 p-4">
                        <div class="icon-circle bg-success bg-opacity-10 text-success mx-auto shadow-sm">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="fw-bold mt-3">BD</h5>
                        <p class="text-muted small">Bisnis Digital.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                    <div class="jurusan-card shadow-sm border-0 h-100 p-4">
                        <div class="icon-circle bg-danger bg-opacity-10 text-danger mx-auto shadow-sm">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <h5 class="fw-bold mt-3">TLM</h5>
                        <p class="text-muted small">Teknologi Laboratorium Medik.</p>
                    </div>
                </div>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="300">
                    <div class="jurusan-card shadow-sm border-0 h-100 p-4">
                        <div class="icon-circle bg-warning bg-opacity-10 text-warning mx-auto shadow-sm">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h5 class="fw-bold mt-3">TKR</h5>
                        <p class="text-muted small">Teknik Kendaraan Ringan.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="400">
                    <div class="jurusan-card shadow-sm border-0 h-100 p-4">
                        <div class="icon-circle bg-info bg-opacity-10 text-info mx-auto shadow-sm">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <h5 class="fw-bold mt-3">TKJT</h5>
                        <p class="text-muted small">Teknik Jaringan Komputer & Telekomunikasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white" id="kontak">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-tag">Hubungi Kami</span>
                <h2 class="section-title">Informasi Kontak & Lokasi</h2>
            </div>
            <div class="row g-4 align-items-center">
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="contact-box">
                        <div class="mb-4">
                            <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <h6 class="fw-bold mb-1">Alamat Sekolah</h6>
                            <p class="small text-muted mb-0">Jl. KH. Abdul Hamid Km2, Situ Ilir, Kec. Cibungbulang, Kabupaten Bogor, Jawa Barat 16630.</p>
                        </div>
                        <div class="mb-4">
                            <div class="contact-icon"><i class="fab fa-whatsapp"></i></div>
                            <h6 class="fw-bold mb-1">WhatsApp Admin</h6>
                            <p class="small text-muted mb-0">+62 856-9155-2116</p>
                        </div>
                        <div>
                            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                            <h6 class="fw-bold mb-1">Email Resmi</h6>
                            <p class="small text-muted mb-0">smkcahaya56@yahoo.com</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7" data-aos="fade-left">
                    <div class="rounded-5 overflow-hidden shadow-lg border" style="height: 400px;">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.262529286461!2d106.63467657451636!3d-6.614275064657158!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69da59f71c480b%3A0xe54d3d2a7141f23!2sSitu%20Ilir%2C%20Cibungbulang%2C%20Bogor%20Regency%2C%20West%20Java!5e0!3m2!1sen!2sid!4v1709400000000!5m2!1sen!2sid" 
                            width="100%" 
                            height="400" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container text-center">
            <img src="logo_cahaya.png" height="70" class="mb-4">
            <h4 class="fw-800 text-white mb-4">SMK CAHAYA</h4>
            <div class="d-flex justify-content-center gap-4 mb-5 fs-4">
                <a href="#" class="text-white opacity-50 hover-opacity-100"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-white opacity-50 hover-opacity-100"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-white opacity-50 hover-opacity-100"><i class="fab fa-youtube"></i></a>
            </div> 
            <p class="small opacity-50 mb-0">© 2026 SMK CAHAYA. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>AOS.init({ duration: 1000, once: true });</script>
</body>
</html>