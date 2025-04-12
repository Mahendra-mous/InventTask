<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}
require_once("connect.php");

$currentPage = basename($_SERVER['PHP_SELF']);

// Tangkap filter dari GET (tidak disanitasi)
$filter = isset($_GET['filter']) ? $_GET['filter'] : "";
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : "";
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : "";

// Jika tombol Export ditekan
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=laporan_inventaris.csv');

    $output = fopen("php://output", "w");
    fputcsv($output, ['ID', 'Nama Barang', 'Total Masuk', 'Total Keluar', 'Stok Saat Ini']);

    $q = "
        SELECT b.id, b.nama, b.stok,
            COALESCE(SUM(sm.jumlah), 0) AS total_masuk,
            COALESCE(SUM(sk.jumlah), 0) AS total_keluar
        FROM barang b
        LEFT JOIN stok_masuk sm ON b.id = sm.barang_id
        LEFT JOIN stok_keluar sk ON b.id = sk.barang_id
        WHERE b.nama LIKE '%$filter%'";

    if (!empty($tgl_awal) && !empty($tgl_akhir)) {
        $q .= " AND sm.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
                AND sk.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
    }

    $q .= " GROUP BY b.id, b.nama, b.stok";

    $res = $conn->query($q);
    while ($row = $res->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['nama'],
            $row['total_masuk'],
            $row['total_keluar'],
            $row['stok']
        ]);
    }

    fclose($output);
    exit();
}

// Query utama
$query = "
    SELECT b.id, b.nama, b.stok,
        COALESCE(SUM(sm.jumlah), 0) AS total_masuk,
        COALESCE(SUM(sk.jumlah), 0) AS total_keluar
    FROM barang b
    LEFT JOIN stok_masuk sm ON b.id = sm.barang_id
    LEFT JOIN stok_keluar sk ON b.id = sk.barang_id
    WHERE b.nama LIKE '%$filter%'
";

if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $query .= " AND sm.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
                AND sk.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$query .= " GROUP BY b.id, b.nama, b.stok";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>📊 Laporan Inventaris</title>
    <style>
        body {
            margin: 0;
            display: flex;
            background-color: #121212;
            color: #e0e0e0;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
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


        /* Main content */
        .main {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        /* Hamburger icon */
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

        /* Mobile */
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        th, td {
            padding: 10px;
            background-color: #1e1e1e;
            border-bottom: 1px solid #333;
            color: #eee;
        }

        th {
            background-color: #2b2b2b;
            color: #00fff7;
        }

        input[type="text"], input[type="date"] {
            padding: 8px;
            border: none;
            border-radius: 4px;
            background-color: #2a2a2a;
            color: #fff;
        }

        .btn {
            background-color: #00fff7;
            color: #000;
            border: none;
            padding: 10px 16px;
            margin-left: 10px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }

        .btn:hover {
            animation: pulse 1s infinite;
        }

        form {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .filter-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="hamburger" onclick="toggleMenu()">☰</div>

    <div class="sidebar" id="sidebar">
        <h2>👤 Admin: <?= $_SESSION["username"]; ?></h2>
        <ul>
            <li class="<?= $currentPage == 'dashboard_admin.php' ? 'active' : '' ?>">🏠 <a href="dashboard_admin.php">Dashboard</a></li>
            <li class="<?= $currentPage == 'data_barang_admin.php' ? 'active' : '' ?>">📦 <a href="data_barang_admin.php">Data Barang</a></li>
            <li class="<?= $currentPage == 'stok_masuk_admin.php' ? 'active' : '' ?>">📥 <a href="stok_masuk_admin.php">Stok Masuk</a></li>
            <li class="<?= $currentPage == 'stok_keluar_admin.php' ? 'active' : '' ?>">📤 <a href="stok_keluar_admin.php">Stok Keluar</a></li>
            <li class="<?= $currentPage == 'laporan_admin.php' ? 'active' : '' ?>">📊 <a href="laporan_admin.php">Laporan Inventaris</a></li>
            <li class="<?= $currentPage == 'pengguna.php' ? 'active' : '' ?>">👤 <a href="pengguna.php">Kelola Pengguna</a></li>
            <li class="<?= $currentPage == 'logout.php' ? 'active' : '' ?>">🔓 <a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <h1>📊 Laporan Inventaris</h1>

        <form method="GET">
            <div class="filter-group">
                <label>🔍 Nama Barang:</label>
                <input type="text" name="filter" value="<?= $filter ?>">

                <label>📅 Tanggal Awal:</label>
                <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>">

                <label>📅 Tanggal Akhir:</label>
                <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>">

                <button type="submit" class="btn">Filter</button>
                <button type="submit" name="export" value="1" class="btn">⬇️ Export CSV</button>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Total Masuk</th>
                    <th>Total Keluar</th>
                    <th>Stok Saat Ini</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row["id"] ?></td>
                        <td><?= $row["nama"] ?></td>
                        <td><?= $row["total_masuk"] ?></td>
                        <td><?= $row["total_keluar"] ?></td>
                        <td><?= $row["stok"] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('open');
        }
    </script>
</body>
</html>
