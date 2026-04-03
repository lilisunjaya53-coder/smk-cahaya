<?php
require_once 'config.php';

$conn = connectDB();

$sql = "ALTER TABLE pendaftar 
        ADD COLUMN status_pembayaran ENUM('Belum Bayar', 'Menunggu Verifikasi', 'Lunas') DEFAULT 'Belum Bayar',
        ADD COLUMN bukti_pembayaran VARCHAR(255) NULL,
        ADD COLUMN midtrans_transaction_id VARCHAR(100) NULL";

if ($conn->query($sql) === TRUE) {
    echo "Table updated successfully";
} else {
    echo "Error updating table: " . $conn->error;
}

$conn->close();
?>
