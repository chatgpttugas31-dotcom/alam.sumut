<?php
session_start();
include '../config/koneksi.php';
if ($_SESSION['level'] != 'siswa') header("Location: ../login.php");
$id = $_SESSION['id'];
$q = $koneksi->query("SELECT * FROM pembayaran WHERE id_siswa=$id");
echo "<h2>Status Pembayaran</h2>";
while($row = $q->fetch_assoc()){
    echo "<p>Bulan: {$row['bulan']} - Status: {$row['status']}";
    if($row['status']=='lunas'){
        echo " - <img src='../uploads/{$row['bukti']}' width='200'>";
    }
    echo "</p>";
}
?>