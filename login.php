<?php
session_start();
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["username"]) && isset($_GET["password"])) {
    $username = $_GET["username"];
    $password = $_GET["password"];

    // Celah: SQL Injection karena input langsung dimasukkan ke query
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];

        if ($user["role"] == "admin") {
            header("Location: dashboard_admin.php");
        } else {
            header("Location: dashboard_user.php");
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
     <style>
        .container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background-color: #111;
            border-radius: 10px;
            box-shadow: 0 0 15px #00fff7;
            color: #fff;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
            background-color: #222;
            color: white;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #00fff7;
            color: #000;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            animation: pulse 2s infinite;
        }

        .success {
            text-align: center;
            padding: 15px;
            margin-top: 15px;
            color: #00ff88;
            font-weight: bold;
            background-color: #1d1d1d;
            border-radius: 8px;
            box-shadow: 0 0 10px #00ff88;
            animation: fadeInGlow 1.5s ease-in-out;
        }

        @keyframes fadeInGlow {
            0% {
                opacity: 0;
                transform: scale(0.95);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0px #00fff7; }
            50% { box-shadow: 0 0 10px #00fff7; }
            100% { box-shadow: 0 0 0px #00fff7; }
        }

        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .line {
            position: absolute;
            width: 3px;
            height: 100%;
            background: linear-gradient(180deg, transparent, #00fff7, transparent);
            animation: moveLine 5s linear infinite;
            opacity: 0.4;
        }

        @keyframes moveLine {
            0% {
                transform: translateY(-100%);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                transform: translateY(100%);
                opacity: 0;
            }
        }

    </style>
</head>
<body>
    <div class="background-animation">
        <?php for ($i = 0; $i < 30; $i++): ?>
            <div class="line" style="left: <?= rand(0, 100) ?>vw; animation-delay: <?= rand(0, 5000) / 1000 ?>s;"></div>
        <?php endfor; ?>
    </div>
    <div class="container">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="GET" action="login.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Masuk</button>
        </form>
        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </div>
</body>
</html>
