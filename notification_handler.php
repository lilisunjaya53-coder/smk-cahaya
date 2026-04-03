<?php
require_once 'config.php';
require_once 'midtrans_config.php';

$conn = connectDB();

try {
    // 1. Tangkap notifikasi dari Midtrans
    $notif = new \Midtrans\Notification();

    $transaction = $notif->transaction_status;
    $type = $notif->payment_type;
    $order_id = $notif->order_id;
    $fraud = $notif->fraud_status;

    // Order ID lo biasanya formatnya: ORDER-IDPENDAFTAR-TIMESTAMP
    // Kita pecah buat ambil ID Pendaftarnya bray
    $order_parts = explode('-', $order_id);
    $id_pendaftar = (int)$order_parts[1];

    if ($transaction == 'settlement') {
        // PEMBAYARAN BERHASIL/LUNAS
        $sql = "UPDATE pendaftar SET status_pembayaran = 'Lunas' WHERE id_pendaftar = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_pendaftar);
        $stmt->execute();
        
    } else if ($transaction == 'pending') {
        // PEMBAYARAN MENUNGGU PEMBAYARAN
        $sql = "UPDATE pendaftar SET status_pembayaran = 'Menunggu Verifikasi' WHERE id_pendaftar = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_pendaftar);
        $stmt->execute();

    } else if ($transaction == 'expire' || $transaction == 'cancel') {
        // PEMBAYARAN GAGAL/EXPIRED
        $sql = "UPDATE pendaftar SET status_pembayaran = 'Belum Bayar' WHERE id_pendaftar = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_pendaftar);
        $stmt->execute();
    }

    echo "OK"; // Kirim respon ke Midtrans bray

} catch (Exception $e) {
    die($e->getMessage());
}

$conn->close();