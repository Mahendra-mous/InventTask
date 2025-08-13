<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}
require_once("connect.php");
$currentPage = basename($_SERVER['PHP_SELF']);
$nomer = 1;


if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: pengguna.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $conn->query("UPDATE users SET username='$username', role='$role' WHERE id=$id");
    header("Location: pengguna.php");
    exit();
}


$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$filter_sql = $keyword ? "WHERE username LIKE '%$keyword%' OR role LIKE '%$keyword%'" : "";
$users = $conn->query("SELECT * FROM users $filter_sql");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>👤 Kelola Pengguna</title>
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
            text-align: center;
            background-color: #1e1e1e;
            border-bottom: 1px solid #333;
        }

        th {
            background-color: #2b2b2b;
            color: #00fff7;
        }

        .btn {
            background-color: #00fff7;
            color: #000;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            margin-right: 4px;
        }

        .btn:hover {
            animation: pulse 1s infinite;
        }

        .search-box {
            margin-top: 1rem;
        }

        .form-edit {
            margin-top: 2rem;
            padding: 1rem;
            background-color: #2b2b2b;
            border-radius: 6px;
            box-shadow: 0 0 10px #00fff7;
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
    <h1>👤 Kelola Pengguna</h1>

    <form method="GET" class="search-box">
        <input type="text" name="keyword" placeholder="Cari username atau role..." value="<?= $keyword ?>" />
        <button class="btn" type="submit">🔍 Cari</button>
        <a href="pengguna.php" class="btn">🔄 Reset</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $nomer++ ?></td>
                <td><?= $row['username'] ?></td>
                <td><?= $row['role'] ?></td>
                <td>
                    <a href="?edit=<?= $row['id'] ?>" class="btn">✏️ Edit</a>
                    <a href="?hapus=<?= $row['id'] ?>" class="btn" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">🗑️ Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Form Edit -->
    <?php if (isset($_GET['edit'])):
        $edit_id = $_GET['edit'];
        $edit_user = $conn->query("SELECT * FROM users WHERE id = $edit_id")->fetch_assoc();
    ?>
    <div class="form-edit">
        <h2>✏️ Edit Pengguna</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
            <label>Username:</label><br>
            <input type="text" name="username" value="<?= $edit_user['username'] ?>" required><br><br>
            <label>Role:</label><br>
            <select name="role" required>
                <option value="admin" <?= $edit_user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="user" <?= $edit_user['role'] == 'user' ? 'selected' : '' ?>>User</option>
            </select><br><br>
            <button type="submit" name="update_user" class="btn">💾 Simpan Perubahan</button>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>
    function toggleMenu() {
        document.getElementById('sidebar').classList.toggle('open');
    }
</script>

</body>
</html>
