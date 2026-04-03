<?php
require_once 'config.php';
checkAuth();
if (!isAdmin()) exit;

$id = (int)($_GET['id'] ?? 0);
$conn = connectDB();

// Query mengambil semua kolom sesuai struktur tabel pendaftar terbaru
$sql = "SELECT p.*, j.nama_keahlian 
        FROM pendaftar p 
        LEFT JOIN jurusan j ON p.id_jurusan_pilihan = j.id_jurusan 
        WHERE p.id_pendaftar = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    echo "<div class='alert alert-danger m-3 text-center fw-bold'>Data pendaftar tidak ditemukan!</div>";
    exit;
}
?>

<style>
    .detail-container { padding: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .section-header { 
        background: #f8f9fc; 
        padding: 10px 15px; 
        border-left: 5px solid #4e73df; 
        font-weight: 800; 
        color: #4e73df; 
        margin-bottom: 20px; 
        text-transform: uppercase;
        font-size: 0.9rem;
    }
    .info-label { font-weight: 700; color: #858796; font-size: 0.72rem; text-transform: uppercase; margin-bottom: 2px; display: block; }
    .info-value { color: #2e3750; font-weight: 600; font-size: 0.95rem; border-bottom: 1px solid #eaecf4; padding-bottom: 5px; margin-bottom: 15px; display: block; }
    .img-pendaftar { border-radius: 12px; border: 5px solid #fff; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); width: 100%; max-width: 220px; }
    .doc-btn { transition: 0.2s; border-radius: 8px; font-weight: 600; }
</style>

<div class="detail-container">
    <div class="row">
        <div class="col-md-3 text-center border-end mb-4">
            <div class="mb-4">
                <span class="info-label mb-3">Pas Foto Calon Siswa</span>
                <?php if (!empty($data['foto_file'])): ?>
                    <img src="uploads/<?php echo $data['foto_file']; ?>" class="img-pendaftar">
                <?php else: ?>
                    <div class="bg-light border rounded py-5 text-muted">
                        <i class="fas fa-user-tie fa-4x opacity-25"></i>
                        <p class="small mt-2">Foto tidak tersedia</p>
                    </div>
                <?php endif; ?>
            </div>

            <span class="info-label mb-2">Dokumen Terlampir</span>
            <div class="d-grid gap-2">
                <?php 
                $berkas = [
                    'kk_file' => ['label' => 'Kartu Keluarga', 'icon' => 'fa-users'],
                    'ktp_ortu_file' => ['label' => 'KTP Orang Tua', 'icon' => 'fa-id-card'],
                    'ijazah_sd_file' => ['label' => 'Ijazah SD/MI', 'icon' => 'fa-file-graduation-cap'],
                    'skl_file' => ['label' => 'SKL SMP/MTs', 'icon' => 'fa-file-alt'] // DITAMBAHKAN
                ];
                foreach ($berkas as $key => $val): if (!empty($data[$key])): ?>
                    <a href="uploads/<?php echo $data[$key]; ?>" target="_blank" class="btn btn-sm btn-outline-primary doc-btn text-start">
                        <i class="fas <?php echo $val['icon']; ?> me-2"></i> <?php echo $val['label']; ?>
                    </a>
                <?php endif; endforeach; ?>
            </div>
        </div>

        <div class="col-md-9 ps-md-4">
            <div class="section-header">A. Identitas Calon Peserta Didik</div>
            <div class="row">
                <div class="col-md-4">
                    <span class="info-label">No. Pendaftaran</span>
                    <span class="info-value text-primary fw-bold"><?php echo $data['no_pendaftaran']; ?></span>
                </div>
                <div class="col-md-4">
                    <span class="info-label">NIK Siswa</span>
                    <span class="info-value"><?php echo $data['nik_siswa'] ?: '-'; ?></span>
                </div>
                <div class="col-md-4">
                    <span class="info-label">NISN</span>
                    <span class="info-value"><?php echo $data['nisn']; ?></span>
                </div>
                <div class="col-md-8">
                    <span class="info-label">Nama Lengkap</span>
                    <span class="info-value fw-bold text-dark"><?php echo strtoupper($data['nama_lengkap']); ?></span>
                </div>
                <div class="col-md-4">
                    <span class="info-label">Nama Panggilan</span>
                    <span class="info-value"><?php echo $data['nama_panggilan'] ?: '-'; ?></span>
                </div>
                <div class="col-md-4">
                    <span class="info-label">WhatsApp</span>
                    <span class="info-value text-success">
                        <i class="fab fa-whatsapp me-1"></i> <?php echo $data['no_hp_siswa'] ?: '-'; ?>
                    </span>
                </div>
                <div class="col-md-4">
                    <span class="info-label">Tempat, Tgl Lahir</span>
                    <span class="info-value"><?php echo ($data['tempat_lahir'] ? $data['tempat_lahir'] . ", " : "") . date('d F Y', strtotime($data['tgl_lahir'])); ?></span>
                </div>
                <div class="col-md-4">
                    <span class="info-label">Agama</span>
                    <span class="info-value"><?php echo $data['agama'] ?: '-'; ?></span>
                </div>
                <div class="col-md-4">
                    <span class="info-label">Status Keluarga</span>
                    <span class="info-value"><?php echo $data['status_yatim']; ?></span>
                </div>
                <div class="col-md-4">
                    <span class="info-label">Jenis Kelamin</span>
                    <span class="info-value"><?php echo ($data['jenis_kelamin'] == 'L' ? 'Laki-Laki' : 'Perempuan'); ?></span>
                </div>
                <div class="col-md-12">
                    <span class="info-label">Alamat Domisili</span>
                    <span class="info-value"><?php echo $data['alamat_siswa']; ?></span>
                </div>
            </div>

            <div class="section-header mt-3">B. Riwayat Pendidikan Dasar</div>
            <div class="row">
                <div class="col-md-6">
                    <span class="info-label">Asal SD/MI</span>
                    <span class="info-value"><?php echo $data['asal_sd'] ?: '-'; ?></span>
                </div>
                <div class="col-md-6">
                    <span class="info-label">Asal SMP/MTs</span>
                    <span class="info-value"><?php echo $data['asal_sekolah'] ?: '-'; ?></span>
                </div>
                <div class="col-md-6">
                    <span class="info-label">Alamat SD/MI</span>
                    <span class="info-value"><?php echo $data['alamat_sd'] ?: '-'; ?></span>
                </div>
                <div class="col-md-6">
                    <span class="info-label">Alamat SMP/MTs</span>
                    <span class="info-value"><?php echo $data['alamat_sekolah'] ?: '-'; ?></span>
                </div>
            </div>

            <div class="section-header mt-3">C. Data Orang Tua Kandung</div>
            <div class="row">
                <div class="col-md-6 border-end pe-md-4">
                    <p class="small fw-bold text-primary mb-2"><i class="fas fa-male me-1"></i> IDENTITAS AYAH</p>
                    <span class="info-label">Nama Ayah</span><span class="info-value"><?php echo $data['nama_ayah'] ?: '-'; ?></span>
                    <span class="info-label">NIK Ayah</span><span class="info-value"><?php echo $data['nik_ayah'] ?: '-'; ?></span>
                    <span class="info-label">Pekerjaan</span><span class="info-value"><?php echo $data['pekerjaan_ayah'] ?: '-'; ?></span>
                </div>
                <div class="col-md-6 ps-md-4">
                    <p class="small fw-bold text-danger mb-2"><i class="fas fa-female me-1"></i> IDENTITAS IBU</p>
                    <span class="info-label">Nama Ibu</span><span class="info-value"><?php echo $data['nama_ibu'] ?: '-'; ?></span>
                    <span class="info-label">NIK Ibu</span><span class="info-value"><?php echo $data['nik_ibu'] ?: '-'; ?></span>
                    <span class="info-label">Pekerjaan</span><span class="info-value"><?php echo $data['pekerjaan_ibu'] ?: '-'; ?></span>
                </div>
            </div>

            <div class="section-header mt-3">D. Kompetensi Keahlian & Rekomendasi</div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-primary bg-opacity-10 border border-primary rounded-3 text-center shadow-sm">
                        <span class="info-label text-primary">Jurusan Dipilih:</span>
                        <h5 class="fw-bold mb-0 text-dark"><?php echo $data['nama_keahlian'] ?: 'Belum Memilih'; ?></h5>
                    </div>
                </div>
                <div class="col-md-6">
                     <div class="p-3 bg-light border rounded-3 text-center shadow-sm">
                        <span class="info-label">Informasi Dari:</span>
                        <h5 class="fw-bold mb-0 text-dark"><?php echo $data['rekomendasi'] ?: '-'; ?></h5>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>