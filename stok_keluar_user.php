<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["role"] != "user") {
    header("Location: login.php");
    exit();
}

require_once("connect.php");

$pesan = "";

if (isset($_POST["submit"])) {
    $id_barang = $_POST["id_barang"];
    $jumlah = $_POST["jumlah"];
    $tanggal = $_POST["tanggal"];

    // Tanpa sanitasi (untuk latihan keamanan)
    mysqli_query($conn, "INSERT INTO stok_keluar (id_barang, jumlah, tanggal, user) 
                         VALUES ('$id_barang', '$jumlah', '$tanggal', '{$_SESSION["username"]}')");

    // Update stok langsung tanpa validasi
    mysqli_query($conn, "UPDATE barang SET stok = stok - $jumlah WHERE id = $id_barang");

    $pesan = "✅ Stok keluar berhasil dicatat!";
}

$barang = mysqli_query($conn, "SELECT * FROM barang");
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>📤 Input Stok Keluar</title>
    <style>
        /* Salin semua style dari stok_masuk_user.php */
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
        .pesan {
            margin-top: 1rem;
            color: #00ffae;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="hamburger" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</div>

<div class="sidebar" id="sidebar">
    <h2>👤 User: <?= $_SESSION["username"]; ?></h2>
    <ul>
        <li class="<?= $currentPage == 'dashboard_user.php' ? 'active' : '' ?>">🏠 <a href="dashboard_user.php">Dashboard</a></li>
        <li class="<?= $currentPage == 'data_barang_user.php' ? 'active' : '' ?>">📋 <a href="data_barang_user.php">Data Barang</a></li>
        <li class="<?= $currentPage == 'stok_masuk_user.php' ? 'active' : '' ?>">📥 <a href="stok_masuk_user.php">Stok Masuk</a></li>
        <li class="<?= $currentPage == 'stok_keluar_user.php' ? 'active' : '' ?>">📤 <a href="stok_keluar_user.php">Stok Keluar</a></li>
        <li>🔓 <a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="main">
    <h2>📤 Input Stok Keluar</h2>
    
    <form method="POST">
        <label for="id_barang">Pilih Barang:</label><br>
        <select name="id_barang" id="id_barang" required>
            <option value="">-- Pilih Barang --</option>
            <?php while($b = mysqli_fetch_assoc($barang)): ?>
                <option value="<?= $b['id'] ?>"><?= $b['nama'] ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="jumlah">Jumlah Keluar:</label><br>
        <input type="number" name="jumlah" id="jumlah" required><br><br>

        <label for="tanggal">Tanggal Keluar:</label><br>
        <input type="date" name="tanggal" id="tanggal" required><br><br>

        <button type="submit" name="submit" class="btn">Simpan</button>
    </form>

    <?php if ($pesan): ?>
        <div class="pesan"><?= $pesan ?></div>
    <?php endif; ?>
</div>

</body>
</html>
