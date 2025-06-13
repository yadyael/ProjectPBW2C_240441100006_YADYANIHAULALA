<?php
session_start();
require 'config/db.php';

// Hitung jumlah pendaftar
$result_pendaftar = $conn->query("SELECT COUNT(*) AS total FROM pendaftaran");
$jumlah_pendaftar = $result_pendaftar->fetch_assoc()['total'];

// Hitung jumlah admin/petugas
$result_admin = $conn->query("SELECT COUNT(*) AS total FROM admin");
$jumlah_admin = $result_admin->fetch_assoc()['total'];

// Hitung jumlah darah berdasarkan golongan dan rhesus
$golongan_darah = ['A', 'B', 'AB', 'O'];
$rhesus = ['+', '-'];
$darah = [];

foreach ($golongan_darah as $gol) {
    foreach ($rhesus as $rh) {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pendaftaran WHERE gol_darah = ? AND rhesus = ?");
        $stmt->bind_param("ss", $gol, $rh);
        $stmt->execute();
        $res = $stmt->get_result();
        $darah["$gol$rh"] = $res->fetch_assoc()['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="assets/blood.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Setetes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f0ede0;
        }
    </style>
</head>
<body>
    <div class="flex flex-col md:flex-row min-h-screen">
        <aside class="w-full md:w-64 bg-[#918e80] p-5 text-white z-10 flex flex-col"> <!-- ini remake -->
            <div class="flex items-center gap-2 mb-6">
                <img src="assets/blood.png" alt="Logo" class="w-8 h-8">
                <h2 class="text-2xl font-bold font" id="main">Setetes</h2>
            </div>
            <!-- Menu utama -->
            <div class="flex-1 space-y-3">
                <a href="#main" class="block p-2 rounded bg-[#970c10]">Dashboard</a>
                <a href="menus/pendaftaran.php" class="block p-2 rounded hover:bg-[#970c10] transition">Pendaftaran</a>
                <a href="menus/jadwal.php" class="block p-2 rounded hover:bg-[#970c10] transition">Jadwal Mobil Unit</a>
            </div>
            <a href="auth/logout.php"
            class="block mt-4 bg-white text-[#970c10] font-semibold p-2 rounded shadow hover:bg-[#970c10] hover:text-white transition">Logout</a>
        </aside>
        <main class="flex-1 p-5 mt-4 md:mt-0"> <!-- ini remake -->
            <h1 class="text-3xl font-bold text-[#970c10] mb-6">Dashboard Admin</h1>

            <div class="bg-[#918e80] text-white p-6 rounded-lg flex flex-col md:flex-row justify-between items-center mb-8">
                <div>
                    <h2 class="text-2xl font-semibold">Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
                    <p class="text-sm text-white/80">Bersama Setetes, kita bantu lebih banyak nyawa.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-4 rounded shadow text-center">
                    <p class="text-gray-600">Jumlah Pendaftar</p>
                    <p class="text-2xl font-bold text-[#970c10]"><?= $jumlah_pendaftar ?></p>
                </div>
                <div class="bg-white p-4 rounded shadow text-center">
                    <p class="text-gray-600">Jumlah Petugas</p>
                    <p class="text-2xl font-bold text-[#970c10]"><?= $jumlah_admin ?></p>
                </div>
            </div>

            <h2 class="text-xl font-bold text-[#970c10] mb-4">Jumlah Darah berdasarkan Golongan</h2>
            <div class="w-full overflow-x-auto"> <!-- ini remake -->
                <table class="min-w-full bg-white border text-sm sm:text-base">
                    <thead>
                        <tr class="bg-[#970c10] text-white">
                            <th class="px-4 py-2 border">Golongan</th>
                            <th class="px-4 py-2 border">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($darah as $gol => $jumlah): ?>
                            <tr>
                                <td class="px-4 py-2 border text-center font-semibold"><?= $gol ?></td>
                                <td class="px-4 py-2 border text-center"><?= $jumlah ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
