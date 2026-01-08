<?php
session_start();
include 'koneksi.php';

// Inisialisasi keranjang
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Ambil data produk
$query = mysqli_query($koneksi, "SELECT * FROM produk");
if (!$query) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Senandung Alam</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f8f4;
}
.card {
    transition: 0.3s;
}
.card:hover {
    transform: translateY(-5px);
}
.card-img-top {
    height: 220px;
    object-fit: cover;
}
.badge-sewa {
    background-color: #0d6efd;
}
.badge-jual {
    background-color: #198754;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-success">
  <div class="container">
    <span class="navbar-brand fw-bold">🌿 Senandung Alam</span>
    <a href="keranjang.php" class="btn btn-light">
      🛒 Keranjang (<?= count($_SESSION['keranjang']); ?>)
    </a>
  </div>
</nav>

<!-- HEADER -->
<div class="container text-center my-4">
  <h2>Produk Alami & Sewa Peralatan</h2>
  <p class="text-muted">
    Sewa kamera, tenda, alat camping & beli produk alami Nusantara
  </p>
</div>

<!-- PRODUK -->
<div class="container mb-5">
  <div class="row g-4">

<?php while ($p = mysqli_fetch_assoc($query)) { ?>

  <div class="col-md-4">
    <div class="card shadow h-100">

      <!-- GAMBAR -->
      <img src="assets/img/<?= htmlspecialchars($p['gambar']); ?>"
           alt="<?= htmlspecialchars($p['nama_produk']); ?>"
           class="card-img-top"
           onerror="this.src='assets/img/no-image.jpg'">

      <div class="card-body d-flex flex-column">

        <!-- JENIS -->
        <span class="badge mb-2 <?= $p['jenis']=='sewa' ? 'badge-sewa' : 'badge-jual'; ?>">
          <?= strtoupper($p['jenis']); ?>
        </span>

        <h5 class="card-title"><?= htmlspecialchars($p['nama_produk']); ?></h5>

        <p class="card-text text-muted small">
          <?= htmlspecialchars($p['deskripsi']); ?>
        </p>

        <h6 class="text-success mb-3">
          Rp <?= number_format($p['harga']); ?>
          <?= $p['jenis']=='sewa' ? '/hari' : ''; ?>
        </h6>

        <!-- FORM TAMBAH KE KERANJANG -->
        <form method="post" action="keranjang.php" class="mt-auto">

          <input type="hidden" name="id" value="<?= $p['id']; ?>">
          <input type="hidden" name="nama" value="<?= htmlspecialchars($p['nama_produk']); ?>">
          <input type="hidden" name="harga" value="<?= $p['harga']; ?>">
          <input type="hidden" name="jenis" value="<?= $p['jenis']; ?>">

          <!-- INPUT HARI SEWA -->
          <?php if ($p['jenis'] == 'sewa') { ?>
            <div class="mb-2">
              <input type="number"
                     name="hari"
                     class="form-control"
                     min="1"
                     value="1"
                     required>
              <small class="text-muted">Jumlah hari sewa</small>
            </div>
          <?php } ?>

          <button type="submit" name="tambah" class="btn btn-success w-100">
            ➕ Tambah ke Keranjang
          </button>
        </form>

      </div>
    </div>
  </div>

<?php } ?>

  </div>
</div>

<!-- FOOTER -->
<footer class="bg-light text-center p-3">
  © 2025 <b>Senandung Alam</b> | Sewa & Produk Alami Nusantara
</footer>

</body>
</html>
