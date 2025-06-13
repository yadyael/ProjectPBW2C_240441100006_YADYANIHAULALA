<?php
session_start();
require '../config/db.php';

$detail = null;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM pendaftaran WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $detail = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="../assets/blood.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pendaftar | Setetes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body style="background-color: #f0ede0;">
    <div class="flex  flex-col md:flex-row min-h-screen">
        <aside class="w-full md:w-64 bg-[#918e80] p-5 text-white z-10 flex flex-col">
            <div class="flex items-center gap-2 mb-6">
                <img src="../assets/blood.png" alt="Logo" class="w-8 h-8">
                <h2 class="text-2xl font-bold font" id="main">Setetes</h2>
            </div>

            <!-- Menu utama -->
            <div class="flex-1 space-y-3">
                <a href="#main" class="block p-2 rounded bg-[#970c10] transition">Data Pendaftar</a>
                <a href="pendaftaran.php" class="block p-2 rounded hover:bg-[#970c10] transition">Kembali</a>
            </div>
            <a href="../auth/logout.php"
            class="block mt-4 bg-white text-[#970c10] font-semibold p-2 rounded shadow hover:bg-[#970c10] hover:text-white transition">Logout</a>
        </aside>

        <main class="flex-1 p-5 mt-4 md:mt-0" id="main">
            <?php if ($detail): ?>
            <h1 class="text-3xl font-bold text-[#970c10] mb-6 border-b pb-2">Detail Pendaftar: <?= htmlspecialchars($detail['nama']) ?></h1>
            
            <div class="overflow-x-auto">
                <table class="w-full bg-white border-gray-200 shadow-md">
                    <?php foreach ($detail as $key => $value): ?>
                        <tr class="border-b">
                            <td class="p-3 bg-[#970c10] font-semibold text-white text-lg capitalize w-1/3">
                                <?= str_replace("_", " ", $key) ?>
                            </td>
                            <td class="p-3 text-gray-900 text-lg">
                                <?= htmlspecialchars($value) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

        <?php else: ?>
            <p class="text-gray-600 text-lg">Tidak ada data untuk ditampilkan.</p>
        <?php endif; ?>

    
        </main>
    </div>
</body>
</html>
