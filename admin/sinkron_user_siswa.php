<?php
include '../config/koneksi.php';

$query = "
    SELECT u.id AS id_user, u.username 
    FROM users u 
    LEFT JOIN siswa s ON u.id = s.id_user 
    WHERE u.level = 'siswa' AND s.id_user IS NULL
";

$result = mysqli_query($koneksi, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $id_user = $row['id_user'];
    $username = $row['username'];

    // Simpan ke tabel siswa dengan nama = username dan data dummy
    $insert = mysqli_query($koneksi, "INSERT INTO siswa (id_user, nama, nis, kelas) 
              VALUES ('$id_user', '$username', '0', '-')");
    
    if ($insert) {
        echo "Berhasil tambah siswa dari user: $username<br>";
    } else {
        echo "Gagal tambah siswa dari user: $username<br>";
    }
}

echo "<br>Selesai.";
?>
