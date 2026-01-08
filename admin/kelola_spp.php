<?php
session_start();
if ($_SESSION['level'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

// Tambah data SPP
if (isset($_POST['tambah'])) {
    $tahun = mysqli_real_escape_string($koneksi, $_POST['tahun']);
    $nominal = mysqli_real_escape_string($koneksi, $_POST['nominal']);

    $cek = mysqli_query($koneksi, "SELECT * FROM spp WHERE tahun = '$tahun'");
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($koneksi, "INSERT INTO spp (tahun, nominal) VALUES ('$tahun', '$nominal')");
        $msg = "Data SPP tahun $tahun berhasil ditambahkan.";
    } else {
        $msg = "SPP untuk tahun $tahun sudah ada!";
    }
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM spp WHERE id = '$id'");
    header("Location: kelola_spp.php");
    exit;
}

// Ambil semua data spp
$data_spp = mysqli_query($koneksi, "SELECT * FROM spp ORDER BY tahun DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola SPP per Tahun</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4 text-center text-primary">Kelola Data SPP per Tahun</h2>

        <?php if (isset($msg)) : ?>
            <div class="alert alert-info"><?= $msg; ?></div>
        <?php endif; ?>

        <form method="POST" class="mb-4 p-3 bg-white rounded shadow-sm">
            <div class="form-row align-items-end">
                <div class="form-group col-md-4">
                    <label>Tahun</label>
                    <input type="number" name="tahun" class="form-control" min="2020" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Nominal / Bulan</label>
                    <input type="number" name="nominal" class="form-control" placeholder="Contoh: 250000" required>
                </div>
                <div class="form-group col-md-4">
                    <button type="submit" name="tambah" class="btn btn-primary btn-block">Tambah SPP</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered bg-white shadow-sm">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    <th>Tahun</th>
                    <th>Nominal / Bulan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($data_spp)) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['tahun']; ?></td>
                        <td>Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                        <td>
                            <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data tahun <?= $row['tahun']; ?>?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary mt-3">← Kembali ke Dashboard</a>
    </div>
</body>
</html>
