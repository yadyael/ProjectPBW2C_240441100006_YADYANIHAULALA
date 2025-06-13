<?php
session_start();
require '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: pendaftaran.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM pendaftaran WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

// update 
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

    $stmt = $conn->prepare("UPDATE pendaftaran SET 
        nama = ?, nik = ?, usia = ?, berat_badan = ?, gol_darah = ?, rhesus = ?, sudah_sarapan = ?, 
        haid_hamil_menyusui = ?, penyakit = ?, konsumsi_alkohol = ?, terakhir_donor = ?, jenis_identitas = ?, 
        jadwal_id = ?, status = ?, alasan_gagal = ?
        WHERE id = ?");

    $stmt->bind_param("ssidsssssssssssi", $nama, $nik, $usia, $berat_badan, $gol_darah, $rhesus, $sudah_sarapan,
        $haid_hamil_menyusui, $penyakit, $konsumsi_alkohol, $terakhir_donor, $jenis_identitas, $jadwal_id,
        $status, $alasan_gagal, $id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Data berhasil diupdate'); window.location.href='pendaftaran.php#table';</script>";
    exit;
}

// Ambil data jadwal
$today = date('Y-m-d');
$query_jadwal = $conn->prepare("SELECT * FROM jadwal WHERE tanggal >= ? ORDER BY tanggal ASC");
$query_jadwal->bind_param("s", $today);
$query_jadwal->execute();
$result_jadwal = $query_jadwal->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="../assets/blood.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data Pendaftaran | Setetes</title>
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
                <a href="#main" class="block p-2 rounded bg-[#970c10] transition">Update Data Pendaftaran</a>
                <a href="pendaftaran.php" class="block p-2 rounded hover:bg-[#970c10] transition">Kembali</a>
            </div>
            <a href="../auth/logout.php"
            class="block mt-4 bg-white text-[#970c10] font-semibold p-2 rounded shadow hover:bg-[#970c10] hover:text-white transition">Logout</a>
        </aside>
        <main class="flex-1 p-5 mt-4 md:mt-0">
            <h1 class="text-3xl font-bold text-[#970c10] mb-6">Update Data Pendaftaran</h1>

            <?php
            // Tampilkan pesan sukses jika ada
            if (isset($_GET['success']) && $_GET['success'] == 1) {
                echo '<div class="bg-green-100 text-green-800 p-3 rounded mb-4">Data berhasil diperbarui.</div>';
            }
            ?>

            <form method="POST" action="" class="bg-white p-6 rounded shadow mb-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">Nama</label>
                        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required class="border p-2 rounded w-full">
                    </div>
                    <div>
                        <label class="block font-medium">NIK (16 digit)</label>
                        <input name="nik" maxlength="16" minlength="16" value="<?= htmlspecialchars($data['nik']) ?>" required class="border p-2 rounded w-full">
                    </div>
                    <div>
                        <label class="block font-medium">Usia</label>
                        <input type="number" name="usia" value="<?= htmlspecialchars($data['usia']) ?>" required class="border p-2 rounded w-full">
                    </div>
                    <div>
                        <label class="block font-medium">Berat Badan (kg)</label>
                        <input type="number" name="berat_badan" step="0.1" value="<?= htmlspecialchars($data['berat_badan']) ?>" required class="border p-2 rounded w-full">
                    </div>
                    <div>
                        <label class="block font-medium">Golongan Darah</label>
                        <select name="gol_darah" required class="border p-2 rounded w-full">
                            <option value="">Pilih Golongan</option>
                            <?php foreach (['A', 'B', 'AB', 'O'] as $gol): ?>
                                <option value="<?= $gol ?>" <?= $data['gol_darah'] == $gol ? 'selected' : '' ?>><?= $gol ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <span class="block font-medium">Rhesus</span>
                        <label><input type="radio" name="rhesus" value="+" <?= $data['rhesus'] == '+' ? 'checked' : '' ?> required> +</label>
                        <label class="ml-4"><input type="radio" name="rhesus" value="-" <?= $data['rhesus'] == '-' ? 'checked' : '' ?> required> -</label>
                    </div>
                    <div>
                        <span class="block font-medium">Sudah Sarapan?</span>
                        <label><input type="radio" name="sudah_sarapan" value="Ya" <?= $data['sudah_sarapan'] == 'Ya' ? 'checked' : '' ?> required> Ya</label>
                        <label class="ml-4"><input type="radio" name="sudah_sarapan" value="Tidak" <?= $data['sudah_sarapan'] == 'Tidak' ? 'checked' : '' ?> required> Tidak</label>
                    </div>
                    <div>
                        <label class="block font-medium">Haid/Hamil/Menyusui?</label>
                        <select name="haid_hamil_menyusui" required class="border p-2 rounded w-full">
                            <option value="">Pilih...</option>
                            <?php foreach (['Tidak', 'Haid', 'Hamil', 'Menyusui'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $data['haid_hamil_menyusui'] == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium">Riwayat Penyakit</label>
                        <input name="penyakit" value="<?= htmlspecialchars($data['penyakit']) ?>" class="border p-2 rounded w-full">
                    </div>
                    <div>
                        <span class="block font-medium">Konsumsi Alkohol?</span>
                        <label><input type="radio" name="konsumsi_alkohol" value="Tidak Pernah" <?= $data['konsumsi_alkohol'] == 'Tidak Pernah' ? 'checked' : '' ?> required> Tidak Pernah</label>
                        <label class="ml-4"><input type="radio" name="konsumsi_alkohol" value="Pernah" <?= $data['konsumsi_alkohol'] == 'Pernah' ? 'checked' : '' ?> required> Pernah</label>
                    </div>
                    <div>
                        <label class="block font-medium">Terakhir Donor</label>
                        <select name="terakhir_donor" required class="border p-2 rounded w-full">
                            <option value="">Pilih...</option>
                            <?php foreach (['Pertama kali', '< 2 bulan', '> 2 bulan'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $data['terakhir_donor'] == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium">Jenis Identitas</label>
                        <select name="jenis_identitas" required class="border p-2 rounded w-full">
                            <option value="">Pilih...</option>
                            <?php foreach (['KTP', 'SIM', 'Kartu Pelajar', 'KTM'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $data['jenis_identitas'] == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium">Pilih Jadwal</label>
                        <select name="jadwal_id" required class="border p-2 rounded w-full">
                            <option value="">Pilih Jadwal</option>
                            <?php while ($jadwal = $result_jadwal->fetch_assoc()): ?>
                                <?php
                                $label = $jadwal['tanggal'] == $today ? '(Hari Ini)' : '';
                                $selected = $data['jadwal_id'] == $jadwal['id'] ? 'selected' : '';
                                ?>
                                <option value="<?= $jadwal['id'] ?>" <?= $selected ?>>
                                    <?= "{$jadwal['tanggal']} - {$jadwal['lokasi']} $label" ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-[#970c10] text-white px-6 py-2 rounded">Update</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
