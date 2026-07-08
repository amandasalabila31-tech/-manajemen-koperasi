<?php
// auth.php — helper otentikasi & sesi
// Wajib di-include SETELAH config.php di setiap halaman yang butuh proteksi login.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Panggil di paling atas halaman yang wajib login.
 * Kalau belum login, langsung redirect ke login.php.
 */
function requireLogin() {
    if (!isset($_SESSION['id_pengguna'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Ambil data pengguna yang sedang login (dari session).
 */
function currentUser() {
    return [
        'id'       => $_SESSION['id_pengguna']  ?? null,
        'username' => $_SESSION['username']     ?? null,
        'nama'     => $_SESSION['nama_lengkap'] ?? null,
        'role'     => $_SESSION['role']         ?? null,
    ];
}
?>
