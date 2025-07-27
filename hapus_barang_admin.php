<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

require_once("connect.php");


$id = $_POST["id"];

$conn->query("DELETE FROM barang WHERE id = $id");

// Setelah menghapus, redirect balik ke halaman data barang
header("Location: data_barang_admin.php");
exit();
