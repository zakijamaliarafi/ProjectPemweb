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

$idupdate = $_GET['id'];

$row = mysqli_fetch_array(mysqli_query($conn, "select * from supplier where id_supplier='$idupdate'"));

if(isset($_POST['update'])){
    $nama = $_POST['nama_supplier'];
    $kota = $_POST['kota'];
    $update = "update supplier set nama_supplier='$nama', kota='$kota' where id_supplier='$idupdate'";
    $query = mysqli_query($conn,$update);
    if($query){
        ?>
        <script>
            alert('Data berhasil Diubah!');
            document.location='view.php';
        </script>
        <?php
    }
}

if($row['id_supplier']!=""){
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
    <h1>Edit Data Supplier</h1>
    <form action='<?php $_SERVER['PHP_SELF']; ?>' name="update" method="post">
        <table>
            <tr>
              <td>Nama</td>
              <td><input type="text" name="nama_supplier" required value='<?php echo $row['nama_supplier']; ?>'></td>  
            </tr>
            <tr>
              <td>Kota</td>
              <td><input type="text" name="kota" required value='<?php echo $row['kota']; ?>'></td>  
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" name='update' value="Edit Data Supplier">
                </td>
            </tr>
        </table>
    </form>
    </body>
    </html>
    <?php
}
?>