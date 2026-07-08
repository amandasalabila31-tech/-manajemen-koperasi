<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

// Catat waktu logout ke log_aktivitas_login (opsional, kalau tabelnya ada)
if (isset($_SESSION['id_user'])) {
    try {
        $stmt = $pdo->prepare("
            UPDATE log_aktivitas_login
            SET waktu_logout = CURRENT_TIMESTAMP
            WHERE id_log = (
                SELECT id_log FROM log_aktivitas_login
                WHERE id_user = ? AND waktu_logout IS NULL
                ORDER BY waktu_login DESC
                LIMIT 1
            )
        ");
        $stmt->execute([$_SESSION['id_user']]);
    } catch (Exception $e) {
        // Kalau tabel log belum ada / query gagal, abaikan saja supaya logout tetap jalan
    }
}

// Hapus semua data sesi
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header("Location: login.php");
exit;
?>
