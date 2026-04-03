<?php
// generate_hash.php
$password_plain = 'LiliSunjaya'; // Tentukan password Kepsek di sini
$hashed_password = password_hash($password_plain, PASSWORD_DEFAULT);

echo "Password Plaintext: " . $password_plain . "\n";
echo "Hash BCrypt (Siap Disalin): **" . $hashed_password . "**\n";
echo "================================================================================\n";
echo "SALIN SELURUH STRING DI ATAS (TERMASUK \$2y\$...) DAN UPDATE DI TABEL USERS VIA PHPMYADMIN.\n";
