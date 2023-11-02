<?php
include "../inc/koneksi.php";
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

if($_SESSION['role']!='manajer'){
    header("Location: ../routing.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan</title>
    <style>
        body{
            text-align: center;
        }
    </style>
</head>
<body>
    <p>
        <a href="view.php">Laporan</a>
        <a href="../inc/logout.php">Log out</a>
    </p>
    <h1>Laporan</h1>
</body>
</html>