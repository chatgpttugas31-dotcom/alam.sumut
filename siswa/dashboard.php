<?php 
session_start();
include '../config/koneksi.php';

// Proteksi agar hanya siswa yang bisa mengakses
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'siswa') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id'];
$query_siswa = mysqli_query($koneksi, "SELECT * FROM siswa WHERE id_user = '$id_user'");
$data_siswa = mysqli_fetch_assoc($query_siswa);

// Jika data siswa tidak ditemukan
if (!$data_siswa) {
    echo "<script>alert('Data siswa tidak ditemukan. Silakan hubungi admin.');</script>";
    $nama_siswa = 'Tidak ditemukan';
    $nis_siswa = '-';
    $kelas_siswa = '-';
    $id_siswa = 0;
} else {
    $nama_siswa = $data_siswa['nama'];
    $nis_siswa = $data_siswa['nis'];
    $kelas_siswa = $data_siswa['kelas'];
    $id_siswa = $data_siswa['id'];
}

$bulan_ini = date('F');
$tahun_ini = date('Y');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #667eea, #764ba2);
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 950px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            position: relative;
        }
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            background-color: #dc3545;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        h2 {
            color: #4e54c8;
            margin-bottom: 15px;
        }
        .info {
            margin-bottom: 25px;
            padding: 15px;
            background: #f7f9fc;
            border-left: 5px solid #4e54c8;
            border-radius: 6px;
        }
        .info p {
            margin: 5px 0;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            text-align: center;
        }
        th {
            background-color: #4e54c8;
            color: white;
            text-transform: uppercase;
        }
        .status-lunas {
            color: green;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .upload-btn {
            display: inline-block;
            margin-bottom: 15px;
            background-color: #4e54c8;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.3s;
        }
        .upload-btn i {
            margin-right: 8px;
        }
        .upload-btn:hover {
            background-color: #3b3f91;
        }
        .footer {
            margin-top: 25px;
            font-size: 14px;
            text-align: center;
            color: #aaa;
        }
        a {
            color: #4e54c8;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Tombol Logout -->
    <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>

    <h2><i class="fas fa-user-graduate"></i> Selamat Datang, <?= htmlspecialchars($nama_siswa) ?></h2>

    <div class="info">
        <p><strong>NIS:</strong> <?= htmlspecialchars($nis_siswa) ?></p>
        <p><strong>Kelas:</strong> <?= htmlspecialchars($kelas_siswa) ?></p>
    </div>

    <?php if ($id_siswa != 0): ?>
        <a href="bayar_spp.php?bulan=<?= $bulan_ini ?>&tahun=<?= $tahun_ini ?>" class="upload-btn">
            <i class="fas fa-money-check-alt"></i> Bayar SPP
        </a>
        <a href="cetak_riwayat.php" class="upload-btn" target="_blank">
            <i class="fas fa-print"></i> Cetak Riwayat
        </a>

        <h3><i class="fas fa-check-circle"></i> Status Pembayaran Terakhir</h3>
        <?php
        $last = mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE id_siswa = '$id_siswa' ORDER BY tanggal_bayar DESC LIMIT 1");
        $latest = mysqli_fetch_assoc($last);
        if ($latest) {
            echo "<p><strong>Bulan:</strong> " . htmlspecialchars($latest['bulan']) . " " . htmlspecialchars($latest['tahun']) . "</p>";
            echo "<p><strong>Status:</strong> " . 
                ($latest['status'] == 'lunas' 
                    ? "<span class='status-lunas'>Lunas</span>" 
                    : "<span class='status-pending'>Menunggu Verifikasi</span>") . "</p>";
        } else {
            echo "<p>Belum ada pembayaran yang tercatat.</p>";
        }
        ?>

        <h3><i class="fas fa-history"></i> Riwayat Pembayaran</h3>
        <table>
            <tr>
                <th>No</th>
                <th>Tanggal Bayar</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Bukti</th>
            </tr>
            <?php
            $no = 1;
            $riwayat = mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE id_siswa = '$id_siswa' ORDER BY tanggal_bayar DESC");
            if (mysqli_num_rows($riwayat) > 0) {
                while ($row = mysqli_fetch_assoc($riwayat)) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['tanggal_bayar']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['bulan']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tahun']) . "</td>";
                    echo "<td>Rp " . number_format($row['jumlah'], 0, ',', '.') . "</td>";
                    echo "<td>" . 
                        ($row['status'] == 'lunas' 
                            ? "<span class='status-lunas'>Lunas</span>" 
                            : "<span class='status-pending'>Menunggu Verifikasi</span>") . "</td>";
                    echo "<td>";
                    if (!empty($row['bukti'])) {
                        echo "<a href='../uploads/" . htmlspecialchars($row['bukti']) . "' target='_blank'><i class='fas fa-image'></i> Lihat</a>";
                    } else {
                        echo "Belum Upload";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Belum ada pembayaran.</td></tr>";
            }
            ?>
        </table>
    <?php else: ?>
        <p style="color: red;">⚠️ Akun Anda belum terhubung ke data siswa. Silakan hubungi admin.</p>
    <?php endif; ?>

    <div class="footer">
        &copy; <?= date('Y') ?> SMP Kartini Parsoburan — Sistem Pembayaran SPP
    </div>
</div>

</body>
</html>
