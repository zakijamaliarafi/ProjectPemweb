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
    $id = $_POST['id_barang'];
    $nama = $_POST['nama_barang'];
    $stok = $_POST['stok'];
    $supplier = $_POST['supplier'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $insert = "INSERT INTO `barang` (`id_barang`, `nama_barang`, `stok`, `harga_beli`, `harga_jual`, `id_supplier`) VALUES ('$id', '$nama', '$stok', '$harga_beli', '$harga_jual', '$supplier') ";
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
  <h1>Tambah Data Barang</h1>
    <form action='<?php $_SERVER['PHP_SELF']; ?>' name="insert" method="post">
        <table>
            <tr>
              <td>Id Barang</td>
              <td><input type="text" name="id_barang" maxlength="5" required></td>  
            </tr>
            <tr>
              <td>Nama</td>
              <td><input type="text" name="nama_barang" required></td>  
            </tr>
            <tr>
              <td>Stok</td>
              <td><input type="number" name="stok" required></td>  
            </tr>
            <tr>
              <td>Supplier</td>
              <td>
                <select name="supplier">
                  <?php
                  $s = "select * from supplier";
                  $q = mysqli_query($conn, $s);
                  while($row=mysqli_fetch_array($q)){
                    echo "<option value='$row[id_supplier]'>$row[nama_supplier]</option>";
                  }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td>Harga Beli</td>
              <td><input type="number" name="harga_beli" required></td>  
            </tr>
            <tr>
              <td>Harga Jual</td>
              <td><input type="number" name="harga_jual" required></td>  
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" name='input' value="Tambah Data Barang">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>