<?php 
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

if (!isset($_GET['id'])) {
    echo "ID pembayaran tidak ditemukan.";
    exit;
}

$id_pembayaran = $_GET['id'];

// Ambil data pembayaran dan data siswa
$query = "SELECT pembayaran.*, siswa.nama, siswa.kelas 
          FROM pembayaran 
          JOIN siswa ON pembayaran.id_siswa = siswa.id 
          WHERE pembayaran.id = '$id_pembayaran'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    echo "Data tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bukti Pembayaran</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .bukti-container {
            width: 700px;
            margin: auto;
            border: 1px solid #000;
            padding: 20px;
        }
        .bukti-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .bukti-body table {
            width: 100%;
            border-collapse: collapse;
        }
        .bukti-body td {
            padding: 8px;
        }
        .bukti-footer {
            margin-top: 30px;
            text-align: right;
        }
        .btn-print {
            display: block;
            width: 100px;
            margin: 20px auto;
            padding: 10px;
            background-color: #2e86de;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }
        @media print {
            .btn-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="bukti-container">
        <div class="bukti-header">
            <h2>Bukti Pembayaran SPP</h2>
            <p>SMP Kartini Parsoburan</p>
            <hr>
        </div>

        <div class="bukti-body">
            <table>
                <tr>
                    <td>Nama Siswa</td>
                    <td>: <?= $data['nama']; ?></td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>: <?= $data['kelas']; ?></td>
                </tr>
                <tr>
                    <td>Bulan</td>
                    <td>: <?= $data['bulan']; ?> <?= $data['tahun']; ?></td>
                </tr>
                <tr>
                    <td>Jumlah Bayar</td>
                    <td>: Rp <?= number_format($data['jumlah'], 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td>Tanggal Bayar</td>
                    <td>: <?= date('d-m-Y', strtotime($data['tanggal_bayar'])); ?></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>: <?= ucfirst($data['status']); ?></td>
                </tr>
                <tr>
                    <td>Bukti Transfer</td>
                    <td>
                        <?php if (!empty($data['bukti']) && file_exists('../uploads/' . $data['bukti'])): ?>
                            <img src="../uploads/<?= $data['bukti']; ?>" alt="Bukti Transfer" width="300">
                        <?php else: ?>
                            Tidak ada bukti transfer.
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="bukti-footer">
            <p>Dicetak oleh: Admin</p>
            <p><?= date("d-m-Y H:i:s"); ?></p>
        </div>
    </div>

    <a href="#" class="btn-print" onclick="window.print()">Cetak</a>
</body>
</html>
