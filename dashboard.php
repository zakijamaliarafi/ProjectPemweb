<?php
include 'inc/koneksi.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

if($_SESSION['role']=='admin'){
    header("Location: barang/view.php");
}

?>