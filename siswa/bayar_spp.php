<?php  
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'siswa') {
    header("Location: ../login.php");
    exit;
}

// Ambil id_user dari session
$id_user = $_SESSION['id'];

// Cari id_siswa berdasarkan id_user
$query_siswa = mysqli_query($koneksi, "SELECT id FROM siswa WHERE id_user = '$id_user'");
$data_siswa = mysqli_fetch_assoc($query_siswa);

if (!$data_siswa) {
    echo "<script>alert('Data siswa tidak ditemukan!');history.back();</script>";
    exit;
}

$id_siswa = $data_siswa['id'];
$jumlah = 250000; // Nominal tetap

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bulan  = mysqli_real_escape_string($koneksi, $_POST['bulan']);
    $tahun  = mysqli_real_escape_string($koneksi, $_POST['tahun']);
    $tanggal_bayar = date('Y-m-d');

    // Validasi tahun harus 4 digit
    if (!preg_match('/^[0-9]{4}$/', $tahun)) {
        echo "<script>alert('Tahun harus berupa 4 digit angka!');history.back();</script>";
        exit;
    }

    // Cek apakah pembayaran bulan dan tahun yang sama sudah pernah dilakukan
    $cek_duplikat = mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE id_siswa = '$id_siswa' AND bulan = '$bulan' AND tahun = '$tahun'");
    if (mysqli_num_rows($cek_duplikat) > 0) {
        echo "<script>alert('Pembayaran untuk bulan $bulan $tahun sudah pernah dilakukan!');history.back();</script>";
        exit;
    }

    // Validasi file upload
    $file = $_FILES['bukti'];
    $allowed = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo "<script>alert('Format file hanya jpg, jpeg, atau png!');history.back();</script>";
        exit;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        echo "<script>alert('Ukuran file maksimal 2MB!');history.back();</script>";
        exit;
    }

    $upload_dir = "../uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $new_filename = time() . "_siswa{$id_siswa}." . $ext;
    $path = $upload_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        $query = "INSERT INTO pembayaran (id_siswa, bulan, tahun, jumlah, tanggal_bayar, bukti, status)
                  VALUES ('$id_siswa', '$bulan', '$tahun', '$jumlah', '$tanggal_bayar', '$new_filename', 'pending')";

        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Pembayaran berhasil dikirim. Menunggu konfirmasi admin.'); window.location='dashboard.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan ke database: " . mysqli_error($koneksi) . "');history.back();</script>";
        }
    } else {
        echo "<script>alert('Upload bukti gagal.');history.back();</script>";
    }
}

// Data untuk bulan & tahun default
$bulan_list = ['Januari','Februari','Maret','April','Mei','Juni',
               'Juli','Agustus','September','Oktober','November','Desember'];
$bulan_sekarang = date('n') - 1;
$tahun_sekarang = date('Y');

// Ambil bulan yang sudah dibayar siswa
$sudah_bayar = [];
$cek_bayar = mysqli_query($koneksi, "SELECT bulan, tahun FROM pembayaran WHERE id_siswa='$id_siswa'");
while ($row = mysqli_fetch_assoc($cek_bayar)) {
    $sudah_bayar[] = $row['bulan'] . '-' . $row['tahun'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bayar SPP</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #eef2f7;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            background: white;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #4e54c8;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="number"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        input[readonly] {
            background-color: #f2f2f2;
        }

        button {
            margin-top: 20px;
            padding: 12px;
            background-color: #4e54c8;
            color: white;
            border: none;
            width: 100%;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: #3b409c;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #333;
        }

        .back-link:hover {
            color: #4e54c8;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Bayar SPP</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="bulan">Bulan:</label>
        <select id="bulan" name="bulan" required>
            <option value="">-- Pilih Bulan --</option>
            <?php 
            foreach ($bulan_list as $index => $bln) {
                $key = $bln . '-' . $tahun_sekarang;
                if (in_array($key, $sudah_bayar)) {
                    // Sudah dibayar → disabled
                    echo "<option value=\"$bln\" disabled>$bln (sudah dibayar)</option>";
                } else {
                    $selected = ($index === $bulan_sekarang) ? 'selected' : '';
                    echo "<option value=\"$bln\" $selected>$bln</option>";
                }
            }
            ?>
        </select>

        <label for="tahun">Tahun:</label>
        <input type="number" name="tahun" id="tahun" required value="<?= $tahun_sekarang ?>">

        <label for="jumlah">Jumlah Bayar (Rp):</label>
        <input type="number" id="jumlah" name="jumlah" value="250000" readonly required>

        <label for="bukti">Upload Bukti Transfer:</label>
        <input type="file" id="bukti" name="bukti" accept=".jpg,.jpeg,.png" required>

        <button type="submit">Kirim Pembayaran</button>
    </form>

    <a class="back-link" href="dashboard.php">← Kembali ke Dashboard</a>
</div>
</body>
</html>
