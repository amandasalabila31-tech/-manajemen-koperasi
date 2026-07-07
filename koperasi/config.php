<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "ep-withered-resonance-atruyl3z-pooler.c-9.us-east-1.aws.neon.tech";
$port = "5432";
$dbname = "neondb";
$user = "neondb_owner";
$password = "npg_sL3tCwQK9dkm";

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $user,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
