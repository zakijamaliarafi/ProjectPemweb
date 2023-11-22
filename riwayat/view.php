<?php
include "../inc/koneksi.php";
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat</title>
    <style>
        body{
            text-align: center;
        }
    </style>
</head>
<body>
    <?php
    if($_SESSION['role']=='pegawai'){
        echo "<p>
        <a href='../transaksi/view.php'>Transaksi</a>
        <a href='../riwayat/view.php'>Riwayat Transaksi</a>
        <a href='../barang/view.php'>Data Barang</a>
        <a href='../supplier/view.php'>Data Supplier</a>
        <a href='../inc/logout.php'>Log out</a>
    </p>";
    } else if($_SESSION['role']=='manajer'){
        echo "<p>
        <a href='../laporan/view.php'>Laporan</a>
        <a href='../riwayat/view.php'>Riwayat Transaksi</a>
        <a href='../pegawai/view.php'>Data Pegawai</a>
        <a href='../barang/view.php'>Data Barang</a>
        <a href='../supplier/view.php'>Data Supplier</a>
        <a href='../inc/logout.php'>Log out</a>
    </p>";
    } else {
        echo "<p>
        <a href='../laporan/view.php'>Laporan</a>
        <a href='../transaksi/view.php'>Transaksi</a>
        <a href='../riwayat/view.php'>Riwayat Transaksi</a>
        <a href='../pegawai/view.php'>Manajemen Pegawai</a>
        <a href='../barang/view.php'>Manajemen Barang</a>
        <a href='../supplier/view.php'>Manajemen Supplier</a>
        <a href='../inc/logout.php'>Log out</a>
    </p>";
    }
    ?>
    <h1>Riwayat Transaksi</h1>
    <table>
        <tr>
            <td>No</td>
            <td>Kasir</td>
            <td>Total Barang</td>
            <td>Total Harga</td>
            <td>Tanggal</td>
            <td></td>
        </tr>
        <?php
        $no = 1;
        $sql = "SELECT user.nama_user, SUM(detail_transaksi.jumlah_barang) AS jumlah_barang, transaksi.total_transaksi, transaksi.tgl_transaksi FROM `transaksi` INNER JOIN user ON user.id_user=transaksi.id_user INNER JOIN detail_transaksi ON detail_transaksi.id_transaksi=transaksi.id_transaksi WHERE transaksi.bayar IS NOT NULL GROUP BY transaksi.id_transaksi";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_array($query)){
            echo "
            <tr>
                <td>$no</td>
                <td>$row[nama_user]</td>
                <td>$row[jumlah_barang]</td>
                <td>$row[total_transaksi]</td>
                <td>$row[tgl_transaksi]</td>
                <td><button>Cetak Nota</button></td>
            </tr>
            ";
            $no++;
        }
        ?>
    </table>
</body>
</html>