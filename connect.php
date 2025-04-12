<?php
// connect.php

$host = "localhost";
$user = "root";
$pass = ""; //Kosong karena default dari xampp
$db   = "inventory";

$conn = new mysqli($host, $user, $pass, $db);

// Celah keamanan: menampilkan error DB secara langsung (leak informasi)
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
