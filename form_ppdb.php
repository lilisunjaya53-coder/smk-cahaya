<?php
require_once 'config.php';

// Pengecekan Autentikasi
checkAuth(); 
$currentUser = getCurrentUser(); 

if (!isSiswa() || !$currentUser) {
    header("Location: dashboard.php");
    exit;
}

$conn = connectDB();
$is_filled = !is_null($currentUser['id_pendaftar']); 
$current_page = basename($_SERVER['PHP_SELF']); 

if ($is_filled) {
    header("Location: riwayat_pendaftaran.php?info=already_submitted");
    exit;
}

$error = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction(); 
    
    try {
        // 1. Ambil Data dari Form
        $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
        $nama_panggilan = trim($_POST['nama_panggilan'] ?? '');
        $nik_siswa = trim($_POST['nik_siswa'] ?? '');
        $nisn = trim($_POST['nisn'] ?? ''); 
        $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
        $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
        $tgl_lahir = $_POST['tgl_lahir'] ?? '';
        $agama = $_POST['agama'] ?? '';
        $status_yatim = $_POST['status_yatim'] ?? 'Tidak';

        $asal_sd = trim($_POST['asal_sd'] ?? '');
        $alamat_sd = trim($_POST['alamat_sd'] ?? '');
        $asal_sekolah = trim($_POST['asal_sekolah'] ?? ''); 
        $alamat_sekolah = trim($_POST['alamat_sekolah'] ?? '');
        $no_hp_siswa = trim($_POST['no_hp_siswa'] ?? '');
        $alamat_siswa = trim($_POST['alamat_siswa'] ?? '');

        $nama_ayah = trim($_POST['nama_ayah'] ?? '');
        $nik_ayah = trim($_POST['nik_ayah'] ?? '');
        $pekerjaan_ayah = trim($_POST['pekerjaan_ayah'] ?? '');
        $nama_ibu = trim($_POST['nama_ibu'] ?? '');
        $nik_ibu = trim($_POST['nik_ibu'] ?? '');
        $pekerjaan_ibu = trim($_POST['pekerjaan_ibu'] ?? '');
        
        $nama_wali = trim($_POST['nama_wali'] ?? '');
        $hubungan_wali = trim($_POST['hubungan_wali'] ?? '');
        $id_jurusan_pilihan = (int)($_POST['id_jurusan_pilihan'] ?? 0); 
        $rekomendasi = trim($_POST['rekomendasi'] ?? '');
        
        // --- VALIDASI FORMAL DAN TEKNIS ---
        if (empty($nama_lengkap)) $error[] = "Nama Lengkap wajib diisi.";
        
        if (!ctype_digit($nik_siswa) || strlen($nik_siswa) != 16) {
            $error[] = "NIK Siswa harus terdiri dari 16 digit angka.";
        }
        
        if (empty($nik_ayah)) {
            $error[] = "NIK Ayah wajib diisi.";
        } elseif (!ctype_digit($nik_ayah) || strlen($nik_ayah) != 16) {
            $error[] = "NIK Ayah harus terdiri dari 16 digit angka.";
        }

        if (empty($nik_ibu)) {
            $error[] = "NIK Ibu wajib diisi.";
        } elseif (!ctype_digit($nik_ibu) || strlen($nik_ibu) != 16) {
            $error[] = "NIK Ibu harus terdiri dari 16 digit angka.";
        }

        if (!ctype_digit($nisn) || strlen($nisn) != 10) {
            $error[] = "NISN harus terdiri dari 10 digit angka.";
        }

        if (empty($agama)) $error[] = "Mohon pilih Agama.";
        if ($id_jurusan_pilihan == 0) $error[] = "Mohon pilih Kompetensi Keahlian.";

        // Validasi Berkas (Menambahkan skl_file)
        $files_required = ['kk_file', 'ktp_ortu_file', 'ijazah_sd_file', 'skl_file', 'foto_file'];
        foreach ($files_required as $f) {
            if (!isset($_FILES[$f]) || $_FILES[$f]['error'] != 0) {
                $error[] = "Berkas " . strtoupper(str_replace('_file', '', $f)) . " belum dipilih.";
            }
        }

        if (!empty($error)) {
            throw new Exception("Lengkapi data yang masih salah.");
        }

        // 2. PROSES UPLOAD FILE
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $uploaded_paths = [];

        foreach ($files_required as $f) {
            $ext = pathinfo($_FILES[$f]['name'], PATHINFO_EXTENSION);
            $new_name = $f . "_" . $nisn . "_" . time() . "." . $ext;
            move_uploaded_file($_FILES[$f]['tmp_name'], $upload_dir . $new_name);
            $uploaded_paths[$f] = $new_name;
        }
        
        $no_pendaftaran = generateNoPendaftaran($conn);
        $tgl_daftar = date('Y-m-d');
        $user_id = $currentUser['id_user'];
        
        // 3. Query Insert Data Pokok Pendaftar
        $sql_pendaftar = "INSERT INTO pendaftar (
            no_pendaftaran, tgl_daftar, nama_lengkap, nama_panggilan, nik_siswa, nisn, 
            jenis_kelamin, tempat_lahir, tgl_lahir, agama, status_yatim, 
            no_hp_siswa, alamat_siswa, id_jurusan_pilihan
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql_pendaftar);
        $stmt->bind_param("sssssssssssssi", 
            $no_pendaftaran, $tgl_daftar, $nama_lengkap, $nama_panggilan, $nik_siswa, $nisn,
            $jenis_kelamin, $tempat_lahir, $tgl_lahir, $agama, $status_yatim, 
            $no_hp_siswa, $alamat_siswa, $id_jurusan_pilihan
        );
        
        if (!$stmt->execute()) { throw new Exception("Error Simpan Data Siswa: " . $stmt->error); }
        $pendaftar_id = $conn->insert_id; 

        // 4. Query Insert Pendaftar Ortu
        $sql_ortu = "INSERT INTO pendaftar_ortu (id_pendaftar, nama_ayah, nik_ayah, pekerjaan_ayah, nama_ibu, nik_ibu, pekerjaan_ibu, nama_wali, hubungan_wali) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_ortu = $conn->prepare($sql_ortu);
        $stmt_ortu->bind_param("issssssss", $pendaftar_id, $nama_ayah, $nik_ayah, $pekerjaan_ayah, $nama_ibu, $nik_ibu, $pekerjaan_ibu, $nama_wali, $hubungan_wali);
        if (!$stmt_ortu->execute()) { throw new Exception("Error Simpan Data Orang Tua: " . $stmt_ortu->error); }

        // 5. Query Insert Pendaftar Berkas
        $sql_berkas = "INSERT INTO pendaftar_berkas (id_pendaftar, kk_file, ktp_ortu_file, ijazah_sd_file, skl_file, foto_file) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_berkas = $conn->prepare($sql_berkas);
        $stmt_berkas->bind_param("isssss", $pendaftar_id, $uploaded_paths['kk_file'], $uploaded_paths['ktp_ortu_file'], $uploaded_paths['ijazah_sd_file'], $uploaded_paths['skl_file'], $uploaded_paths['foto_file']);
        if (!$stmt_berkas->execute()) { throw new Exception("Error Simpan Dokumen Berkas: " . $stmt_berkas->error); }

        // 6. Query Insert Pendaftar Pendidikan (Using asal_smp and alamat_smp)
        $sql_pendidikan = "INSERT INTO pendaftar_pendidikan (id_pendaftar, asal_sd, alamat_sd, asal_smp, alamat_smp) VALUES (?, ?, ?, ?, ?)";
        $stmt_pendidikan = $conn->prepare($sql_pendidikan);
        $stmt_pendidikan->bind_param("issss", $pendaftar_id, $asal_sd, $alamat_sd, $asal_sekolah, $alamat_sekolah);
        if (!$stmt_pendidikan->execute()) { throw new Exception("Error Simpan Riwayat Pendidikan: " . $stmt_pendidikan->error); }
        
        $conn->query("INSERT INTO pendaftar_status (id_pendaftar) VALUES ($pendaftar_id)");
        $conn->query("UPDATE users SET id_pendaftar = $pendaftar_id WHERE id_user = $user_id");
        $conn->commit();
        header("Location: dashboard.php?status=success_ppdb");
        exit;

   } catch (Exception $e) {
        $conn->rollback();
        // INI KUNCINYA BRAY: Masukin pesan error ke array supaya muncul di form
        $error[] = "Sistem Gagal Menyimpan: " . $e->getMessage();
    }
}

