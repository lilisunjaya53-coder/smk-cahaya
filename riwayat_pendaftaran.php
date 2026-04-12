<?php
require_once 'config.php';

// Keamanan & Otorisasi
checkAuth();
if (!isSiswa()) {
    header("Location: dashboard.php");
    exit;
}

$conn = connectDB();
$currentUser = getCurrentUser();
$id_user = $currentUser['id_user'];
$is_filled = !is_null($currentUser['id_pendaftar']);
$read_only = true;
$pageTitle = "Riwayat Pendaftaran";
$update_message = '';

if (!$is_filled) {
    header("Location: dashboard.php");
    exit;
}

$student_data = getStudentFullData($conn, $id_user);
$status_verifikasi = $student_data['status_verifikasi'] ?? 'Menunggu Pengumuman';

$display_status = $status_verifikasi;
if ($status_verifikasi == 'Diverifikasi' || $status_verifikasi == 'Terverifikasi' || $status_verifikasi == 'Disetujui') {
    $display_status = "Diterima";
} elseif ($status_verifikasi == 'Menunggu Verifikasi') {
    $display_status = "Sedang Diproses";
}

$allowed_to_edit = in_array($status_verifikasi, [
    'Menunggu Verifikasi',
    'Menunggu Verifikasi (Revisi)'
]);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update') {
    if (!$allowed_to_edit) {
        $update_message = "<div class='alert alert-danger border-0 shadow-sm rounded-4'>Gagal: Data sudah diterima dan tidak dapat diubah lagi.</div>";
    } else {
        // Ambil data lengkap sesuai pendaftar.sql
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
        $asal_smp = trim($_POST['asal_smp'] ?? '');
        $alamat_smp = trim($_POST['alamat_smp'] ?? '');
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
        $id_jurusan_pilihan = $_POST['id_jurusan_pilihan'] ?? '';
        // $rekomendasi = trim($_POST['rekomendasi'] ?? '');

        try {
            $sql_update_pendaftar = "UPDATE pendaftar SET 
                            nama_lengkap = ?, nama_panggilan = ?, nik_siswa = ?, jenis_kelamin = ?, tempat_lahir = ?, tgl_lahir = ?, 
                            agama = ?, status_yatim = ?, no_hp_siswa = ?, alamat_siswa = ?, id_jurusan_pilihan = ? 
                            WHERE id_pendaftar = ?";

            $stmt = $conn->prepare($sql_update_pendaftar);
            $stmt->bind_param(
                "ssssssssssis",
                $nama_lengkap,
                $nama_panggilan,
                $nik_siswa,
                $jenis_kelamin,
                $tempat_lahir,
                $tgl_lahir,
                $agama,
                $status_yatim,
                $no_hp_siswa,
                $alamat_siswa,
                $id_jurusan_pilihan,
                $student_data['id_pendaftar']
            );

            if (!$stmt->execute()) {
                throw new Exception("Update Pendaftar Gagal: " . $stmt->error);
            }
            $stmt->close();

            $sql_update_ortu = "UPDATE pendaftar_ortu SET nama_ayah = ?, nik_ayah = ?, pekerjaan_ayah = ?, nama_ibu = ?, nik_ibu = ?, pekerjaan_ibu = ?, nama_wali = ?, hubungan_wali = ? WHERE id_pendaftar = ?";
            $stmt_ortu = $conn->prepare($sql_update_ortu);
            $stmt_ortu->bind_param("ssssssssi", $nama_ayah, $nik_ayah, $pekerjaan_ayah, $nama_ibu, $nik_ibu, $pekerjaan_ibu, $nama_wali, $hubungan_wali, $student_data['id_pendaftar']);
            if (!$stmt_ortu->execute()) {
                throw new Exception("Update Ortu Gagal: " . $stmt_ortu->error);
            }
            $stmt_ortu->close();

            $sql_update_pendidikan = "UPDATE pendaftar_pendidikan SET asal_sd = ?, alamat_sd = ?, asal_smp = ?, alamat_smp = ? WHERE id_pendaftar = ?";
            $stmt_pend = $conn->prepare($sql_update_pendidikan);
            $stmt_pend->bind_param("ssssi", $asal_sd, $alamat_sd, $asal_smp, $alamat_smp, $student_data['id_pendaftar']);
            if (!$stmt_pend->execute()) {
                throw new Exception("Update Pendidikan Gagal: " . $stmt_pend->error);
            }
            $stmt_pend->close();

            $sql_update_status = "UPDATE pendaftar_status SET status_verifikasi = 'Menunggu Verifikasi (Revisi)' WHERE id_pendaftar = ?";
            $stmt_status = $conn->prepare($sql_update_status);
            $stmt_status->bind_param("i", $student_data['id_pendaftar']);
            $stmt_status->execute();
            $stmt_status->close();
            header("Location: riwayat_pendaftaran.php?status=updated");
            exit;
        } catch (Exception $e) {
            $update_message = "<div class='alert alert-danger border-0 shadow-sm rounded-4'>Terjadi Kesalahan: " . $e->getMessage() . "</div>";
        }
    }
}

