<?php
include "../inc/koneksi.php";
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

if($_SESSION['role']!='pegawai'){
    header("Location: ../routing.php");
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
    <p>
        <a href="../transaksi/view.php">Transaksi</a>
        <a href="<?php $_SERVER['PHP_SELF']; ?>">Riwayat</a>
        <a href="../inc/logout.php">Log out</a>
    </p>
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