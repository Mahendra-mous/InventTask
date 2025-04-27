<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["role"] != "user") {
    header("Location: login.php");
    exit();
}

require_once("connect.php");
$currentPage = basename($_SERVER['PHP_SELF']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barang_id = $_POST["barang_id"];
    $jumlah = $_POST["jumlah"];
    $tanggal = $_POST["tanggal"];

    // Versi tidak aman: tidak ada validasi atau sanitasi input
    $conn->query("INSERT INTO stok_keluar (barang_id, jumlah, tanggal) VALUES ('$barang_id', '$jumlah', '$tanggal')");

    // Update stok barang
    $conn->query("UPDATE barang SET stok = stok - $jumlah WHERE id = $barang_id");

    header("Location: stok_keluar_user.php");
    exit();
}

$barang = $conn->query("SELECT * FROM barang");

$riwayat = $conn->query("
    SELECT sk.*, b.nama AS nama_barang 
    FROM stok_keluar sk 
    JOIN barang b ON sk.barang_id = b.id 
    ORDER BY sk.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>📤 Stok Keluar</title>
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
            transition: transform 0.3s ease;
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
            box-shadow: inset 4px 0 0 #00fff7,
                        0 0 12px #00fff7;
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
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 6px #00fff7; }
            50% { box-shadow: 0 0 16px #00fff7; }
            100% { box-shadow: 0 0 6px #00fff7; }
        }

        .sidebar a:hover {
            color: #000;
            background-color: #00fff7;
            box-shadow: 0 0 8px #00fff7,
                        0 0 16px #00fff7,
                        0 0 32px #00fff7;
            transform: scale(1.05);
            animation: pulse 0.8s infinite;
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
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .hamburger {
                display: block;
            }
        }

        input, select {
            padding: 8px;
            background-color: #1e1e1e;
            color: #00fff7;
            border: 1px solid #00fff7;
            border-radius: 4px;
            width: 100%;
        }

        label {
            margin-top: 1rem;
            display: block;
        }

        .btn {
            background-color: #00fff7;
            color: #000;
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn:hover {
            animation: pulse 1s infinite;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        th, td {
            padding: 10px;
            text-align: center;
            background-color: #1e1e1e;
            border-bottom: 1px solid #333;
        }

        th {
            background-color: #2b2b2b;
            color: #00fff7;
        }
    </style>
</head>
<body>
    <div class="hamburger" onclick="toggleMenu()">☰</div>

    <div class="sidebar" id="sidebar">
        <h2>👤 Admin: <?= $_SESSION["username"]; ?></h2>
        <ul>
            <li class="<?= $currentPage == 'dashboard_user.php' ? 'active' : '' ?>">🏠 <a href="dashboard_user.php">Dashboard</a></li>
            <li class="<?= $currentPage == 'data_barang_user.php' ? 'active' : '' ?>">📋 <a href="data_barang_user.php">Data Barang</a></li>
            <li class="<?= $currentPage == 'stok_masuk_user.php' ? 'active' : '' ?>">📥 <a href="stok_masuk_user.php">Stok Masuk</a></li>
            <li class="<?= $currentPage == 'stok_keluar_user.php' ? 'active' : '' ?>">📤 <a href="stok_keluar_user.php">Stok Keluar</a></li>
            <li class="<?= $currentPage == 'logout.php' ? 'active' : '' ?>">🔓 <a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <h1>📤 Stok Keluar</h1>

        <form method="POST">
            <label>Pilih Barang:</label><br>
            <select name="barang_id" required>
                <option value="">-- Pilih Barang --</option>
                <?php while($row = $barang->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
                <?php endwhile; ?>
            </select><br><br>

            <label>Jumlah Keluar:</label><br>
            <input type="number" name="jumlah" required><br><br>

            <label>Tanggal Keluar:</label><br>
            <input type="date" name="tanggal" required><br><br>

            <button type="submit" class="btn">Simpan</button>
        </form>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('open');
        }
    </script>
</body>
</html>
