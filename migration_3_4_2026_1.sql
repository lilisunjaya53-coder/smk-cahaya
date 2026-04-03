-- Create the new table for tracking verification and payment status
CREATE TABLE pendaftar_status (
    id_status INT AUTO_INCREMENT PRIMARY KEY,
    id_pendaftar INT NOT NULL,
    status_verifikasi VARCHAR(50) NOT NULL DEFAULT 'Menunggu Verifikasi',
    status_pembayaran ENUM('Belum Bayar', 'Menunggu Verifikasi', 'Lunas') DEFAULT 'Belum Bayar',
    midtrans_transaction_id VARCHAR(100) NULL,
    FOREIGN KEY (id_pendaftar) REFERENCES pendaftar(id_pendaftar) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Migrate existing data from pendaftar to the new table
INSERT INTO pendaftar_status (id_pendaftar, status_verifikasi, status_pembayaran, midtrans_transaction_id)
SELECT id_pendaftar, status_verifikasi, status_pembayaran, midtrans_transaction_id
FROM pendaftar;

-- Drop the separated columns from the original pendaftar table
ALTER TABLE pendaftar 
DROP COLUMN status_verifikasi,
DROP COLUMN status_pembayaran,
DROP COLUMN midtrans_transaction_id;
