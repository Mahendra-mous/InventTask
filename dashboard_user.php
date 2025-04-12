<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["role"] != "user") {
    header("Location: login.php");
    exit();
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <style>
        body {
            margin: 0;
            display: flex;
            background-color: #121212;
            color: #e0e0e0;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            background-color: #1e1e1e;
            width: 250px;
            height: 100%;
            padding: 2rem 1rem;
            box-shadow: 0 0 10px #00fff7;
        }

        .sidebar h2 {
            margin-bottom: 2rem;
            font-size: 20px;
            color: #00fff7;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar li {
            margin: 1rem 0;
        }

        .sidebar li.active a {
            background-color: #00fff7;
            color: #000;
            box-shadow: inset 4px 0 0 #00fff7, 0 0 12px #00fff7;
            font-weight: bold;
            transform: scale(1.05);
        }

        .sidebar a {
            color: #00fff7;
            text-decoration: none;
            font-size: 1rem;
            padding: 8px 12px;
            display: inline-block;
            border-radius: 6px;
            transition: all 0.3s ease;
            position: relative;
        }

        .sidebar a:hover {
            color: #000;
            background-color: #00fff7;
            box-shadow: 0 0 8px #00fff7, 0 0 16px #00fff7, 0 0 32px #00fff7;
            transform: scale(1.05);
            animation: pulse 0.8s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 6px #00fff7; }
            50% { box-shadow: 0 0 16px #00fff7; }
            100% { box-shadow: 0 0 6px #00fff7; }
        }

        .main {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        .hamburger {
            display: none;
            position: absolute;
            top: 1rem;
            left: 1rem;
            font-size: 24px;
            color: #00fff7;
            cursor: pointer;
            z-index: 10;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                transform: translateX(-100%);
                z-index: 5;
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .hamburger {
                display: block;
            }
        }
    </style>
</head>
<body>

    <div class="hamburger" onclick="toggleMenu()">☰</div>

    <div class="sidebar" id="sidebar">
        <h2>👤 User: <?= $_SESSION["username"]; ?></h2>
        <ul>
            <li class="<?= $currentPage == 'dashboard_user.php' ? 'active' : '' ?>">🏠 <a href="dashboard_user.php">Dashboard</a></li>
            <li class="<?= $currentPage == 'data_barang_user.php' ? 'active' : '' ?>">📋 <a href="data_barang_user.php">Lihat Barang</a></li>
            <li class="<?= $currentPage == 'input_masuk.php' ? 'active' : '' ?>">📥 <a href="user/input_masuk.php">Input Stok Masuk</a></li>
            <li class="<?= $currentPage == 'input_keluar.php' ? 'active' : '' ?>">📤 <a href="user/input_keluar.php">Input Stok Keluar</a></li>
            <li>🔓 <a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <h1>📊 Dashboard User</h1>
        <p>Selamat datang, <strong><?= $_SESSION["username"]; ?></strong>! Silakan pilih menu dari sidebar untuk mulai menggunakan sistem inventaris.</p>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('open');
        }
    </script>
</body>
</html>
