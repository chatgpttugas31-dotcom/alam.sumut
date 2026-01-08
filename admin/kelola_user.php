<?php
session_start();
if ($_SESSION['level'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

// Tambah user (khusus admin)
if (isset($_POST['tambah'])) {
    $level = 'admin';
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah username sudah ada
    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('Username sudah digunakan!'); window.location='kelola_user.php';</script>";
        exit;
    }

    mysqli_query($koneksi, "INSERT INTO users (username, password, plain_password, level) 
                            VALUES ('$username', '$hashed_password', '$password', '$level')");
    header("Location: kelola_user.php");
    exit;
}

// Update user
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $level = mysqli_real_escape_string($koneksi, $_POST['level']);

    if (!empty($_POST['password'])) {
        $password = mysqli_real_escape_string($koneksi, $_POST['password']);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        mysqli_query($koneksi, "UPDATE users SET 
            username='$username', 
            password='$hashed_password', 
            plain_password='$password', 
            level='$level' 
            WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "UPDATE users SET 
            username='$username', 
            level='$level' 
            WHERE id='$id'");
    }

    header("Location: kelola_user.php");
    exit;
}

// Hapus user (dan jika siswa, hapus juga di tabel siswa)
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM siswa WHERE id_user = '$id'");
    mysqli_query($koneksi, "DELETE FROM users WHERE id = '$id'");
    header("Location: kelola_user.php");
    exit;
}

// Ambil data user yang ingin diedit
$edit_mode = false;
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit'];
    $result = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$edit_id'");
    $edit_data = mysqli_fetch_assoc($result);
}

// Ambil semua data user
$users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #666; padding: 8px; text-align: center; }
        h2 { color: #333; }
        form { margin-top: 20px; }
        input, select { padding: 5px; margin: 5px; width: 200px; }
        .btn { padding: 5px 10px; background: #4e54c8; color: #fff; border: none; cursor: pointer; }
        .btn:hover { background: #3a3ea0; }
    </style>
</head>
<body>
    <h2>Kelola User</h2>

    <form method="POST">
        <?php if ($edit_mode): ?>
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <input type="text" name="username" placeholder="Username" value="<?= $edit_data['username'] ?>" required>
            <input type="password" name="password" placeholder="Password (biarkan kosong jika tidak diubah)">
            <select name="level" required>
                <option value="admin" <?= $edit_data['level'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="siswa" <?= $edit_data['level'] == 'siswa' ? 'selected' : '' ?>>Siswa</option>
            </select>
            <button class="btn" type="submit" name="update">Simpan Perubahan</button>
            <a class="btn" href="kelola_user.php" style="background:#888;">Batal</a>
        <?php else: ?>
            <input type="text" name="username" placeholder="Username Admin" required>
            <input type="password" name="password" placeholder="Password Admin" required>
            <button class="btn" type="submit" name="tambah">Tambah Admin</button>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username (Nama Siswa/Admin)</th>
                <th>Password</th>
                <th>Level</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['plain_password']) ?></td>
                    <td><?= $row['level'] ?></td>
                    <td>
                        <a href="?edit=<?= $row['id'] ?>">Edit</a> | 
                        <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin hapus user ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <br>
    <a href="dashboard.php">← Kembali ke Dashboard</a>
</body>
</html>
