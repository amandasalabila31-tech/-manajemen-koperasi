<?php
include 'config.php';
include 'auth.php';

// Kalau sudah login, langsung lempar ke dashboard
if (isset($_SESSION['id_pengguna'])) {
    header('Location: index.php');
    exit;
}

$error = "";
$logout_message = isset($_GET['logout']) ? "Anda berhasil keluar dari sistem." : "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Username dan password wajib diisi.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = $1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<pre>";
var_dump($user);
echo "</pre>";

echo "Username yang diinput: " . $username . "<br>";
echo "Password yang diinput: " . $password;
exit;
        if ($user && $password === $user['password']) {
            session_regenerate_id(true);
            $_SESSION['id_pengguna']  = $user['id'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role']         = $user['role'];
            header('Location: index.php');
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreCoop — Masuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-sm">
            <div class="flex flex-col items-center mb-8">
                <div class="h-12 w-12 bg-indigo-500 rounded-2xl flex items-center justify-center font-bold text-white text-xl mb-3">C</div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">CoreCoop</h1>
                <p class="text-slate-500 text-sm mt-1">Masuk ke sistem manajemen koperasi</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">

                <?php if ($error): ?>
                    <div class="p-3 mb-5 text-sm text-rose-700 bg-rose-50 rounded-xl font-medium">
                        ✗ <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($logout_message): ?>
                    <div class="p-3 mb-5 text-sm text-emerald-700 bg-emerald-50 rounded-xl font-medium">
                        ✓ <?= htmlspecialchars($logout_message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Username</label>
                        <input type="text" name="username" required autofocus
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="mis. admin">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="••••••••">
                    </div>
                    <button type="submit" name="login"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 rounded-xl text-sm transition-all">
                        Masuk
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-slate-400 mt-6">CoreCoop — Sistem Akuntansi v1.0</p>
        </div>
    </div>
</body>
</html>