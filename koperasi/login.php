<?php
// Jalankan file ini SEKALI untuk menghasilkan hash password yang valid.
// Cara pakai:
//   1. Taruh file ini di folder yang sama dengan login.php di server (Railway) ATAU jalankan di laptop kalau PHP terpasang.
//   2. Akses lewat browser, contoh: https://aplikasi-manajemen-koperasi-production.up.railway.app/generate_hash.php
//   3. Copy hasil hash yang muncul.
//   4. Pakai hash itu di query UPDATE (LANGKAH 2).
//   5. PENTING: hapus file ini dari server setelah selesai dipakai, supaya tidak jadi celah keamanan.

$password_asli = 'admin123'; // ganti sesuai password yang mau dipakai

$hash = password_hash($password_asli, PASSWORD_BCRYPT);

echo "Password asli : " . htmlspecialchars($password_asli) . "<br>";
echo "Hash bcrypt   : " . htmlspecialchars($hash) . "<br><br>";
echo "Copy hash di atas, lalu pakai di query UPDATE berikut:<br><br>";
echo "<pre>";
echo "UPDATE public.users\n";
echo "SET password_hash = '" . htmlspecialchars($hash) . "'\n";
echo "WHERE username = 'admin';";
echo "</pre>";
?>