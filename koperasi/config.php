<?php

$host = "ep-withered-resonance-atruyl3z-pooler.c-9.us-east-1.aws.neon.tech";
$port = "5432";
$dbname = "neondb";
$user = "neondb_owner";
$password = "npg_sL3tCwQK9dkm";

echo "<pre>";
echo "HOST : $host\n";
echo "DB   : $dbname\n";
echo "USER : $user\n";

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $user,
        $password
    );

    echo "Koneksi berhasil\n\n";

    $stmt = $pdo->query("
        SELECT tablename
        FROM pg_tables
        WHERE schemaname='public'
        ORDER BY tablename
    ");

    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));

} catch (PDOException $e) {
    echo $e->getMessage();
}

exit;