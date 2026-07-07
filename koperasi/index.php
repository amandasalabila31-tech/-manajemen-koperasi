<?php
include 'config.php';

echo "<pre>";

echo "Database : " . $pdo->query("SELECT current_database()")->fetchColumn() . "\n";
echo "User     : " . $pdo->query("SELECT current_user")->fetchColumn() . "\n";

echo "\nDaftar tabel:\n";

$stmt = $pdo->query("
SELECT tablename
FROM pg_tables
WHERE schemaname='public'
ORDER BY tablename
");

print_r($stmt->fetchAll(PDO::FETCH_COLUMN));

echo "</pre>";
exit;