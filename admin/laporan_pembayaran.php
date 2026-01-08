<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran SPP</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; background: #fff; color: #000; padding: 20px; }
        .header { text-align: center; }
        .header h2, .header h4 { margin: 0; }
        .header h4 { font-weight: normal; }
        hr { border: 1px solid #000; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #eee; }
        .footer { margin-top: 40px; text-align: right; padding-right: 50px; font-size: 14px; }
        @media print { button, .form-filter, .kembali { display: none; } }
        .form-filter { margin: 20px 0; }
        select, button { padding: 5px 10px; margin-right: 10px; }
        .btn-print { margin-bottom: 20px; }
        .kembali { margin-top: 30px; }
        h3 { margin-top:40px; text-align:center; }
    </style>
</head>
<body>

<div class="header">
    <h2>SMP Kartini Parsoburan</h2>
    <h4>Laporan Pembayaran SPP</h4>
</div>
<hr>

<div class="form-filter">
    <form method="GET">
        <label>Bulan:
            <select name="filter_bulan">
                <option value="">-- Semua --</option>
                <?php
                $bulanList = ['Januari','Februari','Maret','April','Mei','Juni',
                              'Juli','Agustus','September','Oktober','November','Desember'];
                foreach ($bulanList as $bln) {
                    $selected = (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] === $bln) ? 'selected' : '';
                    echo "<option value=\"$bln\" $selected>$bln</option>";
                }
                ?>
            </select>
        </label>

        <label>Kelas:
            <select name="filter_kelas">
                <option value="">-- Semua --</option>
                <?php
                $kelasQuery = mysqli_query($koneksi, "SELECT DISTINCT kelas FROM siswa ORDER BY kelas");
                while ($k = mysqli_fetch_assoc($kelasQuery)) {
                    $selected = (isset($_GET['filter_kelas']) && $_GET['filter_kelas'] === $k['kelas']) ? 'selected' : '';
                    echo "<option value=\"{$k['kelas']}\" $selected>{$k['kelas']}</option>";
                }
                ?>
            </select>
        </label>

        <button type="submit">Tampilkan</button>
    </form>
</div>

<div class="btn-print">
    <button onclick="window.print()">🖨️ Cetak Laporan</button>
</div>

<?php
$filterBulan = isset($_GET['filter_bulan']) ? $_GET['filter_bulan'] : '';
$filterKelas = isset($_GET['filter_kelas']) ? $_GET['filter_kelas'] : '';
?>

<!-- ================== DATA YANG SUDAH BAYAR ================== -->
<h3>✔️ Siswa yang Sudah Bayar</h3>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Jumlah</th>
            <th>Tanggal Bayar</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $where = "WHERE pembayaran.status = 'lunas'";
    if ($filterBulan) {
        $bulan = mysqli_real_escape_string($koneksi, $filterBulan);
        $where .= " AND pembayaran.bulan = '$bulan'";
    }
    if ($filterKelas) {
        $kelas = mysqli_real_escape_string($koneksi, $filterKelas);
        $where .= " AND siswa.kelas = '$kelas'";
    }

    $query = mysqli_query($koneksi, "
        SELECT 
            siswa.nama,
            siswa.kelas,
            pembayaran.bulan,
            pembayaran.tahun,
            pembayaran.jumlah,
            pembayaran.tanggal_bayar,
            pembayaran.status
        FROM pembayaran
        INNER JOIN siswa ON pembayaran.id_siswa = siswa.id
        $where
        ORDER BY pembayaran.tanggal_bayar DESC
    ");

    $no = 1;
    while ($data = mysqli_fetch_assoc($query)) {
        echo "<tr>
            <td>{$no}</td>
            <td>" . htmlspecialchars($data['nama']) . "</td>
            <td>{$data['kelas']}</td>
            <td>{$data['bulan']}</td>
            <td>{$data['tahun']}</td>
            <td>Rp " . number_format($data['jumlah'], 0, ',', '.') . "</td>
            <td>{$data['tanggal_bayar']}</td>
            <td><strong>Lunas</strong></td>
        </tr>";
        $no++;
    }
    ?>
    </tbody>
</table>

<!-- PAGE BREAK supaya saat print pindah halaman -->
<div style="page-break-before: always;"></div>

<!-- ================== DATA YANG BELUM BAYAR ================== -->
<h3>❌ Siswa yang Belum Bayar</h3>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $whereSiswa = "WHERE 1=1";
    if ($filterKelas) {
        $kelas = mysqli_real_escape_string($koneksi, $filterKelas);
        $whereSiswa .= " AND kelas = '$kelas'";
    }

    $siswaQuery = mysqli_query($koneksi, "SELECT id, nama, kelas FROM siswa $whereSiswa ORDER BY kelas, nama");
    $no = 1;
    while ($siswa = mysqli_fetch_assoc($siswaQuery)) {
        $cek = mysqli_query($koneksi, "
            SELECT * FROM pembayaran 
            WHERE id_siswa='{$siswa['id']}'
            " . ($filterBulan ? " AND bulan='$filterBulan'" : "") . "
            AND status='lunas'
        ");
        if (mysqli_num_rows($cek) == 0) {
            echo "<tr>
                <td>{$no}</td>
                <td>" . htmlspecialchars($siswa['nama']) . "</td>
                <td>{$siswa['kelas']}</td>
                <td>" . ($filterBulan ?: '-') . "</td>
                <td>" . date('Y') . "</td>
                <td><strong>Belum Bayar</strong></td>
            </tr>";
            $no++;
        }
    }
    ?>
    </tbody>
</table>

<div class="footer">
    <p>Parsoburan, <?= date('d-m-Y') ?></p>
    <p><strong>Kepala Sekolah</strong></p>
    <br><br><br>
    <p>_____________________________</p>
</div>

<div class="kembali">
    <a href="dashboard.php">← Kembali ke Dashboard</a>
</div>

</body>
</html>
