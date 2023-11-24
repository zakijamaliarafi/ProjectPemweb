<?php
include "../inc/koneksi.php";
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

if($_SESSION['role']=='manajer'){
    header("Location: ../routing.php");
    exit();
}

// select untuk mengambil nama user yang sedang login
$select_kasir = "select nama_user from user where id_user='$_SESSION[id]'";
$query = mysqli_query($conn,$select_kasir);
$row1 = mysqli_fetch_array($query);

// mencari apakah ada barang atau tidak dengan id_barang
if(isset($_POST['cari'])){
    $id_barang = $_POST['id_barang'];

    $select_barang = "select * from barang where id_barang='$id_barang'";
    $query = mysqli_query($conn,$select_barang);
    $row2 = mysqli_fetch_array($query);
    if($row2==''){
        ?>
        <script>alert('Barang tidak ditemukan');</script>
        <?php
    }elseif($row2['stok']<1){
        ?>
        <script>alert('Stok barang habis');</script>
        <?php
        $row2 = null;
    }
}

//menambahkan barang 
if(isset($_POST['tambah_barang'])){
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $subtotal = $jumlah * $_POST['harga_jual'];
    $current_time = date('Y-m-d H:i:s');

    $select = "select * from `barang` where id_barang='$id_barang'";
    $query = mysqli_query($conn, $select);
    $row = mysqli_fetch_array($query);
    if($row['stok']<$jumlah){
        ?>
        <script>alert('Stok barang tidak cukup');</script>
        <?php
    } else{
        if(empty($_SESSION['id_transaksi'])){
            $insert_transaksi = "INSERT INTO `transaksi` (`tgl_transaksi`, `id_user`) VALUES ('$current_time', '$_SESSION[id]')";
            $query = mysqli_query($conn, $insert_transaksi);
            
            $select_id = "SELECT * FROM `transaksi` WHERE id_transaksi=(SELECT MAX(id_transaksi) FROM `transaksi`)";
            $query = mysqli_query($conn, $select_id);
            $row = mysqli_fetch_array($query);
            $_SESSION['id_transaksi'] = $row['id_transaksi'];
            
            $insert_detail = "INSERT INTO `detail_transaksi` (`id_transaksi`, `id_barang`, `jumlah_barang`, `subtotal`) VALUES ('$_SESSION[id_transaksi]', '$id_barang', '$jumlah', '$subtotal') ";
            $query = mysqli_query($conn, $insert_detail);
        } else {
            $select = "SELECT * FROM `detail_transaksi` WHERE id_barang='$id_barang' AND id_transaksi='$_SESSION[id_transaksi]'";
            $query = mysqli_query($conn, $select);
            $row = mysqli_fetch_array($query);
            if($row==''){
                $insert_detail = "INSERT INTO `detail_transaksi` (`id_transaksi`, `id_barang`, `jumlah_barang`, `subtotal`) VALUES ('$_SESSION[id_transaksi]', '$id_barang', '$jumlah', '$subtotal') ";
                $query = mysqli_query($conn, $insert_detail);
            } else {
                ?>
                <script>alert('Barang sudah ada');</script>
                <?php
            }
        }
    
        $update = "UPDATE `transaksi` SET total_transaksi=(SELECT SUM(subtotal) FROM detail_transaksi WHERE id_transaksi='$_SESSION[id_transaksi]') WHERE id_transaksi='$_SESSION[id_transaksi]'";
        $query = mysqli_query($conn, $update);
    }
}

if(isset($_POST['bayar']) && !empty($_SESSION['id_transaksi'])){
    $bayar = $_POST['uang_bayar'];
    $kembali = $_POST['uang_kembali'];

    $update = "UPDATE `transaksi` SET bayar='$bayar', kembali='$kembali' WHERE id_transaksi='$_SESSION[id_transaksi]'";
    $query = mysqli_query($conn, $update);
    ?>
        <script>alert('Transaksi berhasil');window.open('cetak.php?id=<?php echo $_SESSION['id_transaksi'];?>', '_blank');</script>
    <?php
    $_SESSION['id_transaksi'] = null;
}

