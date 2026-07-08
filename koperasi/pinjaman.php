<?php
include 'config.php';
include 'auth.php';
requireLogin();
$user = currentUser();
$message = "";

// Handle Pengajuan Pinjaman Baru
if (isset($_POST['ajukan_pinjaman'])) {
    $id_anggota = $_POST['id_anggota'];
    $jumlah_pinjaman = $_POST['jumlah_pinjaman'];
    $tenor_bulan = $_POST['tenor_bulan'];
    $bunga_pct = $_POST['bunga_pct'];
    $metode_bunga = $_POST['metode_bunga'] ?? 'flat';

    try {
        $stmt = $pdo->prepare("INSERT INTO pengajuan_pinjaman (id_anggota, jumlah_pinjaman, tenor_bulan, bunga_pct, metode_bunga, status_pengajuan) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$id_anggota, $jumlah_pinjaman, $tenor_bulan, $bunga_pct, $metode_bunga]);
        $message = "<div class='p-4 mb-4 text-sm text-emerald-700 bg-emerald-50 rounded-xl font-medium'>✓ Pengajuan kredit berhasil didaftarkan! Sila tunggu verifikasi.</div>";
    } catch (Exception $e) {
        $message = "<div class='p-4 mb-4 text-sm text-rose-700 bg-rose-50 rounded-xl font-medium'>✗ Gagal: " . $e->getMessage() . "</div>";
    }
}

// Handle Verifikasi (Setujui / Tolak) Pengajuan -> panggil stored procedure sp_setujui_pinjaman
if (isset($_POST['verifikasi_pinjaman'])) {
    $id_pinjaman = $_POST['id_pinjaman'];
    $keputusan = $_POST['keputusan']; // 'Disetujui' atau 'Ditolak'

    try {
        $stmt = $pdo->prepare("CALL sp_setujui_pinjaman(?, ?)");
        $stmt->execute([$id_pinjaman, $keputusan]);
        $message = "<div class='p-4 mb-4 text-sm text-emerald-700 bg-emerald-50 rounded-xl font-medium'>✓ Pengajuan #{$id_pinjaman} berhasil di-{$keputusan}. Pencairan & jurnal otomatis sudah diproses.</div>";
    } catch (Exception $e) {
        $message = "<div class='p-4 mb-4 text-sm text-rose-700 bg-rose-50 rounded-xl font-medium'>✗ Verifikasi Gagal: " . $e->getMessage() . "</div>";
    }
}

// Handle Input Angsuran -> trigger trg_angsuran_ai otomatis update sisa_pokok + jurnal
if (isset($_POST['bayar_angsuran'])) {
    $id_pinjaman_aktif = $_POST['id_pinjaman_aktif'];
    $angsuran_ke = $_POST['angsuran_ke'];
    $bayar_pokok = $_POST['bayar_pokok'];
    $bayar_bunga = $_POST['bayar_bunga'];
    $hari_keterlambatan = $_POST['hari_keterlambatan'] ?: 0;

    try {
        $stmt = $pdo->prepare("INSERT INTO angsuran (id_pinjaman_aktif, angsuran_ke, bayar_pokok, bayar_bunga, hari_keterlambatan) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id_pinjaman_aktif, $angsuran_ke, $bayar_pokok, $bayar_bunga, $hari_keterlambatan]);
        $message = "<div class='p-4 mb-4 text-sm text-emerald-700 bg-emerald-50 rounded-xl font-medium'>✓ Angsuran ke-{$angsuran_ke} berhasil dicatat. Sisa pokok & jurnal ter-update otomatis.</div>";
    } catch (Exception $e) {
        $message = "<div class='p-4 mb-4 text-sm text-rose-700 bg-rose-50 rounded-xl font-medium'>✗ Gagal Catat Angsuran: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreCoop — Sistem Pinjaman</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-800 font-['Inter']">
    <div class="flex min-h-screen">
        <!-- SIDEBAR -->
        <aside class="w-64 bg-slate-900 text-white p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-8">
                    <div class="h-8 w-8 bg-indigo-500 rounded-lg flex items-center justify-center font-bold">C</div>
                    <span class="text-xl font-bold tracking-tight">CoreCoop</span>
                </div>
                <nav class="space-y-2">
                    <a href="index.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">Dashboard</a>
                    <a href="simpanan.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">Simpanan</a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 bg-indigo-600 text-white rounded-xl font-medium transition-all">Pinjaman</a>
                    <a href="akuntansi.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">Jurnal & Keuangan</a>
                </nav>
            </div>
            <div>
                <div class="text-xs text-slate-500 border-t border-slate-800 pt-4 mb-4">
                    <?= htmlspecialchars($user['nama'] ?? $user['username']) ?>
                </div>
                <a href="logout.php" class="block text-center p-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg text-sm transition shadow-md">
                    🚪 Keluar Sistem
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-10">
            <header class="mb-10">
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Kredit & Simulasi Pinjaman</h1>
                <p class="text-slate-500 text-sm mt-1">Proses kredit multiguna, perhitungan angsuran otomatis, dan status outstanding plafon.</p>
            </header>

            <?= $message ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- FORM PENGAJUAN & SIMULASI -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 h-fit">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Pengajuan Kredit</h3>
                    <form action="" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Anggota Pemohon</label>
                            <select name="id_anggota" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:outline-indigo-600" required>
                                <?php
                                $ang_stmt = $pdo->query("SELECT id_anggota, nama FROM anggota");
                                while($ang = $ang_stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$ang['id_anggota']}'>ID: {$ang['id_anggota']} - {$ang['nama']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Plafon Pinjaman (Rp)</label>
                            <input type="number" id="jumlah_pinjaman" name="jumlah_pinjaman" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:outline-indigo-600" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tenor (Bulan)</label>
                                <input type="number" id="tenor_bulan" name="tenor_bulan" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:outline-indigo-600" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Bunga per Tahun (%)</label>
                                <input type="number" step="0.01" id="bunga_pct" name="bunga_pct" value="12.00" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:outline-indigo-600" required>
                            </div>
                        </div>

                        <!-- LIVE PREVIEW SIMULASI (Bonus Feature) -->
                        <div id="simulasi_box" class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100 hidden">
                            <span class="text-xs font-bold text-indigo-800 uppercase tracking-wide block mb-2">Estimasi Cicilan (Flat)</span>
                            <p class="text-sm text-slate-600">Cicilan Pokok: <span id="lbl_pokok" class="font-semibold text-slate-900"></span></p>
                            <p class="text-sm text-slate-600">Cicilan Bunga: <span id="lbl_bunga" class="font-semibold text-slate-900"></span></p>
                            <div class="border-t border-indigo-100 my-2 pt-2">
                                <p class="text-base font-bold text-indigo-700">Total / Bulan: <span id="lbl_total"></span></p>
                            </div>
                        </div>

                        <button type="submit" name="ajukan_pinjaman" class="w-full bg-indigo-600 text-white font-medium p-3 rounded-xl hover:bg-indigo-700 transition-all text-sm shadow-sm">
                            Kirim Pengajuan Kredit
                        </button>
                    </form>
                </div>

                <!-- DAFTAR PENGAJUAN AKTIF -->
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Log Pengajuan & Status Outstanding</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                    <th class="pb-3">ID</th>
                                    <th class="pb-3">Nama</th>
                                    <th class="pb-3">Plafon</th>
                                    <th class="pb-3">Tenor</th>
                                    <th class="pb-3">Bunga</th>
                                    <th class="pb-3 text-center">Status</th>
                                    <th class="pb-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm text-slate-700">
                                <?php
                                $pinj_stmt = $pdo->query("SELECT p.*, a.nama FROM pengajuan_pinjaman p JOIN anggota a ON p.id_anggota = a.id_anggota ORDER BY p.id_pinjaman DESC");
                                while($row = $pinj_stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $status_color = $row['status_pengajuan'] == 'Disetujui' ? 'bg-emerald-50 text-emerald-700' : ($row['status_pengajuan'] == 'Ditolak' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700');
                                    echo "<tr>";
                                    echo "<td class='py-4 font-mono text-slate-400'>#{$row['id_pinjaman']}</td>";
                                    echo "<td class='py-4 font-medium text-slate-900'>{$row['nama']}</td>";
                                    echo "<td class='py-4 font-semibold'>Rp " . number_format($row['jumlah_pinjaman'], 0, ',', '.') . "</td>";
                                    echo "<td class='py-4'>{$row['tenor_bulan']} bln</td>";
                                    echo "<td class='py-4'>{$row['bunga_pct']}%</td>";
                                    echo "<td class='py-4 text-center'><span class='px-3 py-1 rounded-full font-medium text-xs $status_color'>{$row['status_pengajuan']}</span></td>";
                                    echo "<td class='py-4 text-center'>";
                                    if ($row['status_pengajuan'] == 'Pending') {
                                        echo "<form method='POST' class='inline-flex gap-1'>";
                                        echo "<input type='hidden' name='id_pinjaman' value='{$row['id_pinjaman']}'>";
                                        echo "<button type='submit' name='verifikasi_pinjaman' value='1' onclick=\"this.form.keputusan.value='Disetujui'\" class='px-2 py-1 text-xs font-semibold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700'>Setuju</button>";
                                        echo "<button type='submit' name='verifikasi_pinjaman' value='1' onclick=\"this.form.keputusan.value='Ditolak'\" class='px-2 py-1 text-xs font-semibold bg-rose-600 text-white rounded-lg hover:bg-rose-700'>Tolak</button>";
                                        echo "<input type='hidden' name='keputusan' value='Disetujui'>";
                                        echo "</form>";
                                    } else {
                                        echo "<span class='text-slate-300 text-xs'>—</span>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
                <!-- FORM INPUT ANGSURAN -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 h-fit">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Input Pembayaran Angsuran</h3>
                    <form action="" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pinjaman Aktif</label>
                            <select name="id_pinjaman_aktif" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:outline-indigo-600" required>
                                <?php
                                $aktif_stmt = $pdo->query("SELECT pa.id_pinjaman_aktif, a.nama, pa.sisa_pokok FROM pinjaman_aktif pa JOIN pengajuan_pinjaman pp ON pa.id_pinjaman_aktif = pp.id_pinjaman JOIN anggota a ON pp.id_anggota = a.id_anggota WHERE pa.sisa_pokok > 0");
                                while($ak = $aktif_stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$ak['id_pinjaman_aktif']}'>#{$ak['id_pinjaman_aktif']} - {$ak['nama']} (Sisa: Rp " . number_format($ak['sisa_pokok'], 0, ',', '.') . ")</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Angsuran Ke-</label>
                            <input type="number" name="angsuran_ke" min="1" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:outline-indigo-600" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Bayar Pokok (Rp)</label>
                                <input type="number" step="0.01" name="bayar_pokok" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:outline-indigo-600" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Bayar Bunga (Rp)</label>
                                <input type="number" step="0.01" name="bayar_bunga" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:outline-indigo-600" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Hari Keterlambatan</label>
                            <input type="number" name="hari_keterlambatan" value="0" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm focus:outline-indigo-600">
                        </div>
                        <button type="submit" name="bayar_angsuran" class="w-full bg-indigo-600 text-white font-medium p-3 rounded-xl hover:bg-indigo-700 transition-all text-sm shadow-sm">
                            Catat Pembayaran Angsuran
                        </button>
                    </form>
                </div>

                <!-- RIWAYAT ANGSURAN -->
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Riwayat Angsuran Terbaru</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                    <th class="pb-3">Pinjaman</th>
                                    <th class="pb-3">Nama</th>
                                    <th class="pb-3 text-center">Angsuran Ke-</th>
                                    <th class="pb-3 text-right">Pokok</th>
                                    <th class="pb-3 text-right">Bunga</th>
                                    <th class="pb-3 text-center">Telat (hari)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm text-slate-700">
                                <?php
                                $angs_stmt = $pdo->query("SELECT ang.*, a.nama FROM angsuran ang JOIN pinjaman_aktif pa ON ang.id_pinjaman_aktif = pa.id_pinjaman_aktif JOIN pengajuan_pinjaman pp ON pa.id_pinjaman_aktif = pp.id_pinjaman JOIN anggota a ON pp.id_anggota = a.id_anggota ORDER BY ang.tanggal_bayar DESC LIMIT 10");
                                while($row = $angs_stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $telat_color = $row['hari_keterlambatan'] > 0 ? 'text-rose-600 font-semibold' : 'text-slate-400';
                                    echo "<tr>";
                                    echo "<td class='py-4 font-mono text-slate-400'>#{$row['id_pinjaman_aktif']}</td>";
                                    echo "<td class='py-4 font-medium text-slate-900'>{$row['nama']}</td>";
                                    echo "<td class='py-4 text-center'>{$row['angsuran_ke']}</td>";
                                    echo "<td class='py-4 text-right'>Rp " . number_format($row['bayar_pokok'], 0, ',', '.') . "</td>";
                                    echo "<td class='py-4 text-right'>Rp " . number_format($row['bayar_bunga'], 0, ',', '.') . "</td>";
                                    echo "<td class='py-4 text-center $telat_color'>{$row['hari_keterlambatan']}</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- JS Untuk Hitung Simulasi Tanpa Reload -->
    <script>
        const inputPlafon = document.getElementById('jumlah_pinjaman');
        const inputTenor = document.getElementById('tenor_bulan');
        const inputBunga = document.getElementById('bunga_pct');
        const boxSimulasi = document.getElementById('simulasi_box');

        function hitungSimulasi() {
            const plafon = parseFloat(inputPlafon.value) || 0;
            const tenor = parseInt(inputTenor.value) || 0;
            const bungaThn = parseFloat(inputBunga.value) || 0;

            if (plafon > 0 && tenor > 0) {
                boxSimulasi.classList.remove('hidden');
                
                const pokokPerBulan = plafon / tenor;
                const bungaPerBulan = (plafon * (bungaThn / 100)) / 12;
                const totalPerBulan = pokokPerBulan + bungaPerBulan;

                document.getElementById('lbl_pokok').innerText = "Rp " + Math.round(pokokPerBulan).toLocaleString('id-ID');
                document.getElementById('lbl_bunga').innerText = "Rp " + Math.round(bungaPerBulan).toLocaleString('id-ID');
                document.getElementById('lbl_total').innerText = "Rp " + Math.round(totalPerBulan).toLocaleString('id-ID');
            } else {
                boxSimulasi.classList.add('hidden');
            }
        }

        inputPlafon.addEventListener('input', hitungSimulasi);
        inputTenor.addEventListener('input', hitungSimulasi);
        inputBunga.addEventListener('input', hitungSimulasi);
    </script>
</body>
</html>