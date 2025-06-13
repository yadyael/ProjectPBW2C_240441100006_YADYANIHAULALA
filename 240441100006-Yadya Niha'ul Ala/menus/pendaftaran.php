<?php
session_start();
require '../config/db.php';

// Simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $nik = $_POST['nik'];
    $usia = (int)$_POST['usia'];
    $berat_badan = (float)$_POST['berat_badan'];
    $gol_darah = $_POST['gol_darah'];
    $rhesus = $_POST['rhesus'];
    $sudah_sarapan = $_POST['sudah_sarapan'];
    $haid_hamil_menyusui = $_POST['haid_hamil_menyusui'];
    $penyakit = $_POST['penyakit'];
    $konsumsi_alkohol = $_POST['konsumsi_alkohol'];
    $terakhir_donor = $_POST['terakhir_donor'];
    $jenis_identitas = $_POST['jenis_identitas'];
    $jadwal_id = (int)$_POST['jadwal_id'];

    // Validasi status
    $status = "Lolos";
    $alasan = [];

    if ($usia < 17) {
        $status = "Gagal";
        $alasan[] = "Usia kurang dari 17 tahun";
    }

    if ($berat_badan < 45) {
        $status = "Gagal";
        $alasan[] = "Berat badan kurang dari 45 kg";
    }

    if ($sudah_sarapan === "Tidak") {
        $status = "Gagal";
        $alasan[] = "Belum sarapan";
    }

    if ($haid_hamil_menyusui !== "Tidak") {
        $status = "Gagal";
        $alasan[] = "Dalam kondisi haid/hamil/menyusui";
    }

    $penyakit_berbahaya = ['Jantung', 'Hepatitis', 'HIV', 'Diabetes', 'TBC'];
    $penyakit_list = array_map('trim', explode(',', $penyakit));
    foreach ($penyakit_list as $p) {
        if (in_array($p, $penyakit_berbahaya)) {
            $status = "Gagal";
            $alasan[] = "Memiliki penyakit berbahaya: $p";
        }
    }

    if ($konsumsi_alkohol === "Pernah") {
        $status = "Gagal";
        $alasan[] = "Pernah mengonsumsi alkohol";
    }

    if ($terakhir_donor === "< 2 bulan") {
        $status = "Gagal";
        $alasan[] = "Terakhir donor kurang dari 2 bulan";
    }

    $alasan_gagal = ($status === "Gagal") ? implode("; ", $alasan) : null;

    $stmt = $conn->prepare("INSERT INTO pendaftaran
        (nama, nik, usia, berat_badan, gol_darah, rhesus, sudah_sarapan,
        haid_hamil_menyusui, penyakit, konsumsi_alkohol, terakhir_donor,
        jenis_identitas, jadwal_id, status, alasan_gagal)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssidsssssssssss", $nama, $nik, $usia, $berat_badan, $gol_darah,
        $rhesus, $sudah_sarapan, $haid_hamil_menyusui, $penyakit, $konsumsi_alkohol,
        $terakhir_donor, $jenis_identitas, $jadwal_id, $status, $alasan_gagal);

    $stmt->execute();
    $stmt->close();

    header("Location: pendaftaran.php#table");
    exit;
}
// Ambil jadwal urut
$today = date('Y-m-d');
$query_jadwal = $conn->prepare("SELECT * FROM jadwal WHERE tanggal >= ? ORDER BY tanggal ASC");
$query_jadwal->bind_param("s", $today);
$query_jadwal->execute();
$result_jadwal = $query_jadwal->get_result();

// Hapus data
if (isset($_GET['hapus_id'])) {
    $id = (int)$_GET['hapus_id']; 
    $stmt = $conn->prepare("DELETE FROM pendaftaran WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id); 
        if ($stmt->execute()) {
            echo "<script>alert('Data berhasil dihapus'); window.location.href='?';</script>";
            exit;
        } else {
            echo "Gagal menghapus data: " . $stmt->error; 
        }
        $stmt->close(); 
    } else {
        echo "Statement error: " . $conn->error;
    }
}