if ($allowed_to_edit && isset($_GET['action']) && $_GET['action'] == 'edit') {
    $read_only = false;
    $pageTitle = "Edit Data Pendaftaran";
}

if (isset($_GET['status']) && $_GET['status'] == 'updated') {
    $update_message = "<div class='alert alert-success border-0 shadow-sm rounded-4'>Data berhasil diperbarui! Mohon tunggu proses tinjauan kembali.</div>";
}

$jurusan_options = [];
$result_jurusan = $conn->query("SELECT id_jurusan, nama_keahlian FROM jurusan ORDER BY nama_keahlian");
if ($result_jurusan) {
    $jurusan_options = $result_jurusan->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
include 'includes/header.php';
?>

<style>
    :root {
        --primary-color: #4e73df;
        --success-color: #1cc88a;
        --dark-bg: #f8f9fc;
    }

    .content-area {
        background-color: var(--dark-bg);
        padding: 40px 20px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .form-header-title {
        font-weight: 800;
        color: #2e3750;
        text-transform: uppercase;
        position: relative;
        display: inline-block;
        margin-bottom: 30px;
    }

    .form-header-title::after {
        content: '';
        position: absolute;
        width: 60%;
        height: 4px;
        background: var(--primary-color);
        bottom: -10px;
        left: 0;
        border-radius: 10px;
    }

    .registration-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }

    .section-divider {
        border-left: 5px solid var(--primary-color);
        padding-left: 15px;
        margin-bottom: 25px;
        font-weight: 700;
        color: var(--primary-color);
    }

    .form-label {
        font-weight: 700;
        color: #4e73df;
        font-size: 0.8rem;
        text-transform: uppercase;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 12px;
        border: 1px solid #d1d3e2;
        background-color: #fff;
    }

    .status-badge {
        font-size: 0.9rem;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 700;
    }

    .btn-save {
        background: linear-gradient(180deg, #1cc88a 10%, #13855c 100%);
        border: none;
        padding: 15px;
        font-weight: 700;
        border-radius: 10px;
        width: 100%;
        color: white;
    }

    .btn-edit-trigger {
        background-color: #f6c23e;
        border: none;
        color: white;
        font-weight: 700;
        border-radius: 8px;
        padding: 8px 16px;
    }

    .doc-link {
        display: inline-flex;
        align-items: center;
        padding: 10px 15px;
        background: #fff;
        border: 1px solid #d1d3e2;
        border-radius: 10px;
        text-decoration: none;
        color: #4e73df;
        font-weight: 600;
        transition: 0.2s;
    }
</style>

<?php include 'includes/sidebar_siswa.php'; ?>

<main class="content-area">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                <h2 class="form-header-title"><?php echo $pageTitle; ?></h2>
                <?php echo $update_message; ?>

                <div class="alert alert-white shadow-sm border rounded-4 d-flex justify-content-between align-items-center mb-4 p-3 bg-white">
                    <div>
                        <span class="text-muted small d-block mb-1 text-uppercase fw-bold">Status Verifikasi:</span>
                        <span class="status-badge <?php echo ($display_status == 'Diterima') ? 'bg-success text-white' : 'bg-primary text-white'; ?>">
                            <i class="fas <?php echo ($display_status == 'Diterima') ? 'fa-check-circle' : 'fa-clock'; ?> me-2"></i>
                            <?php echo htmlspecialchars($display_status); ?>
                        </span>
                    </div>
                    <?php if ($allowed_to_edit && $read_only): ?>
                        <a href="riwayat_pendaftaran.php?action=edit" class="btn btn-edit-trigger shadow-sm"><i class="fas fa-edit me-1"></i> Perbaiki Data</a>
                    <?php endif; ?>
                </div>

                <div class="card registration-card mb-5">
                    <div class="card-body p-4 p-md-5">
                        <form action="riwayat_pendaftaran.php" method="post">
                            <input type="hidden" name="action" value="update">

                            <div class="section-divider">A. IDENTITAS CALON PESERTA DIDIK</div>
                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">NIK Siswa</label>
                                    <input type="text" class="form-control" name="nik_siswa" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['nik_siswa'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NISN (Permanen)</label>
                                    <input type="text" class="form-control" name="nisn" readonly value="<?php echo htmlspecialchars($student_data['nisn'] ?? ''); ?>">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="nama_lengkap" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['nama_lengkap'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nama Panggilan</label>
                                    <input type="text" class="form-control" name="nama_panggilan" <?php echo $read_only ? 'readonly' : ''; ?> value="<?php echo htmlspecialchars($student_data['nama_panggilan'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Agama</label>
                                    <?php if ($read_only): ?>
                                        <input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($student_data['agama'] ?? '-'); ?>">
                                    <?php else: ?>
                                        <select class="form-select" name="agama" required>
                                            <?php $ag_list = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha'];
                                            foreach ($ag_list as $v) {
                                                $sel = ($student_data['agama'] == $v) ? 'selected' : '';
                                                echo "<option value='$v' $sel>$v</option>";
                                            } ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status Keluarga</label>
                                    <?php if ($read_only): ?>
                                        <input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($student_data['status_yatim'] ?? 'Tidak'); ?>">
                                    <?php else: ?>
                                        <select class="form-select" name="status_yatim" required>
                                            <?php $sy_list = ['Tidak' => 'Lengkap', 'Yatim' => 'Yatim', 'Piatu' => 'Piatu', 'Yatim Piatu' => 'Yatim Piatu'];
                                            foreach ($sy_list as $k => $v) {
                                                $sel = ($student_data['status_yatim'] == $k) ? 'selected' : '';
                                                echo "<option value='$k' $sel>$v</option>";
                                            } ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" name="jenis_kelamin" required>
                                        <option value="L" <?= (($student_data['jenis_kelamin'] ?? '') == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                        <option value="P" <?= (($student_data['jenis_kelamin'] ?? '') == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control" name="tempat_lahir" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['tempat_lahir'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" name="tgl_lahir" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['tgl_lahir'] ?? ''); ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">WhatsApp Siswa</label>
                                    <input type="text" class="form-control" name="no_hp_siswa" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['no_hp_siswa'] ?? ''); ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Alamat Lengkap Siswa</label>
                                    <textarea class="form-control" name="alamat_siswa" rows="2" <?php echo $read_only ? 'readonly' : 'required'; ?>><?php echo htmlspecialchars($student_data['alamat_siswa'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="section-divider">B. DATA ORANG TUA KANDUNG</div>
                            <div class="row g-4 mb-5">
                                <div class="col-md-6 border-end">
                                    <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-male me-1"></i> Data Ayah</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Nama Ayah</label>
                                        <input type="text" class="form-control" name="nama_ayah" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['nama_ayah'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">NIK Ayah</label>
                                        <input type="text" class="form-control" name="nik_ayah" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['nik_ayah'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Pekerjaan Ayah</label>
                                        <input type="text" class="form-control" name="pekerjaan_ayah" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['pekerjaan_ayah'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="col-md-6 ps-md-4">
                                    <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-female me-1"></i> Data Ibu</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Nama Ibu</label>
                                        <input type="text" class="form-control" name="nama_ibu" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['nama_ibu'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">NIK Ibu</label>
                                        <input type="text" class="form-control" name="nik_ibu" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['nik_ibu'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Pekerjaan Ibu</label>
                                        <input type="text" class="form-control" name="pekerjaan_ibu" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['pekerjaan_ibu'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="col-md-6 ps-md-4">
                                    <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-male me-1"></i> Data Wali</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Nama Wali</label>
                                        <input type="text" class="form-control" name="nama_wali" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['nama_wali'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Hubungan Wali</label>
                                        <input type="text" class="form-control" name="hubungan_wali" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['hubungan_wali'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="section-divider">C. RIWAYAT PENDIDIKAN</div>
                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">Asal SD/MI</label>
                                    <input type="text" class="form-control" name="asal_sd" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['asal_sd'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Asal SMP/MTs</label>
                                    <input type="text" class="form-control" name="asal_smp" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['asal_smp'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alamat SD/MI</label>
                                    <input type="text" class="form-control" name="alamat_sd" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['alamat_sd'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alamat SMP/MTs</label>
                                    <input type="text" class="form-control" name="alamat_smp" <?php echo $read_only ? 'readonly' : 'required'; ?> value="<?php echo htmlspecialchars($student_data['alamat_smp'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="section-divider">D. BERKAS DOKUMEN & LAINNYA</div>
                            <div class="row g-3 mb-5">
                                <div class="col-md-12 mb-3">
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php
                                        // DISESUAIKAN: Menambahkan SKL ke daftar berkas
                                        $files = [
                                            'kk_file' => 'KK',
                                            'ktp_ortu_file' => 'KTP',
                                            'ijazah_sd_file' => 'Ijazah SD',
                                            'skl_file' => 'SKL SMP/MTs',
                                            'foto_file' => 'Pas Foto'
                                        ];
                                        foreach ($files as $key => $lbl): if (!empty($student_data[$key])): ?>
                                                <a href="uploads/<?php echo $student_data[$key]; ?>" target="_blank" class="doc-link">
                                                    <i class="fas fa-file-alt me-2"></i> Lihat <?php echo $lbl; ?>
                                                </a>
                                        <?php endif;
                                        endforeach; ?>
                                    </div>
                                    <p class="small text-muted mt-2">Catatan: Berkas dokumen hanya bisa diubah melalui pendaftaran ulang jika ada kesalahan fatal.</p>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label text-danger">Pilihan Jurusan</label>
                                    <?php if ($read_only): ?>
                                        <div class="p-3 border rounded-3 bg-light fw-bold text-dark">
                                            <?php
                                            $sel = $student_data['id_jurusan_pilihan'] ?? '';
                                            foreach ($jurusan_options as $j) {
                                                if ($sel == $j['id_jurusan']) echo htmlspecialchars($j['nama_keahlian']);
                                            }
                                            ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="row g-2">
                                            <?php foreach ($jurusan_options as $jurusan): ?>
                                                <div class="col-md-6">
                                                    <div class="form-check p-3 border rounded bg-white shadow-sm">
                                                        <input class="form-check-input ms-1 me-3" type="radio" name="id_jurusan_pilihan"
                                                            value="<?php echo $jurusan['id_jurusan']; ?>"
                                                            <?php echo (($student_data['id_jurusan_pilihan'] ?? '') == $jurusan['id_jurusan']) ? 'checked' : ''; ?> required>
                                                        <label class="form-check-label fw-bold"><?php echo htmlspecialchars($jurusan['nama_keahlian']); ?></label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <!-- <div class="col-md-12 mt-3">
                                    <label class="form-label">Rekomendasi Dari</label>
                                    <input type="text" class="form-control" name="rekomendasi" <?php echo $read_only ? 'readonly' : ''; ?> value="<?php echo htmlspecialchars($student_data['rekomendasi'] ?? ''); ?>">
                                </div> -->
                            </div>

                            <?php if (!$read_only): ?>
                                <button type="submit" class="btn btn-save shadow"><i class="fas fa-save me-2"></i> Simpan Perubahan Data</button>
                                <a href="riwayat_pendaftaran.php" class="btn btn-link w-100 mt-2 text-muted text-decoration-none small">Batalkan Perubahan</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>