-- MIGRATION: Normalization Phase 2 (Parents & Documents Data)
-- Date: 2026-04-08

-- 1. Create `pendaftar_ortu` table to normalize Parent/Guardian data
CREATE TABLE pendaftar_ortu (
    id_ortu INT AUTO_INCREMENT PRIMARY KEY,
    id_pendaftar INT NOT NULL,
    nama_ayah VARCHAR(150),
    nik_ayah VARCHAR(16),
    pekerjaan_ayah VARCHAR(100),
    nama_ibu VARCHAR(150),
    nik_ibu VARCHAR(16),
    pekerjaan_ibu VARCHAR(100),
    nama_wali VARCHAR(150),
    hubungan_wali VARCHAR(50),
    FOREIGN KEY (id_pendaftar) REFERENCES pendaftar(id_pendaftar) ON DELETE CASCADE ON UPDATE CASCADE
);

-- 2. Create `pendaftar_berkas` table to normalize uploaded files data
CREATE TABLE pendaftar_berkas (
    id_berkas INT AUTO_INCREMENT PRIMARY KEY,
    id_pendaftar INT NOT NULL,
    kk_file VARCHAR(255),
    ktp_ortu_file VARCHAR(255),
    ijazah_sd_file VARCHAR(255),
    skl_file VARCHAR(255),
    foto_file VARCHAR(255),
    -- bukti_pembayaran VARCHAR(255),
    FOREIGN KEY (id_pendaftar) REFERENCES pendaftar(id_pendaftar) ON DELETE CASCADE ON UPDATE CASCADE
);

-- 3. Create `pendaftar_pendidikan` table to normalize previous education data
CREATE TABLE pendaftar_pendidikan (
    id_pendidikan INT AUTO_INCREMENT PRIMARY KEY,
    id_pendaftar INT NOT NULL,
    asal_sd VARCHAR(150),
    alamat_sd TEXT,
    asal_smp VARCHAR(150),   -- previously asal_sekolah
    alamat_smp TEXT,         -- previously alamat_sekolah
    FOREIGN KEY (id_pendaftar) REFERENCES pendaftar(id_pendaftar) ON DELETE CASCADE ON UPDATE CASCADE
);

-- 4. Migrate Existing Data
-- Migrate Parents Data (we use IGNORE or handle nulls if some columns don't exist in older rows)
INSERT INTO pendaftar_ortu (id_pendaftar, nama_ayah, nik_ayah, pekerjaan_ayah, nama_ibu, nik_ibu, pekerjaan_ibu, nama_wali, hubungan_wali)
SELECT id_pendaftar, nama_ayah, nik_ayah, pekerjaan_ayah, nama_ibu, nik_ibu, pekerjaan_ibu, nama_wali, hubungan_wali
FROM pendaftar;

-- Migrate Documents Data
INSERT INTO pendaftar_berkas (id_pendaftar, kk_file, ktp_ortu_file, ijazah_sd_file, skl_file, foto_file)
SELECT id_pendaftar, kk_file, ktp_ortu_file, ijazah_sd_file, skl_file, foto_file
FROM pendaftar;

-- Migrate Education Data
INSERT INTO pendaftar_pendidikan (id_pendaftar, asal_sd, alamat_sd, asal_smp, alamat_smp)
SELECT id_pendaftar, asal_sd, alamat_sd, asal_sekolah, alamat_sekolah
FROM pendaftar;

-- 5. Drop Separated Columns from `pendaftar`
ALTER TABLE pendaftar 
DROP COLUMN nama_ayah,
DROP COLUMN nik_ayah,
DROP COLUMN pekerjaan_ayah,
DROP COLUMN nama_ibu,
DROP COLUMN nik_ibu,
DROP COLUMN pekerjaan_ibu,
DROP COLUMN nama_wali,
DROP COLUMN hubungan_wali,
DROP COLUMN kk_file,
DROP COLUMN ktp_ortu_file,
DROP COLUMN ijazah_sd_file,
DROP COLUMN skl_file,
DROP COLUMN foto_file,
-- DROP COLUMN bukti_pembayaran,
DROP COLUMN asal_sd,
DROP COLUMN alamat_sd,
DROP COLUMN asal_sekolah,
DROP COLUMN alamat_sekolah;
