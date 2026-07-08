<?php
// auth.php
// Fungsi ini WAJIB dipanggil di paling atas setiap halaman yang butuh login.
// Contoh pemakaian di index.php, anggota.php, dll:
//     require 'auth.php';
//     cek_login();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function cek_login() {
    if (!isset($_SESSION['id_user'])) {
        header("Location: login.php");
        exit;
    }
}

// Helper untuk ambil data user yang sedang login (dipakai di header/topbar)
function user_login() {
    return [
        'id'           => $_SESSION['id_user']       ?? null,
        'username'     => $_SESSION['username']      ?? null,
        'nama_lengkap' => $_SESSION['nama_lengkap']  ?? null,
        'role'         => $_SESSION['role']          ?? null,
    ];
}
?>
