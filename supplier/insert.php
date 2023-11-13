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

if(isset($_POST['input'])){
    $nama = $_POST['nama_supplier'];
    $kota = $_POST['kota'];
    $insert = "INSERT INTO `supplier` (`nama_supplier`, `kota`) VALUES ('$nama', '$kota') ";
    $query = mysqli_query($conn, $insert);
    if($query){
        ?>
        <script>
            alert('Data berhasil Ditambahkan!');
            document.location='view.php';
        </script>
        <?php
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Insert</title>
    <style>
      body{
        text-align: center;
      }
      table {
        margin: auto;
      }
      td {
        text-align: left;
        padding: 5px;
      }
    </style>
</head>
<body>
  <h1>Tambah Data Supplier</h1>
    <form action='<?php $_SERVER['PHP_SELF']; ?>' name="insert" method="post">
        <table>
            <tr>
              <td>Nama</td>
              <td><input type="text" name="nama_supplier" required></td>  
            </tr>
            <tr>
              <td>Kota</td>
              <td><input type="text" name="kota" required></td>  
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" name='input' value="Tambah Data Supplier">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>