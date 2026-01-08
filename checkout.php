<?php
session_start();

if (empty($_SESSION['keranjang'])) {
    header("Location: index.php");
    exit;
}

$total = 0;
$pesan = "Halo Senandung Alam,\n\nSaya ingin memesan:\n";

foreach ($_SESSION['keranjang'] as $item) {

    $subtotal = $item['harga'] * $item['jumlah'];
    $total += $subtotal;

    if ($item['jenis'] == 'sewa') {
        $pesan .= "- {$item['nama']} ({$item['jumlah']} hari) : Rp "
                . number_format($subtotal) . "\n";
    } else {
        $pesan .= "- {$item['nama']} ({$item['jumlah']} pcs) : Rp "
                . number_format($subtotal) . "\n";
    }
}

$pesan .= "\nTotal Bayar: Rp " . number_format($total);
$pesan .= "\n\nTerima kasih.";

/* =========================
   NOMOR WA (TANPA + & 0)
   ========================= */
$no_wa = "6281274806494";

$link = "https://wa.me/" . $no_wa . "?text=" . urlencode($pesan);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Checkout WhatsApp</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5 text-center">
  <h3>Checkout</h3>
  <p>Klik tombol di bawah untuk melanjutkan ke WhatsApp</p>

  <a href="<?= $link ?>" target="_blank" class="btn btn-success btn-lg">
    💬 Checkout via WhatsApp
  </a>

  <div class="mt-3">
    <a href="keranjang.php" class="btn btn-secondary">⬅ Kembali</a>
  </div>
</div>

</body>
</html>
