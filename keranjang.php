<?php
session_start();

/* =========================
   RESET KERANJANG (DEV ONLY)
   ========================= */
if (isset($_GET['reset'])) {
    unset($_SESSION['keranjang']);
    header("Location: index.php");
    exit;
}

/* =========================
   INISIALISASI
   ========================= */
if (!isset($_SESSION['keranjang']) || !is_array($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

/* =========================
   TAMBAH KE KERANJANG
   ========================= */
if (isset($_POST['tambah'])) {

    $id    = $_POST['id'];
    $nama  = $_POST['nama'];
    $harga = (int) $_POST['harga'];
    $jenis = $_POST['jenis'];
    $hari  = ($jenis === 'sewa') ? max(1, (int)($_POST['hari'] ?? 1)) : 1;

    if (!isset($_SESSION['keranjang'][$id])) {
        $_SESSION['keranjang'][$id] = [
            'nama'   => $nama,
            'harga'  => $harga,
            'jenis'  => $jenis,
            'jumlah' => $hari
        ];
    } else {
        // jika item sama ditambahkan lagi
        $_SESSION['keranjang'][$id]['jumlah'] += $hari;
    }

    header("Location: index.php");
    exit;
}

/* =========================
   HAPUS ITEM
   ========================= */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    unset($_SESSION['keranjang'][$id]);
    header("Location: keranjang.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Keranjang | Senandung Alam</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background:#f4f8f4">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-success">
  <div class="container">
    <span class="navbar-brand fw-bold">🛒 Keranjang Belanja</span>
    <a href="index.php" class="btn btn-light">⬅ Kembali</a>
  </div>
</nav>

<div class="container mt-4">

<?php if (empty($_SESSION['keranjang'])) { ?>

  <div class="alert alert-warning text-center">
    Keranjang masih kosong
  </div>

<?php } else { ?>

<table class="table table-bordered table-striped align-middle">
<thead class="table-success">
<tr>
  <th>Produk</th>
  <th>Jenis</th>
  <th>Harga</th>
  <th>Jumlah</th>
  <th>Total</th>
  <th>Aksi</th>
</tr>
</thead>

<tbody>
<?php
$grand_total = 0;

foreach ($_SESSION['keranjang'] as $id => $item) {

    $nama   = $item['nama'] ?? '-';
    $harga  = $item['harga'] ?? 0;
    $jenis  = $item['jenis'] ?? '-';
    $jumlah = $item['jumlah'] ?? 1;

    $total = $harga * $jumlah;
    $grand_total += $total;
?>
<tr>
  <td><?= htmlspecialchars($nama); ?></td>

  <td>
    <span class="badge <?= $jenis=='sewa' ? 'bg-primary' : 'bg-success'; ?>">
      <?= strtoupper($jenis); ?>
    </span>
  </td>

  <td>Rp <?= number_format($harga); ?></td>

  <td>
    <?= $jumlah; ?>
    <?= $jenis=='sewa' ? 'hari' : 'pcs'; ?>
  </td>

  <td>Rp <?= number_format($total); ?></td>

  <td>
    <a href="keranjang.php?hapus=<?= $id; ?>"
       class="btn btn-danger btn-sm"
       onclick="return confirm('Hapus item ini?')">
       ❌ Hapus
    </a>
  </td>
</tr>
<?php } ?>
</tbody>

<tfoot>
<tr>
  <th colspan="4" class="text-end">Total Bayar</th>
  <th colspan="2">Rp <?= number_format($grand_total); ?></th>
</tr>
</tfoot>
</table>

<div class="text-end">
  <a href="checkout.php" class="btn btn-success">
    ✅ Checkout via WhatsApp
  </a>
</div>

<?php } ?>

</div>

<footer class="bg-light text-center p-3 mt-4">
© 2025 <b>Senandung Alam</b>
</footer>

</body>
</html>
