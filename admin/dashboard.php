<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['level'])) {
    header("Location: ../login.php");
    exit;
}

// Jika siswa login, arahkan ke dashboard siswa
if ($_SESSION['level'] === 'siswa') {
    header("Location: ../siswa/dashboard_siswa.php");
    exit;
}

// Jika bukan admin (misalnya level tidak dikenal), kembalikan ke login
if ($_SESSION['level'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil nama admin dari session (jika tersedia)
$nama_admin = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SPP SMP Kartini</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        /* RESET */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            color: #2c3e50;
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background-color: #2c3e50;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 30px 20px;
            align-items: center;
        }

        .sidebar img {
            width: 100px;
            margin-bottom: 15px;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 30px;
            font-weight: 600;
            text-align: center;
        }

        .nav-link {
            text-decoration: none;
            color: #b0e2eeff;
            display: flex;
            align-items: center;
            padding: 12px 18px;
            margin-bottom: 12px;
            border-radius: 8px;
            transition: background 0.2s ease;
            width: 100%;
        }

        .nav-link:hover {
            background-color: #34495e;
        }

        .nav-link i {
            margin-right: 15px;
            font-size: 18px;
        }

        /* MAIN AREA */
        .main {
            flex: 1;
            background-image: url('../assets/logo_sekolah.jpeg');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .overlay {
            background-color: rgba(146, 141, 141, 0.5);
            height: 100%;
            width: 100%;
            padding: 40px;
        }

        .main h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .main p {
            font-size: 16px;
            color: #333;
            margin-bottom: 30px;
        }

        /* DASHBOARD CARDS */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.8);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #2c3e50;
            opacity: 0;
            animation: fadeSlideUp 0.6s ease forwards;
            transition: transform 0.3s ease, background 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.95);
        }

        .card i {
            font-size: 30px;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .card span {
            font-size: 16px;
            font-weight: 600;
            text-align: center;
        }

        .dashboard-cards a.card:nth-child(1) { animation-delay: 0.1s; }
        .dashboard-cards a.card:nth-child(2) { animation-delay: 0.2s; }
        .dashboard-cards a.card:nth-child(3) { animation-delay: 0.3s; }
        .dashboard-cards a.card:nth-child(4) { animation-delay: 0.4s; }
        .dashboard-cards a.card:nth-child(5) { animation-delay: 0.5s; }

        @keyframes fadeSlideUp {
            0% {
                opacity: 0;
                transform: translateY(60px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* LOGOUT */
        .logout-btn {
            margin-top: auto;
            text-align: center;
        }

        .logout-btn a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #e74c3c;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .logout-btn a:hover {
            background-color: #c0392b;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                flex-direction: row;
                overflow-x: auto;
                padding: 10px;
                justify-content: space-around;
            }

            .sidebar h2, .logout-btn {
                display: none;
            }

            .main {
                padding: 20px;
            }

            .dashboard-cards {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <img src="../assets/logo.png" alt="Logo SMP Kartini Parsoburan">
        <h2>Admin Panel</h2>

        <a href="kelola_siswa.php" class="nav-link"><i class="fas fa-user-graduate"></i> Kelola Siswa</a>
        <a href="kelola_spp.php" class="nav-link"><i class="fas fa-money-bill-wave"></i> Kelola SPP</a>
        <a href="verifikasi_pembayaran.php" class="nav-link"><i class="fas fa-clipboard-check"></i> Verifikasi</a>
        <a href="kelola_user.php" class="nav-link"><i class="fas fa-users-cog"></i> Kelola User</a>
        <a href="laporan_pembayaran.php" class="nav-link"><i class="fas fa-file-alt"></i> Laporan</a>

        <div class="logout-btn">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main">
        <div class="overlay">
            <h1>Selamat Datang, <?= $nama_admin; ?></h1>
            <p>Kelola dan pantau semua data pembayaran SPP SMP Kartini dengan mudah dan cepat.</p>

            <div class="dashboard-cards">
                <a href="kelola_siswa.php" class="card">
                    <i class="fas fa-user-graduate"></i>
                    <span>Data Siswa</span>
                </a>
                <a href="kelola_spp.php" class="card">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Data SPP</span>
                </a>
                <a href="verifikasi_pembayaran.php" class="card">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Verifikasi</span>
                </a>
                <a href="kelola_user.php" class="card">
                    <i class="fas fa-users-cog"></i>
                    <span>Kelola User</span>
                </a>
                <a href="laporan_pembayaran.php" class="card">
                    <i class="fas fa-file-alt"></i>
                    <span>Laporan</span>
                </a>
            </div>
        </div>
    </main>

</body>
</html>
