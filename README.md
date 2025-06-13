## Deskripsi

**Setetes** merupakan aplikasi berbasis web untuk mengelola pendaftaran donor darah, dikembangkan sebagai projek akhir dari praktikum pemrograman berbasis web, dengan menerapkan materi dari modul 1 hingga 6. 
Sistem ini bukan multi-user, hanya **admin** yang memiliki akses untuk melakukan input, pengelolaan data, dan melihat rekap informasi.

Sistem ini membantu admin (petugas) dalam:
- Mencatat pendaftaran calon pendonor
- Menyimpan data riwayat donor
- Mengelola jadwal mobil unit donor darah
- Melihat rekap ketersediaan darah

## Fitur Utama
**Login Admin (Session-based)**
**- Dashboard Ringkasan:**
  - Total pendaftar
  - Total petugas (admin)
  - Tabel jumlah darah berdasarkan golongannya
  - Quick-access ke menu utama

**- Manajemen Pendaftaran Donor:**
  - Form pendaftaran dengan validasi kelayakan donor (otomatis menentukan status Lolos/Gagal)
  - Riwayat pendaftar tersimpan baik yang lolos maupun gagal
  - Edit, hapus, dan detail pendaftaran
  - Rekap stok darah berdasarkan golongan & rhesus dari pendaftar yang lolos

**- Manajemen Jadwal Mobil Unit:**
  - Tambah, edit, hapus jadwal lokasi donor
  - Tabel jadwal

## Teknologi & Tools

- **Frontend:** HTML5, CSS3, TailwindCSS, JavaScript
- **Backend:** PHP (Native PHP)
- **Database:** MySQLi
- **Responsive:** Mobile friendly (TailwindCSS Framework)

## Struktur Menu

|          Menu          |                     Deskripsi                        |
| -----------------------| ---------------------------------------------------- |
| **Dashboard**          | Ringkasan jumlah pendaftar, petugas, dan akses cepat |
| **Pendaftaran**        | CRUD data pendaftar, form validasi donor             |
| **Jadwal**             | CRUD data jadwal mobil unit donor darah              |
| **Logout**             | Keluar dari session admin                            |

## Struktur Database

### 1 Tabel `admin`
|   Field  |     Tipe     |     Keterangan       |
| -------- | ------------ | -------------------- |
| id       | INT          | Primary Key          |
| nama     | VARCHAR(100) | Nama admin           |
| email    | VARCHAR(100) | Email admin          |  
| password | VARCHAR(255) | Password terenkripsi |

### 2 Tabel `jadwal`
|  Field  |     Tipe     |    Keterangan   |
| ------- | ------------ | --------------- |
| id      | INT          | Primary Key     |
| tanggal | DATE         | Tanggal jadwal  |
| lokasi  | VARCHAR(255) | Lokasi kegiatan |
| waktu   | TIME         | Waktu kegiatan  |

### 3 Tabel `pendaftaran`
|        Field        |                       Tipe                   |       Keterangan         |
| ------------------- | -------------------------------------------- | ------------------------ |
| id                  | INT                                          | Primary Key              |
| nama                | VARCHAR(100)                                 | Nama pendaftar           |
| nik                 | CHAR(16)                                     | NIK                      |
| usia                | INT                                          | Usia pendaftar           |
| berat_badan         | FLOAT                                        | Berat badan              |
| gol_darah           | ENUM('A','B','AB','O')                       | Golongan darah           |
| rhesus              | ENUM('+','-')                                | Rhesus                   |
| sudah_sarapan       | ENUM('Ya','Tidak')                           | Sarapan atau tidak       |
| haid_hamil_menyusui | ENUM('Tidak','Haid','Hamil','Menyusui')      | Kondisi hormonal         |
| penyakit            | TEXT                                         | Riwayat penyakit         |
| konsumsi_alkohol    | ENUM('Tidak Pernah','Pernah')                | Riwayat konsumsi alkohol |
| terakhir_donor      | ENUM('Pertama kali','< 2 bulan','> 2 bulan') | Riwayat donor sebelumnya |
| jenis_identitas     | ENUM('KTP','SIM','Kartu Pelajar','KTM')      | Identitas yang dibawa    |
| jadwal_id           | INT                                          | Relasi ke jadwal donor   |
| status              | ENUM('Lolos','Gagal')                        | Status kelayakan donor   |

## Cara Install & Jalankan
1. Clone / Download repository
2. Import database SQL (`donor_darah.sql`)
3. Sesuaikan konfigurasi koneksi database (`config.php`)
4. Jalankan di local server (XAMPP, Laragon, dsb.)

## 👩‍💻 Developer
- Nama: Yadya Niha'ul Ala
- NIM: 240441100006
- Proyek: Tugas Akhir Pemrograman Berbasis Web Semester 2
- Tema: Pedaftaran Donor Darah

## 🩸 "Setetes darahmu, sejuta harapan." 🩸
