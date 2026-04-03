<?php
// Konfigurasi Midtrans
// Pastikan SECURITY: Jangan pernah mengekspos Server Key di sisi client (JavaScript)

// GANTI DENGAN SERVER KEY ANDA (Dari Dashboard Midtrans)
// Ingat: Sandbox Server Key diawali dengan 'SB-Mid-server-'
define('MIDTRANS_SERVER_KEY', 'Mid-server-EQXQZ24GdbgWfSEO3KqEEXco'); 

// Setup Environment (true untuk Production, false untuk Sandbox)
define('MIDTRANS_IS_PRODUCTION', false);

// Helper Function untuk memanggil API Midtrans (Tanpa Composer)
function getSnapToken($params) {
    // 1. Set Endpoint URL
    $url = MIDTRANS_IS_PRODUCTION 
        ? 'https://app.midtrans.com/snap/v1/transactions' 
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

    // 2. Siapkan Header
    $server_key = MIDTRANS_SERVER_KEY;
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode($server_key . ':')
    ];

    // 3. Kirim Request menggunakan CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 4. Proses Respon
    if ($http_code != 201 && $http_code != 200) {
        return ['error' => true, 'message' => 'Midtrans API Error: ' . $http_code, 'details' => $response];
    }

    return json_decode($response, true);
}
?>
