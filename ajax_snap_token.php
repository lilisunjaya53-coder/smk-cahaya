<?php
// ajax_snap_token.php
// Endpoint untuk Frontend mendapatkan Snap Token

header('Content-Type: application/json');
require_once 'config.php';
require_once 'midtrans_config.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => true, 'message' => 'Unauthorized']);
    exit;
}

// 2. Ambil Data User
$conn = connectDB();
$currentUser = getCurrentUser();
$id_user = $currentUser['id_user'];
$student_data = getStudentFullData($conn, $id_user);
$conn->close();

if (!$student_data) {
    echo json_encode(['error' => true, 'message' => 'Data siswa tidak ditemukan']);
    exit;
}

// 3. Cek apakah sudah pernah membuat order ID sebelumnya agar tidak duplikat di Midtrans
// Untuk sederhananya, kita generate Order ID baru setiap kali klik bayar jika belum Lunas.
// Idealnya simpan order_id di database.
if (empty($student_data['midtrans_transaction_id'])) {
    $order_id = "PPDB-" . date("YmdHis") . "-" . $student_data['id_pendaftar'];
} else {
    // Jika ingin reuse order ID lama (opsional), tapi lebih aman buat baru utk menghindari expire
    $order_id = "PPDB-" . date("YmdHis") . "-" . $student_data['id_pendaftar'];
}

// 4. Siapkan Parameter Transaksi Midtrans
$amount = 150000; // Biaya Pendaftaran Tetap

$transaction_details = [
    'order_id' => $order_id,
    'gross_amount' => $amount
];

$customer_details = [
    'first_name' => $student_data['nama_lengkap'],
    'email' => "siswa" . $student_data['id_pendaftar'] . "@smkcahaya.sch.id", // Email dummy jika tidak ada kolom email
    'phone' => $student_data['no_hp_siswa']
    // 'billing_address' => ...
];

$params = [
    'transaction_details' => $transaction_details,
    'customer_details' => $customer_details,
    // 'enabled_payments' => ['gopay', 'bank_transfer'], // Opsional: Batasi metode bayar
];

// 5. Minta Token ke Midtrans
$result = getSnapToken($params);

if (isset($result['token'])) {
    // Simpan order_id sementara ke session atau database jika perlu tracking ketat
    echo json_encode(['token' => $result['token'], 'order_id' => $order_id]);
} else {
    echo json_encode($result); // Return error details
}
?>
