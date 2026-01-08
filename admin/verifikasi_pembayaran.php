<?php
session_start();
include '../config/koneksi.php';

// Proteksi admin
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Setujui
if (isset($_GET['setujui'])) {
    $id = $_GET['setujui'];
    mysqli_query($koneksi, "UPDATE pembayaran SET status='lunas' WHERE id='$id'");
    header("Location: verifikasi_pembayaran.php");
    exit;
}

// Tolak
if (isset($_GET['tolak'])) {
    $id = $_GET['tolak'];
    $cek = mysqli_query($koneksi, "SELECT bukti FROM pembayaran WHERE id='$id'");
    $data = mysqli_fetch_assoc($cek);
    if (!empty($data['bukti']) && file_exists('../uploads/' . $data['bukti'])) {
        unlink('../uploads/' . $data['bukti']);
    }
    mysqli_query($koneksi, "DELETE FROM pembayaran WHERE id='$id'");
    header("Location: verifikasi_pembayaran.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Pembayaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; padding: 20px; }
        h2 { color: #333; }
        #searchInput { padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; }
        th { background-color: #4e54c8; color: white; }
        .btn { padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; color: white; margin: 2px; }
        .btn-approve { background-color: #28a745; }
        .btn-reject { background-color: #dc3545; }
        .btn:hover { opacity: 0.9; }
        .status-lunas { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-belum { color: red; font-weight: bold; }
        .view-link { background-color: #17a2b8; color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; }
        .view-link:hover { background-color: #138496; }
        .logout { margin-top: 20px; }
        form { margin-bottom: 15px; }
    </style>
</head>
<body>

<h2>Verifikasi Pembayaran SPP</h2>

<form method="GET">
    <select name="filter_bulan">
        <option value="">-- Pilih Bulan --</option>
        <?php
        $bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        foreach ($bulanList as $bln) {
            $selected = (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] === $bln) ? 'selected' : '';
            echo "<option value=\"$bln\" $selected>$bln</option>";
        }
        ?>
    </select>

    <select name="filter_kelas">
        <option value="">-- Pilih Kelas --</option>
        <?php
        $kelasQuery = mysqli_query($koneksi, "SELECT DISTINCT kelas FROM siswa ORDER BY kelas");
        while ($k = mysqli_fetch_assoc($kelasQuery)) {
            $selected = (isset($_GET['filter_kelas']) && $_GET['filter_kelas'] === $k['kelas']) ? 'selected' : '';
            echo "<option value=\"{$k['kelas']}\" $selected>{$k['kelas']}</option>";
        }
        ?>
    </select>

    <button type="submit">Filter</button>
</form>

<input type="text" id="searchInput" placeholder="Cari nama siswa...">

<table id="tabelPembayaran">
    <tr>
        <th>No</th>
        <th>Nama Siswa</th>
        <th>Kelas</th>
        <th>Bulan</th>
        <th>Tahun</th>
        <th>Jumlah</th>
        <th>Tanggal Bayar</th>
        <th>Bukti Transfer</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

<?php
$filterBulan = isset($_GET['filter_bulan']) ? $_GET['filter_bulan'] : '';
$filterKelas = isset($_GET['filter_kelas']) ? $_GET['filter_kelas'] : '';

$whereKelas = $filterKelas ? "WHERE siswa.kelas = '$filterKelas'" : '';
$order = "ORDER BY 
    CASE WHEN pembayaran.tanggal_bayar IS NULL THEN 1 ELSE 0 END, 
    pembayaran.tanggal_bayar DESC";

$query = mysqli_query($koneksi, "
    SELECT 
        siswa.id AS id_siswa,
        siswa.nama,
        siswa.kelas,
        pembayaran.id,
        pembayaran.bulan,
        pembayaran.tahun,
        pembayaran.jumlah,
        pembayaran.tanggal_bayar,
        pembayaran.bukti,
        pembayaran.status
    FROM siswa
    LEFT JOIN pembayaran ON siswa.id = pembayaran.id_siswa 
        " . ($filterBulan ? "AND LOWER(pembayaran.bulan) = LOWER('$filterBulan')" : "") . "
    $whereKelas
    $order
");

$no = 1;
while ($data = mysqli_fetch_assoc($query)) {
    ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($data['nama']) ?></td>
        <td><?= htmlspecialchars($data['kelas']) ?></td>
        <td><?= $data['bulan'] ?? ($filterBulan ?: '-') ?></td>
        <td><?= $data['tahun'] ?? '-' ?></td>
        <td><?= isset($data['jumlah']) ? 'Rp ' . number_format($data['jumlah'], 0, ',', '.') : '-' ?></td>
        <td><?= $data['tanggal_bayar'] ?? '-' ?></td>
        <td>
            <?php if (!empty($data['bukti'])): ?>
                <a href="../uploads/<?= $data['bukti'] ?>" target="_blank" class="view-link">Lihat Bukti</a>
            <?php else: ?>
                Tidak ada
            <?php endif; ?>
        </td>
        <td>
            <?php
            if ($data['status'] === 'lunas') {
                echo '<span class="status-lunas">Lunas</span>';
            } elseif ($data['status'] === 'pending') {
                echo '<span class="status-pending">Pending</span>';
            } else {
                echo '<span class="status-belum">Belum Bayar</span>';
            }
            ?>
        </td>
        <td>
            <?php if ($data['status'] === 'pending'): ?>
                <a href="?setujui=<?= $data['id'] ?>" class="btn btn-approve" onclick="return confirm('Setujui pembayaran ini?')">
                    <i class="fas fa-check"></i> Setujui
                </a>
                <a href="?tolak=<?= $data['id'] ?>" class="btn btn-reject" onclick="return confirm('Tolak dan hapus pembayaran ini?')">
                    <i class="fas fa-times"></i> Tolak
                </a>
            <?php elseif ($data['status'] === 'lunas'): ?>
                <i class="fas fa-check-circle" style="color: green;"></i>
            <?php else: ?>
                -
            <?php endif; ?>
        </td>
    </tr>
<?php } ?>
</table>

<script>
document.getElementById('searchInput').addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tabelPembayaran tr:not(:first-child)');
    rows.forEach(row => {
        const nama = row.cells[1].textContent.toLowerCase();
        row.style.display = nama.includes(filter) ? '' : 'none';
    });
});
</script>

<div class="logout">
    <a href="dashboard.php">← Kembali ke Dashboard</a>
</div>

</body>
</html>