// Search dan Filter
$whereClauses = [];
$params = [];
$paramTypes = "";

if (!empty($_GET['search'])) {
    $whereClauses[] = "nama LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
    $paramTypes .= "s";
}

if (!empty($_GET['filter_gol'])) {
    $whereClauses[] = "gol_darah = ? AND status = 'Lolos'";
    $params[] = $_GET['filter_gol'];
    $paramTypes .= "s";
}

$whereSQL = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";

$stmt = $conn->prepare("SELECT * FROM pendaftaran $whereSQL ORDER BY id DESC");
if (!empty($params)) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result_pendaftaran = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="../assets/blood.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pendaftaran | Setetes</title>
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
                <a href="../dashboard.php" class="block p-2 rounded hover:bg-[#970c10]">Dashboard</a>
                <a href="#main" class="block p-2 rounded bg-[#970c10] transition">Pendaftaran</a>
                <a href="jadwal.php" class="block p-2 rounded hover:bg-[#970c10] transition">Jadwal Mobil Unit</a>
            </div>
            <a href="../auth/logout.php"
            class="block mt-4 bg-white text-[#970c10] font-semibold p-2 rounded shadow hover:bg-[#970c10] hover:text-white transition">Logout</a>
        </aside>
        <main class="flex-1 p-5 mt-4 md:mt-0">
            <h1 class="text-3xl font-bold text-[#970c10] mb-6">Data Pendaftaran</h1>
            <form method="POST" action="" class="bg-white p-6 rounded shadow mb-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">Nama</label>
                        <input name="nama" required class="border p-2 rounded w-full">
                    </div>
                    <div>
                        <label class="block font-medium">NIK (16 digit)</label>
                        <input name="nik" maxlength="16" minlength="16" required class="border p-2 rounded w-full">
                    </div>
                    <div>
                        <label class="block font-medium">Usia</label>
                        <input type="number" name="usia" required class="border p-2 rounded w-full">
                    </div>
                    <div>
                        <label class="block font-medium">Berat Badan (kg)</label>
                        <input type="number" name="berat_badan" step="0.1" required class="border p-2 rounded w-full">
                    </div>
                    <div>
                        <label class="block font-medium">Golongan Darah</label>
                        <select name="gol_darah" required class="border p-2 rounded w-full">
                        <option value="">Pilih Golongan</option><option>A</option><option>B</option><option>AB</option><option>O</option>
                        </select>
                    </div>
                    <div>
                        <span class="block font-medium">Rhesus</span>
                        <label><input type="radio" name="rhesus" value="+" required> +</label>
                        <label class="ml-4"><input type="radio" name="rhesus" value="-" required> -</label>
                    </div>
                    <div>
                        <span class="block font-medium">Sudah Sarapan?</span>
                        <label><input type="radio" name="sudah_sarapan" value="Ya" required> Ya</label>
                        <label class="ml-4"><input type="radio" name="sudah_sarapan" value="Tidak" required> Tidak</label>
                    </div>
                    <div>
                        <label class="block font-medium">Haid/Hamil/Menyusui?</label>
                        <select name="haid_hamil_menyusui" required class="border p-2 rounded w-full">
                        <option value="">Pilih...</option><option>Tidak</option><option>Haid</option><option>Hamil</option><option>Menyusui</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium">Riwayat Penyakit</label>
                        <input name="penyakit" class="border p-2 rounded w-full">
                    </div>
                    <div>
                        <span class="block font-medium">Konsumsi Alkohol?</span>
                        <label><input type="radio" name="konsumsi_alkohol" value="Tidak Pernah" required> Tidak Pernah</label>
                        <label class="ml-4"><input type="radio" name="konsumsi_alkohol" value="Pernah" required> Pernah</label>
                    </div>
                    <div>
                        <label class="block font-medium">Terakhir Donor</label>
                        <select name="terakhir_donor" required class="border p-2 rounded w-full">
                        <option value="">Pilih...</option><option>Pertama kali</option><option>< 2 bulan</option><option>> 2 bulan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium">Jenis Identitas</label>
                        <select name="jenis_identitas" required class="border p-2 rounded w-full">
                        <option value="">Pilih...</option><option>KTP</option><option>SIM</option><option>Kartu Pelajar</option><option>KTM</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium">Pilih Jadwal</label>
                        <select name="jadwal_id" required class="border p-2 rounded w-full">
                        <option value="">Pilih Jadwal</option>
                        <?php while ($jadwal = $result_jadwal->fetch_assoc()): ?>
                            <?php $label = $jadwal['tanggal']==$today?'(Hari Ini)':''; ?>
                            <option value="<?= $jadwal['id'] ?>"><?= "{$jadwal['tanggal']} - {$jadwal['lokasi']} $label" ?></option>
                        <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-[#970c10] text-white px-6 py-2 rounded">Simpan</button>
                </div>
            </form>

            <h2 class="text-2xl font-bold text-[#970c10] mb-4">Data Pendaftar</h2>

            <form method="GET" class="mb-4 flex flex-wrap gap-4 items-center">
                <!-- Pencarian Nama -->
                <input type="text" name="search" placeholder="Cari Nama" class="border p-2 rounded border-red-800 w-80" value="<?= $_GET['search'] ?? '' ?>">

                <button type="submit" class="bg-[#970c10] text-white px-4 py-2 rounded">Cari</button>

                <!-- Filter Golongan Darah -->
                <div class="flex gap-2">
                    <?php
                    $gol_darah_options = ['A', 'B', 'AB', 'O'];
                    foreach ($gol_darah_options as $gol) {
                        $active = (isset($_GET['filter_gol']) && $_GET['filter_gol'] === $gol) ? 'bg-[#970c10]' : 'bg-red-500';
                        echo "<a href='?filter_gol=$gol' class='$active text-white px-4 py-2 rounded'>$gol</a>";
                    }

                    // Reset filter
                    if (!empty($_GET['filter_gol'])) {
                        echo "<a href='?' class='bg-gray-500 text-white px-4 py-2 rounded'>Reset Filter</a>";
                    }
                    ?>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="tbl" class="min-w-full bg-white border cursor-pointer">
                <thead class="bg-[#970c10] text-white"><tr>
                    <th class="px-4 py-2 border">Nama</th><th class="px-4 py-2 border">Gol</th>
                    <th class="px-4 py-2 border">Rhesus</th><th class="px-4 py-2 border">Status</th><th class="px-4 py-2 border">Aksi</th>
                </tr></thead>
                <tbody>
                <?php while($row = $result_pendaftaran->fetch_assoc()): ?>
                    <tr data-id="<?= $row['id'] ?>">
                    <td class="px-4 py-2 border text-center"><?= htmlspecialchars($row['nama']) ?></td>
                    <td class="px-4 py-2 border text-center"><?= $row['gol_darah'] ?></td>
                    <td class="px-4 py-2 border text-center"><?= $row['rhesus'] ?></td>
                    <td class="px-4 py-2 border text-center"><?= $row['status'] ?></td>
                    <td class="px-4 py-2 border text-center">
                        <div class="flex flex-col sm:flex-row gap-2 justify-center">
                            <a href="update.php?id=<?= $row['id'] ?>"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded shadow transition">
                            Update
                            </a>
                            <a href="pendaftaran.php?hapus_id=<?= $row['id'] ?>"
                            onclick="return confirm('Yakin ingin menghapus data ini?')"
                            class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded shadow transition">
                            Hapus
                            </a>
                            <a href="data.php?id=<?= $row['id'] ?>"
                            class="bg-yellow-400 hover:bg-yellow-500 text-white text-sm px-3 py-1 rounded shadow transition">
                            Detail
                            </a>
                        </div>
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
