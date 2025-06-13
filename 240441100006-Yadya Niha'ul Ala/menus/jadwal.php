<?php
session_start();
require '../config/db.php';

$id = $tanggal = $lokasi = $waktu = "";
$edit_mode = false;

// Handle simpan atau update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $lokasi = $_POST['lokasi'];
    $waktu = $_POST['waktu'];

    if (!empty($_POST['id'])) {
        // UPDATE
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE jadwal SET tanggal=?, lokasi=?, waktu=? WHERE id=?");
        $stmt->bind_param("sssi", $tanggal, $lokasi, $waktu, $id);
        $stmt->execute();
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO jadwal (tanggal, lokasi, waktu) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $tanggal, $lokasi, $waktu);
        $stmt->execute();
    }
    header("Location: jadwal.php");
    exit();
}

// Edit
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM jadwal WHERE id = $id");
    $data = $result->fetch_assoc();
    $tanggal = $data['tanggal'];
    $lokasi = $data['lokasi'];
    $waktu = $data['waktu'];
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM jadwal WHERE id = $id");
    header("Location: jadwal.php");
    exit();
}

$result = $conn->query("SELECT * FROM jadwal ORDER BY tanggal ASC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="../assets/blood.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Mobil Unit | Setetes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body style="background-color: #f0ede0;">
    <div class="flex flex-col md:flex-row min-h-screen">
        <aside class="w-full md:w-64 bg-[#918e80] p-5 text-white z-10 flex flex-col">
            <div class="flex items-center gap-2 mb-6">
                <img src="../assets/blood.png" alt="Logo" class="w-8 h-8">
                <h2 class="text-2xl font-bold font" id="main">Setetes</h2>
            </div>
            <!-- Menu utama -->
            <div class="flex-1 space-y-3">
                <a href="#main" class="block p-2 rounded bg-[#970c10] transition">Jadwal Mobil Unit</a>
                <a href="pendaftaran.php" class="block p-2 rounded hover:bg-[#970c10] transition">Kembali</a>
            </div>
            <a href="../auth/logout.php"
            class="block mt-4 bg-white text-[#970c10] font-semibold p-2 rounded shadow hover:bg-[#970c10] hover:text-white transition">Logout</a>
        </aside>
        <main class="flex-1 p-5 mt-4 md:mt-0">
            <!-- <h1 class="text-3xl font-bold text-[#970c10] mb-6">Jadwal Mobil Unit</h1> -->

            <h1 class="text-3xl font-bold text-[#970c10] mb-6"><?= $edit_mode ? 'Edit Jadwal' : 'Tambah Jadwal Baru' ?></h1>

            <form method="POST" class="bg-white p-6 rounded shadow mb-10">
                <input type="hidden" name="id" value="<?= $edit_mode ? $id : '' ?>">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium">Tanggal</label>
                    <input type="date" name="tanggal" value="<?= $tanggal ?>" required class="border p-2 rounded w-full">
                </div>
                <div>
                    <label class="block font-medium">Lokasi</label>
                    <input type="text" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>" required class="border p-2 rounded w-full">
                </div>
                <div>
                    <label class="block font-medium">Waktu</label>
                    <input type="time" name="waktu" value="<?= $waktu ?>" required class="border p-2 rounded w-full">
                </div>
                </div>
                <div class="mt-6">
                <button class="bg-[#970c10] text-white px-6 py-2 rounded"><?= $edit_mode ? 'Simpan Perubahan' : 'Simpan' ?></button>
                <?php if ($edit_mode): ?>
                    <a href="jadwal.php" class="ml-4 text-blue-600">Batal</a>
                <?php endif; ?>
                </div>
            </form>

            <h2 class="text-2xl font-bold text-[#970c10] mb-4">Daftar Jadwal</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border">
                <thead class="bg-[#970c10] text-white">
                    <tr>
                    <th class="px-4 py-2 border">Tanggal</th>
                    <th class="px-4 py-2 border">Lokasi</th>
                    <th class="px-4 py-2 border">Waktu</th>
                    <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                    <td class="px-4 py-2 border text-center"><?= $row['tanggal'] ?></td>
                    <td class="px-4 py-2 border text-center"><?= htmlspecialchars($row['lokasi']) ?></td>
                    <td class="px-4 py-2 border text-center"><?= $row['waktu'] ?></td>
                    <td class="px-4 py-2 border text-center">
                        <a href="jadwal.php?edit=<?= $row['id'] ?>" class="bg-blue-500 text-white px-3 py-1 rounded">Edit</a>
                        <a href="jadwal.php?delete=<?= $row['id'] ?>" class="bg-red-600 text-white px-3 py-1 rounded"
                        onclick="return confirm('Yakin ingin hapus?')">Hapus</a>
                    </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
