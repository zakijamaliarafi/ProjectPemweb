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
    $sql = "delete from user where id_user='$del'";
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
        <a href="<?php $_SERVER['PHP_SELF']; ?>">Manajemen Pegawai</a>
        <a href="../supplier/view.php">Manajemen Supplier</a>
        <a href="../inc/logout.php">Log out</a>
    </p>
    <h1>Data Pegawai</h1>
    <p><a href="insert.php">Tambah Pegawai</a></p>
    <table>
        <tr>
            <th>No</th>
            <th>Id Pegawai</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        $sql = "select * from user where role_user='pegawai' order by id_user asc";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_array($query)){
            echo "
            <tr>
                <td>$no</td>
                <td>$row[id_user]</td>
                <td>$row[nama_user]</td>
                <td>$row[username]</td>
                <td>
                    <a href='update.php?id=$row[id_user]'>Edit</a>
                    <a href='view.php?del=$row[id_user]'>Hapus</a>
                </td>
            </tr>
            ";
            $no++;
        }
        ?>
    </table>
</body>
</html>