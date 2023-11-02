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

if($_SESSION['role']=='pegawai'){
    header("Location: transaksi/view.php");
}

if($_SESSION['role']=='manajer'){
    header("Location: laporan/view.php");
}

?>