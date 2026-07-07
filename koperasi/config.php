<?php
// 1. Membaca kredensial otomatis dari server Railway (Production)
// Jika tidak ditemukan (saat dijalankan di laptop), maka akan memakai alternatif di sebelah kanan (Localhost)
$host     = getenv('PGHOST')     ?: "ep-little-leaf-at8qu3dj-pooler.c-9.us-east-1.aws.neon.tech";
$port     = getenv('PGPORT')     ?: "5432";
$dbname   = getenv('PGDATABASE') ?: "neondb";
$user     = getenv('PGUSER')     ?: "neondb_owner";
$password = getenv('PGPASSWORD') ?: "npg_c1YhqZMnb8Nv"; // <-- Isi password laptop Anda di sini

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";

try {
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Koneksi Gagal: " . $e->getMessage());
}
?>