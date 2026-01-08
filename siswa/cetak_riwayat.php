<?php
session_start();
include '../config/koneksi.php';

// Proteksi akses hanya untuk siswa
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'siswa') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id'];

// Ambil data siswa
$query_siswa = mysqli_query($koneksi, "SELECT * FROM siswa WHERE id_user = '$id_user'");
$data_siswa = mysqli_fetch_assoc($query_siswa);

// Jika belum terhubung
if (!$data_siswa) {
    die("Data siswa tidak ditemukan. Silakan hubungi admin.");
}

$id_siswa = $data_siswa['id'];
$nama_siswa = $data_siswa['nama'];
$nis_siswa = $data_siswa['nis'];
$kelas_siswa = $data_siswa['kelas'];

// Ambil riwayat pembayaran
$query_riwayat = mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE id_siswa = '$id_siswa' ORDER BY tanggal_bayar DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Riwayat Pembayaran</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2, h4 { text-align: center; margin: 0; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            padding-right: 40px;
        }
    </style>
</head>
<body onload="window.print()">

    <h2>Riwayat Pembayaran SPP</h2>
    <h4>SMP Kartini Parsoburan</h4>
    <br>
    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td><strong>Nama Siswa:</strong></td><td><?= htmlspecialchars($nama_siswa) ?></td>
        </tr>
        <tr>
            <td><strong>NIS:</strong></td><td><?= htmlspecialchars($nis_siswa) ?></td>
        </tr>
        <tr>
            <td><strong>Kelas:</strong></td><td><?= htmlspecialchars($kelas_siswa) ?></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Tanggal Bayar</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            if (mysqli_num_rows($query_riwayat) > 0) {
                while ($row = mysqli_fetch_assoc($query_riwayat)) {
                    echo "<tr>";
                    echo "<td>{$no}</td>";
                    echo "<td>" . htmlspecialchars($row['bulan']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tahun']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tanggal_bayar']) . "</td>";
                    echo "<td>Rp " . number_format($row['jumlah'], 0, ',', '.') . "</td>";
                    echo "<td>" . ($row['status'] === 'lunas' ? 'Lunas' : 'Pending') . "</td>";
                    echo "</tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='6'>Belum ada data pembayaran.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: <?= date('d-m-Y H:i') ?>
    </div>

</body>
</html>
