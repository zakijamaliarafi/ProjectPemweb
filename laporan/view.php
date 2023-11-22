<?php
include "../inc/koneksi.php";
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

if($_SESSION['role']=='pegawai'){
    header("Location: ../routing.php");
    exit();
}

if(isset($_POST['input'])){
    $_SESSION['awal'] = $_POST['tanggal_mulai'];
    $_SESSION['akhir'] = $_POST['tanggal_akhir'];
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
    <?php
    if($_SESSION['role']=='manajer'){
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
    <h1>Laporan</h1>
    <form name="input" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
        <label for="tanggal_mulai">Tanggal mulai:</label>
        <input type="date" id="tanggal_mulai" name="tanggal_mulai">
        <label for="tanggal_akhir">Tanggal akhir:</label>
        <input type="date" id="tanggal_akhir" name="tanggal_akhir">
        <input type="submit" name="input" value="Submit">
    </form>
    <table>
        <?php
        if(isset($_SESSION['awal']) && isset($_SESSION['akhir'])){
            $select = "SELECT DAYOFMONTH(transaksi.tgl_transaksi) as tanggal, SUM((barang.harga_jual - barang.harga_beli)*detail_transaksi.jumlah_barang) as laba FROM `detail_transaksi` INNER JOIN `barang` ON barang.id_barang=detail_transaksi.id_barang INNER JOIN `transaksi` ON transaksi.id_transaksi=detail_transaksi.id_transaksi WHERE DATE(transaksi.tgl_transaksi) BETWEEN '$_SESSION[awal]' AND '$_SESSION[akhir]' GROUP BY DAYOFMONTH(transaksi.tgl_transaksi)";
            $query = mysqli_query($conn, $select);
            echo "
            <tr>
                <td>Tanggal</td>
                <td>Total laba</td>
            </tr>
            ";
            while($row = mysqli_fetch_array($query)){
                echo "
                <tr>
                    <td>$row[tanggal]</td>
                    <td>Rp. $row[laba]</td>
                </tr>
                ";
            }
        }
        ?>
    </table>
    <table>
        <?php
        if(isset($_SESSION['awal']) && isset($_SESSION['akhir'])){
            $select = "SELECT DAYOFMONTH(tgl_transaksi) as tanggal, SUM(total_transaksi) as omset FROM `transaksi` WHERE DATE(tgl_transaksi) BETWEEN '$_SESSION[awal]' AND '$_SESSION[akhir]' GROUP BY DAYOFMONTH(tgl_transaksi)";
            $query = mysqli_query($conn, $select);
            echo "
            <tr>
                <td>Tanggal</td>
                <td>Total omset</td>
            </tr>
            ";
            while($row = mysqli_fetch_array($query)){
                echo "
                <tr>
                    <td>$row[tanggal]</td>
                    <td>Rp. $row[omset]</td>
                </tr>
                ";
            }
        }
        ?>
    </table>
</body>
</html>