$jurusan_options = [];
$res_j = $conn->query("SELECT id_jurusan, nama_keahlian FROM jurusan ORDER BY nama_keahlian");
if ($res_j) $jurusan_options = $res_j->fetch_all(MYSQLI_ASSOC);
$conn->close();

include 'includes/header.php'; 
include 'includes/sidebar_siswa.php';
?>

<style>
    :root { --primary-color: #4e73df; --dark-bg: #f8f9fc; }
    .content-area { background-color: var(--dark-bg); padding: 40px 20px; }
    .form-header-title { font-weight: 800; color: #2e3750; text-transform: uppercase; position: relative; margin-bottom: 30px; display: inline-block; }
    .form-header-title::after { content: ''; position: absolute; width: 60%; height: 4px; background: var(--primary-color); bottom: -10px; left: 0; border-radius: 10px; }
    .registration-card { border: none; border-radius: 15px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
    .section-divider { border-left: 5px solid var(--primary-color); padding-left: 15px; margin-bottom: 25px; font-weight: 700; color: #4e73df; }
    .required-star { color: #e74a3b; }
    .form-label { font-weight: 700; color: #4a4a4a; font-size: 0.85rem; }
    .btn-submit-form { background: linear-gradient(180deg, #4e73df 10%, #224abe 100%); border: none; padding: 15px; font-weight: 700; border-radius: 10px; }
</style>

<main class="content-area"> 
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                <h2 class="form-header-title">Formulir Pendaftaran SPMB</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
                        <h6 class="fw-bold">Terdapat kesalahan pengisian:</h6>
                        <ul class="mb-0 small">
                            <?php foreach ($error as $err) echo "<li>$err</li>"; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card registration-card mb-5">
                    <div class="card-body p-4 p-md-5">
                        <form action="" method="post" enctype="multipart/form-data">
                            
                            <div class="section-divider h5 text-uppercase">A. Identitas Calon Siswa</div>
                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">NIK Siswa (Sesuai KK) <span class="required-star">*</span></label>
                                    <input type="text" class="form-control" name="nik_siswa" value="<?= htmlspecialchars($_POST['nik_siswa'] ?? '') ?>" placeholder="16 digit NIK" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NISN <span class="required-star">*</span></label>
                                    <input type="text" class="form-control" name="nisn" value="<?= htmlspecialchars($_POST['nisn'] ?? '') ?>" placeholder="10 digit NISN" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Nama Lengkap Siswa <span class="required-star">*</span></label>
                                    <input type="text" class="form-control" name="nama_lengkap" value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>" placeholder="Sesuai Ijazah" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nama Panggilan</label>
                                    <input type="text" class="form-control" name="nama_panggilan" value="<?= htmlspecialchars($_POST['nama_panggilan'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Agama <span class="required-star">*</span></label>
                                    <select class="form-select" name="agama" required>
                                        <option value="">-- Pilih --</option>
                                        <?php 
                                        $list_agama = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha'];
                                        foreach($list_agama as $ag) {
                                            $selected = (($_POST['agama'] ?? '') == $ag) ? 'selected' : '';
                                            echo "<option value='$ag' $selected>$ag</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status Keluarga <span class="required-star">*</span></label>
                                    <select class="form-select" name="status_yatim" required>
                                        <?php 
                                        $statuses = ['Tidak' => 'Lengkap', 'Yatim' => 'Yatim', 'Piatu' => 'Piatu', 'Yatim Piatu' => 'Yatim Piatu'];
                                        foreach($statuses as $val => $lbl) {
                                            $selected = (($_POST['status_yatim'] ?? 'Tidak') == $val) ? 'selected' : '';
                                            echo "<option value='$val' $selected>$lbl</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jenis Kelamin <span class="required-star">*</span></label>
                                    <select class="form-select" name="jenis_kelamin" required>
                                        <option value="L" <?= (($_POST['jenis_kelamin'] ?? '') == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                        <option value="P" <?= (($_POST['jenis_kelamin'] ?? '') == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tempat Lahir <span class="required-star">*</span></label>
                                    <input type="text" class="form-control" name="tempat_lahir" value="<?= htmlspecialchars($_POST['tempat_lahir'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Lahir <span class="required-star">*</span></label>
                                    <input type="date" class="form-control" name="tgl_lahir" value="<?= htmlspecialchars($_POST['tgl_lahir'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No. WhatsApp Aktif <span class="required-star">*</span></label>
                                    <input type="text" class="form-control" name="no_hp_siswa" value="<?= htmlspecialchars($_POST['no_hp_siswa'] ?? '') ?>" placeholder="Contoh: 0857 xxx" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Alamat Lengkap Domisili <span class="required-star">*</span></label>
                                    <textarea class="form-control" name="alamat_siswa" rows="2" required><?= htmlspecialchars($_POST['alamat_siswa'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="section-divider h5 text-uppercase">B. Riwayat Pendidikan Dasar</div>
                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">Nama SD/MI Asal <span class="required-star">*</span></label>
                                    <input type="text" class="form-control" name="asal_sd" value="<?= htmlspecialchars($_POST['asal_sd'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nama SMP/MTs Asal <span class="required-star">*</span></label>
                                    <input type="text" class="form-control" name="asal_sekolah" value="<?= htmlspecialchars($_POST['asal_sekolah'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alamat SD/MI <span class="required-star">*</span></label>
                                    <textarea class="form-control" name="alamat_sd" rows="2" required><?= htmlspecialchars($_POST['alamat_sd'] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alamat SMP/MTs <span class="required-star">*</span></label>
                                    <textarea class="form-control" name="alamat_sekolah" rows="2" required><?= htmlspecialchars($_POST['alamat_sekolah'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="section-divider h5 text-uppercase">C. Data Orang Tua Kandung</div>
                            <div class="row g-4 mb-5">
                                <div class="col-md-6 border-end pe-md-4">
                                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-male me-1"></i> IDENTITAS AYAH</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Nama Ayah <span class="required-star">*</span></label>
                                        <input type="text" class="form-control" name="nama_ayah" value="<?= htmlspecialchars($_POST['nama_ayah'] ?? '') ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">NIK Ayah <span class="required-star">*</span></label>
                                        <input type="text" class="form-control" name="nik_ayah" value="<?= htmlspecialchars($_POST['nik_ayah'] ?? '') ?>" placeholder="16 digit angka" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Pekerjaan Ayah <span class="required-star">*</span></label>
                                        <input type="text" class="form-control" name="pekerjaan_ayah" value="<?= htmlspecialchars($_POST['pekerjaan_ayah'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6 ps-md-4">
                                    <h6 class="fw-bold text-danger mb-3"><i class="fas fa-female me-1"></i> IDENTITAS IBU</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Nama Ibu <span class="required-star">*</span></label>
                                        <input type="text" class="form-control" name="nama_ibu" value="<?= htmlspecialchars($_POST['nama_ibu'] ?? '') ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">NIK Ibu <span class="required-star">*</span></label>
                                        <input type="text" class="form-control" name="nik_ibu" value="<?= htmlspecialchars($_POST['nik_ibu'] ?? '') ?>" placeholder="16 digit angka" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Pekerjaan Ibu <span class="required-star">*</span></label>
                                        <input type="text" class="form-control" name="pekerjaan_ibu" value="<?= htmlspecialchars($_POST['pekerjaan_ibu'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="section-divider h5 text-uppercase">D. Unggah Berkas & Dokumen</div>
                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">Scan Kartu Keluarga (KK) <span class="required-star">*</span></label>
                                    <input type="file" class="form-control" name="kk_file" accept="image/*,application/pdf" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Scan KTP Orang Tua <span class="required-star">*</span></label>
                                    <input type="file" class="form-control" name="ktp_ortu_file" accept="image/*,application/pdf" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Scan Ijazah SD/MI <span class="required-star">*</span></label>
                                    <input type="file" class="form-control" name="ijazah_sd_file" accept="image/*,application/pdf" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Scan SKL SMP/MTs <span class="required-star">*</span></label>
                                    <input type="file" class="form-control" name="skl_file" accept="image/*,application/pdf" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pas Foto 3x4 (Terbaru) <span class="required-star">*</span></label>
                                    <input type="file" class="form-control" name="foto_file" accept="image/*" required>
                                </div>
                            </div>

                            <div class="section-divider h5 text-danger text-uppercase">E. Pilihan Jurusan & Informasi</div>
                            <div class="row g-4 mb-5">
                                <div class="col-md-12">
                                    <label class="form-label">Pilih Kompetensi Keahlian <span class="required-star">*</span></label>
                                    <div class="d-flex flex-column gap-2">
                                        <?php foreach ($jurusan_options as $jurusan): ?>
                                            <div class="form-check p-3 border rounded bg-white shadow-sm">
                                                <input class="form-check-input ms-1 me-3" type="radio" name="id_jurusan_pilihan" 
                                                       id="jr_<?= $jurusan['id_jurusan']; ?>"
                                                       value="<?= $jurusan['id_jurusan']; ?>" 
                                                       <?= (($_POST['id_jurusan_pilihan'] ?? '') == $jurusan['id_jurusan']) ? 'checked' : ''; ?> required>
                                                <label class="form-check-label fw-bold text-dark" for="jr_<?= $jurusan['id_jurusan']; ?>">
                                                    <?= htmlspecialchars($jurusan['nama_keahlian']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid pt-3">
                                <button type="submit" class="btn btn-primary btn-submit-form text-white shadow">
                                    <i class="fas fa-paper-plane me-2"></i> SELESAIKAN & KIRIM PENDAFTARAN LENGKAP
                                </button>
                                <p class="text-center text-muted mt-3 small">Data yang sudah dikirim akan diverifikasi oleh panitia SPMB.</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>