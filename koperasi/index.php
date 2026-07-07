<?php
include 'config.php';

echo "<h3>Database: " . $pdo->query("SELECT current_database()")->fetchColumn() . "</h3>";
echo "<h3>Schema: " . $pdo->query("SELECT current_schema()")->fetchColumn() . "</h3>";

$stmt = $pdo->query("
SELECT schemaname, tablename
FROM pg_tables
ORDER BY schemaname, tablename
");

echo "<pre>";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "</pre>";

exit;