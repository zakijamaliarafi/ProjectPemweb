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
        <a href="<?php $_SERVER['PHP_SELF']; ?>">Laporan</a>
        <a href="../inc/logout.php">Log out</a>
    </p>
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
        if(isset($_POST['input'])){
            $awal = $_POST['tanggal_mulai'];
            $akhir = $_POST['tanggal_akhir'];

            $select = "SELECT DAYOFMONTH(transaksi.tgl_transaksi) as tanggal, SUM((barang.harga_jual - barang.harga_beli)*detail_transaksi.jumlah_barang) as laba FROM `detail_transaksi` INNER JOIN `barang` ON barang.id_barang=detail_transaksi.id_barang INNER JOIN `transaksi` ON transaksi.id_transaksi=detail_transaksi.id_transaksi WHERE DATE(transaksi.tgl_transaksi) BETWEEN '$awal' AND '$akhir' GROUP BY DAYOFMONTH(transaksi.tgl_transaksi)";
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
        if(isset($_POST['input'])){
            $awal = $_POST['tanggal_mulai'];
            $akhir = $_POST['tanggal_akhir'];

            $select = "SELECT DAYOFMONTH(tgl_transaksi) as tanggal, SUM(total_transaksi) as omset FROM `transaksi` WHERE DATE(tgl_transaksi) BETWEEN '$awal' AND '$akhir' GROUP BY DAYOFMONTH(tgl_transaksi)";
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