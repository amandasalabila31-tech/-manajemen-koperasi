<?php
// Script SEMENTARA untuk debug login. Upload ke server, akses lewat browser,
// lalu HAPUS lagi setelah selesai (karena menampilkan info sensitif).

include 'config.php';

$username = 'admin';
$password = 'admin123';

echo "<h3>1. Cek koneksi database</h3>";
echo "Koneksi PDO: " . ($pdo ? "OK" : "GAGAL") . "<br><br>";

echo "<h3>2. Query user</h3>";
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = $1");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "❌ User TIDAK ditemukan dengan username '$username'.<br>";
    exit;
}

echo "✅ User ditemukan. Isi datanya:<br>";
echo "<pre>";
print_r($user);
echo "</pre>";

echo "<h3>3. Cek kolom password_hash</h3>";
if (!isset($user['password_hash']) || $user['password_hash'] === null) {
    echo "❌ Kolom 'password_hash' KOSONG / NULL.<br>";
} else {
    echo "✅ password_hash ada, panjang: " . strlen($user['password_hash']) . " karakter<br>";
    echo "Isi: " . htmlspecialchars($user['password_hash']) . "<br>";
}

echo "<h3>4. Cek password_verify()</h3>";
$hasil = password_verify($password, $user['password_hash'] ?? '');
echo "Password yang dicoba: '$password'<br>";
echo "Hasil password_verify(): " . ($hasil ? "✅ TRUE (cocok)" : "❌ FALSE (tidak cocok)") . "<br>";

echo "<h3>5. Info versi PHP</h3>";
echo "PHP version: " . phpversion() . "<br>";
?>
