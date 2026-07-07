<?php
echo "<pre>";

echo "PGHOST = " . ($_ENV['PGHOST'] ?? 'tidak ada') . "\n";
echo "PGDATABASE = " . ($_ENV['PGDATABASE'] ?? 'tidak ada') . "\n";
echo "PGUSER = " . ($_ENV['PGUSER'] ?? 'tidak ada') . "\n";

exit;