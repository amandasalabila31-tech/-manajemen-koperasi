<?php
session_start();

// Hapus semua data sesi
$_SESSION = [];

// Hapus cookie sesi juga (kalau browser pakai cookie untuk session)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan sesi sepenuhnya
session_destroy();

// Arahkan kembali ke halaman login
header("Location: login.php");
exit;
?>