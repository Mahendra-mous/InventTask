<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}
require_once("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST["nama"];
    $stok = $_POST["stok"];

    // Proses upload gambar
    $gambar = '';
    if (isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] == 0) {
        $target_dir = "uploads/";
        $filename = basename($_FILES["gambar"]["name"]);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $unique_name = uniqid() . "." . $ext;
        $target_file = $target_dir . $unique_name;

        // Simpan file
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            $gambar = $unique_name;
        }
    }

    // Simpan ke database
    $sql = "INSERT INTO barang (nama, stok, gambar) VALUES ('$nama', '$stok', '$gambar')";
    $conn->query($sql);

    header("Location: data_barang_admin.php");
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>➕ Tambah Barang</title>
    <style>
        body {
            margin: 0;
            display: flex;
            background-color: #121212;
            color: #e0e0e0;
            height: 100vh;
            overflow: hidden;
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
            position: relative;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 6px #00fff7;
            }
            50% {
                box-shadow: 0 0 16px #00fff7;
            }
            100% {
                box-shadow: 0 0 6px #00fff7;
            }
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

        .main h2 {
            color: #00fff7;
        }

        .btn {
            background-color: #00fff7;
            color: #000;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0 0 8px #00fff7;
            transition: 0.3s ease;
            margin-top: 10px;
            display: inline-block;
        }

        .btn:hover {
            transform: scale(1.05);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 400px;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #1e1e1e;
            color: #e0e0e0;
            box-shadow: 0 0 8px #00fff7;
        }

        input[type="file"] {
            background-color: #292929;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                transform: translateX(-100%);
                transition: transform 0.3s;
                z-index: 5;
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

<div class="hamburger" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</div>

<div class="sidebar" id="sidebar">
    <h2>👤 Admin: <?= $_SESSION["username"]; ?></h2>
    <ul>
        <li class="<?= $currentPage == 'dashboard_admin.php' ? 'active' : '' ?>">🏠 <a href="dashboard_admin.php">Dashboard</a></li>
        <li class="<?= $currentPage == 'data_barang_admin.php' ? 'active' : '' ?>">📦 <a href="data_barang_admin.php">Data Barang</a></li>
        <li class="<?= $currentPage == 'stok_masuk_admin.php' ? 'active' : '' ?>">📥 <a href="stok_masuk_admin.php">Stok Masuk</a></li>
        <li class="<?= $currentPage == 'stok_keluar_admin.php' ? 'active' : '' ?>">📤 <a href="stok_keluar_admin.php">Stok Keluar</a></li>
        <li class="<?= $currentPage == 'laporan_admin.php' ? 'active' : '' ?>">📊 <a href="laporan_admin.php">Laporan Inventaris</a></li>
        <li class="<?= $currentPage == 'pengguna.php' ? 'active' : '' ?>">👤 <a href="pengguna.php">Kelola Pengguna</a></li>
        <li>🔓 <a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="main">
    <h2>➕ Tambah Barang Baru</h2>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="nama" placeholder="Nama Barang" required>
        <input type="number" name="stok" placeholder="Stok Awal" required>
        <input type="file" name="gambar" accept="image/*" required>
        <button type="submit" class="btn">Simpan</button>
    </form>

    <br><a href="data_barang_admin.php" class="btn">⬅️ Kembali</a>
</div>

</body>
</html>
