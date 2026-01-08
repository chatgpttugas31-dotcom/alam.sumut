<?php  
session_start();
if ($_SESSION['level'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

// Tambah siswa
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);

    // Password otomatis = NIS
    $password = $nis;  
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek NIS dan username
    $cek_nis = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis = '$nis'");
    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$nama'");

    if (mysqli_num_rows($cek_nis) > 0 || mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('NIS sudah terdaftar!'); window.location='kelola_siswa.php';</script>";
        exit;
    }

    // Tambah user (username = NIS, password = NIS)
    $insert_user = mysqli_query($koneksi, "INSERT INTO users (username, password, plain_password, level) 
                                           VALUES ('$nama', '$hashed_password', '$password', 'siswa')");
    if ($insert_user) {
        $id_user = mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO siswa (nama, nis, kelas, id_user) 
                                VALUES ('$nama', '$nis', '$kelas', '$id_user')");
        header("Location: kelola_siswa.php");
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan user!');</script>";
    }
}

// Edit siswa
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);

    // Update tabel siswa
    mysqli_query($koneksi, "UPDATE siswa SET nama='$nama', nis='$nis', kelas='$kelas' WHERE id='$id'");

    // Update username dan password di tabel users (password = NIS)
    $get_user = mysqli_query($koneksi, "SELECT id_user FROM siswa WHERE id='$id'");
    $data_user = mysqli_fetch_assoc($get_user);
    $id_user = $data_user['id_user'];

    $hashed_password = password_hash($nis, PASSWORD_DEFAULT);
    mysqli_query($koneksi, "UPDATE users SET username='$nis', password='$hashed_password', plain_password='$nis' WHERE id='$id_user'");

    header("Location: kelola_siswa.php");
    exit;
}

// Hapus siswa
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $get_user = mysqli_query($koneksi, "SELECT id_user FROM siswa WHERE id = '$id'");
    $data = mysqli_fetch_assoc($get_user);
    $id_user = $data['id_user'];
    mysqli_query($koneksi, "DELETE FROM siswa WHERE id = '$id'");
    mysqli_query($koneksi, "DELETE FROM users WHERE id = '$id_user'");
    header("Location: kelola_siswa.php");
    exit;
}

// Ambil semua kelas unik
$kelas_options = mysqli_query($koneksi, "SELECT DISTINCT kelas FROM siswa ORDER BY 
    CASE 
        WHEN kelas LIKE 'VII%' THEN 1
        WHEN kelas LIKE 'VIII%' THEN 2
        WHEN kelas LIKE 'IX%' THEN 3
        ELSE 4
    END, kelas ASC");

// Tangkap filter kelas & pencarian
$selected_kelas = $_GET['kelas'] ?? 'all';
$keyword = mysqli_real_escape_string($koneksi, $_GET['cari'] ?? '');

// Query siswa sesuai filter + pencarian
$query = "SELECT * FROM siswa WHERE 1=1";
if ($selected_kelas != 'all') {
    $query .= " AND kelas LIKE '$selected_kelas%'";
}
if (!empty($keyword)) {
    $query .= " AND (nama LIKE '%$keyword%' OR nis LIKE '%$keyword%')";
}
$query .= " ORDER BY 
    CASE 
        WHEN kelas LIKE 'VII%' THEN 1
        WHEN kelas LIKE 'VIII%' THEN 2
        WHEN kelas LIKE 'IX%' THEN 3
        ELSE 4
    END, kelas ASC, nama ASC";

$siswa = mysqli_query($koneksi, $query);

// Jika mode edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $edit_query = mysqli_query($koneksi, "SELECT * FROM siswa WHERE id='$id_edit'");
    $edit_data = mysqli_fetch_assoc($edit_query);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4 text-primary">Kelola Siswa</h2>

    <!-- Filter Kelas & Cari -->
    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="kelas" class="form-select" onchange="this.form.submit()">
                    <option value="all" <?= $selected_kelas=='all'?'selected':'' ?>>Semua Kelas</option>
                    <?php while($k = mysqli_fetch_assoc($kelas_options)): ?>
                        <option value="<?= $k['kelas'] ?>" <?= $selected_kelas==$k['kelas']?'selected':'' ?>>
                            <?= $k['kelas'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="cari" class="form-control" placeholder="Cari nama atau NIS..." 
                       value="<?= htmlspecialchars($keyword) ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">Cari</button>
            </div>
        </div>
    </form>

    <!-- Form tambah / edit siswa -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="POST" class="row g-3">
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
                <div class="col-md-3">
                    <input type="text" name="nama" class="form-control" placeholder="Nama Siswa" required
                           value="<?= $edit_data['nama'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <input type="text" name="nis" class="form-control" placeholder="NIS" required
                           value="<?= $edit_data['nis'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <input type="text" name="kelas" class="form-control" placeholder="Kelas (VII-A)" required
                           value="<?= $edit_data['kelas'] ?? '' ?>">
                </div>
                <?php if (!$edit_data): ?>
                <div class="col-md-2">
                    <button class="btn btn-success w-100" type="submit" name="tambah">Tambah</button>
                </div>
                <?php else: ?>
                <div class="col-md-2">
                    <button class="btn btn-warning w-100" type="submit" name="update">Update</button>
                </div>
                <div class="col-md-2">
                    <a href="kelola_siswa.php" class="btn btn-secondary w-100">Batal</a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Tabel data siswa -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                if (mysqli_num_rows($siswa) > 0):
                    $no = 1; 
                    while ($row = mysqli_fetch_assoc($siswa)): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['nis']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['kelas']) ?></td>
                            <td class="text-center">
                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Yakin ingin hapus siswa ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; 
                else: ?>
                    <tr><td colspan="5" class="text-center">Tidak ada data siswa</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<div class="logout mt-3">
    <a href="dashboard.php">← Kembali ke Dashboard</a>
</div>
</div>
</body>
</html>
