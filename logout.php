<?php
session_start();

// Celah: tidak menghancurkan seluruh session dengan benar
unset($_SESSION["username"]);
unset($_SESSION["role"]);

header("Location: login.php");
exit();
