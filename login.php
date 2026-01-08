<?php 
session_start();
include 'config/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Ambil user berdasarkan username
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Cek apakah password cocok langsung (akun lama)
        if ($password === $user['password'] || password_verify($password, $user['password'])) {
            // Login berhasil
            $_SESSION['id'] = $user['id'];
            $_SESSION['level'] = $user['level'];

            if ($user['level'] == 'admin') {
                header("Location: admin/dashboard.php");
                exit;
            } elseif ($user['level'] == 'siswa') {
                header("Location: siswa/dashboard.php");
                exit;
            }
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SPP SMP Kartini</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            width: 350px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .login-box label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .login-box input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: 0.3s;
        }

        .login-box input:focus {
            border-color: #4e54c8;
            outline: none;
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            background: #4e54c8;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-box button:hover {
            background: #3c40a2;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        .login-icon {
            text-align: center;
            font-size: 40px;
            color: #4e54c8;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-icon">
            <i class="fas fa-user-lock"></i>
        </div>
        <h2>Login SPP SMP Kartini</h2>
        <?php if ($error) echo "<div class='error-message'>$error</div>"; ?>
        <form method="post" action="">
            <label><i class="fas fa-user"></i> Username</label>
            <input type="text" name="username" required>

            <label><i class="fas fa-lock"></i> Password</label>
            <input type="password" name="password" required>

            <button type="submit"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
    </div>
</body>
</html>
