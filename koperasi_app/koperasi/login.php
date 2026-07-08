<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

// Kalau sudah login, langsung lempar ke dashboard
if (isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Username dan password wajib diisi.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'] ?? '')) {
            // Login berhasil
            session_regenerate_id(true);
            $_SESSION['id_user']       = $user['id'];
            $_SESSION['username']      = $user['username'];
            $_SESSION['nama_lengkap']  = $user['nama_lengkap'] ?? $user['username'];
            $_SESSION['role']          = $user['role'];

            // Catat log login (tabel log_aktivitas_login, opsional)
            try {
                $log = $pdo->prepare("INSERT INTO log_aktivitas_login (id_user, username, ip_address, user_agent, status) VALUES (?, ?, ?, ?, 'Berhasil')");
                $log->execute([$user['id'], $user['username'], $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null]);
            } catch (Exception $e) {
                // Kalau tabel log belum ada, abaikan saja supaya login tetap jalan
            }

            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah.";

            // Catat log gagal login (opsional)
            try {
                $log = $pdo->prepare("INSERT INTO log_aktivitas_login (id_user, username, status, keterangan) VALUES (?, ?, 'Gagal', 'Password atau username salah')");
                $log->execute([$user['id'] ?? null, $username]);
            } catch (Exception $e) {
                // abaikan
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CoreCoop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="h-12 w-12 bg-indigo-500 rounded-xl flex items-center justify-center font-bold text-white text-xl mx-auto mb-3">C</div>
            <h1 class="text-2xl font-bold text-slate-900">CoreCoop</h1>
            <p class="text-slate-500 text-sm mt-1">Masuk ke sistem manajemen koperasi</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <?php if ($error): ?>
                <div class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded-xl border border-red-100">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Username</label>
                    <input type="text" name="username" required autofocus
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-all">
                    Masuk
                </button>
            </form>
        </div>
    </div>

</body>
</html>