if(isset($_POST['batal_bayar']) && !empty($_SESSION['id_transaksi'])){
    $del = "delete from detail_transaksi where id_transaksi='$_SESSION[id_transaksi]'";
    $query = mysqli_query($conn, $del);

    $del = "delete from transaksi where id_transaksi='$_SESSION[id_transaksi]'";
    $query = mysqli_query($conn, $del);
    $_SESSION['id_transaksi'] = null;
    ?>
        <script>alert('Transaksi dibatalkan');</script>
    <?php
}
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi</title>
    <style>
        body{
            text-align: left;
        }
        p{
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
    <h1>Transaksi</h1>

    <table>
        <tr>
            <td>Tanggal</td>
            <td><?php echo date("d/m/Y"); ?></td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td><?php echo $row1['nama_user']; ?></td>
        </tr>
    </table>

    <form action='<?php $_SERVER['PHP_SELF']; ?>' name="cari" method="POST">
        <table>
            <tr>
                <td>ID Barang</td>
                <td><input type="text" name="id_barang" required></td>
                <td><input type="submit" name="cari" value="cari"></td>
                <td><input type="reset" name="reset" value="reset"></td>
            </tr>
        </table>
    </form>

    <form action='<?php $_SERVER['PHP_SELF']; ?>' name="tambah_barang" method="POST">
        <table>
            <tr>
                <td>ID Barang</td>
                <td>Nama Barang</td>
                <td>Harga Barang</td>
                <td>Jumlah</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><input type="text" id="id_barang" name="id_barang" value="<?php echo $row2['id_barang']; ?>" readonly></td>
                <td><input type="text" name="nama_barang" value="<?php echo $row2['nama_barang']; ?>" readonly></td>
                <td><input type="number" name="harga_jual" value="<?php echo $row2['harga_jual']; ?>" readonly></td>
                <td><input type="number" name="jumlah" min="1" required></td>
                <td><input type="submit" id="tambah_barang" name="tambah_barang" value="simpan" disabled></td>
                <td><input type="submit" name="reset" value="Batal"></td>
            </tr>
        </table>
    </form>

    <table>
        <tr>
            <td>No</td>
            <td>Nama Barang</td>
            <td>Harga Barang</td>
            <td>Jumlah</td>
            <td colspan="2">Jumlah Harga</td>
        </tr>
        <?php
        if(!empty($_SESSION['id_transaksi'])){
            $no = 1;
            $select = "SELECT * FROM `detail_transaksi`,`barang` WHERE detail_transaksi.id_barang=barang.id_barang AND id_transaksi='$_SESSION[id_transaksi]'";
            $query = mysqli_query($conn, $select);
            while($row3 = mysqli_fetch_array($query)){
                echo "
                <tr>
                    <td>$no</td>
                    <td>$row3[nama_barang]</td>
                    <td>$row3[harga_jual]</td>
                    <td>$row3[jumlah_barang]</td>
                    <td>Rp. </td>
                    <td align='right'>$row3[subtotal]</td>
                </tr>
                ";
                $no++;
            }
            $select = "SELECT total_transaksi FROM `transaksi` WHERE id_transaksi='$_SESSION[id_transaksi]'";
            $query = mysqli_query($conn, $select);
            $row4 = mysqli_fetch_array($query);
            echo "
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Total Bayar</td>
                    <td>Rp. </td>
                    <td align='right'>$row4[total_transaksi]</td>
                </tr>
            ";
        }
        
        ?>
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>Bayar</td>
                <td colspan="2">Kembali</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td><input type="number" id="uang_bayar" name="uang_bayar" oninput="hitung(<?php echo $row4['total_transaksi']; ?>)" min="<?php echo $row4['total_transaksi']; ?>"></td>
                <td colspan="2"><input type="number" id="uang_kembali" name="uang_kembali" readonly></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><input type="submit" id="bayar" name="bayar" value="Bayar" disabled></td>
                <td><input type="submit" name="batal_bayar" value="Batal"></td>
            </tr>
        </form>
    </table>
<script>
    function hitung(total){
        let uang_bayar = document.getElementById("uang_bayar").value;
        let kembali = uang_bayar - total;
        document.getElementById("uang_kembali").value = kembali;
        if(kembali < 0){
            document.getElementById("bayar").disabled = true;
        } else{
            document.getElementById("bayar").disabled = false;
        }
    }
    if(document.getElementById("uang_bayar").value != ''){
        document.getElementById("bayar").disabled = false;
    }
    if(document.getElementById("id_barang").value != ''){
        document.getElementById("tambah_barang").disabled = false;
    }
    
</script>
</body>
</html>