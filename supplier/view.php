<?php
include "../inc/koneksi.php";
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

if($_SESSION['role']!='admin'){
    header("Location: ../routing.php");
    exit();
}

$del=$_GET['del'];
if($del!=""){
    $sql = "delete from supplier where id_supplier='$del'";
    $query = mysqli_query($conn, $sql);
    if($query){
    ?>
    <script>alert('Data Berhasil Dihapus');document.location='view.php';</script>
    <?php
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View</title>
    <style>
        body{
            text-align: center;
        }
        table {
            margin: auto;
        }
        table, th, td {
            border: 2px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <p>
        <a href="../barang/view.php">Manajemen Barang</a>
        <a href="../pegawai/view.php">Manajemen Pegawai</a>
        <a href="<?php $_SERVER['PHP_SELF']; ?>">Manajemen Supplier</a>
        <a href="../inc/logout.php">Log out</a>
    </p>
    <h1>Data Supplier</h1>
    <p><a href="insert.php">Tambah Supplier</a></p>
    <table>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kota</th>
            <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        $sql = "select * from supplier order by id_supplier asc";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_array($query)){
            echo "
            <tr>
                <td>$no</td>
                <td>$row[nama_supplier]</td>
                <td>$row[kota]</td>
                <td>
                    <a href='update.php?id=$row[id_supplier]'>Edit</a>
                    <a href='view.php?del=$row[id_supplier]'>Hapus</a>
                </td>
            </tr>
            ";
            $no++;
        }
        ?>
    </table>
</body>
</html>