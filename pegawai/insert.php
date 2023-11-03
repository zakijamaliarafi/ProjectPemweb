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
    $id = $_POST['id_user'];
    $nama = $_POST['nama_user'];
    $role = 'pegawai';
    $username = $_POST['username'];
    $password = $_POST['password'];
    $insert = "INSERT INTO `user` (`id_user`, `nama_user`, `role_user`, `username`, `password`) VALUES ('$id', '$nama', '$role', '$username', '$password') ";
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
  <h1>Tambah Data Pegawai</h1>
    <form action='<?php $_SERVER['PHP_SELF']; ?>' name="insert" method="post">
        <table>
            <tr>
              <td>Id Pegawai</td>
              <td><input type="text" name="id_user" maxlength="5" required></td>  
            </tr>
            <tr>
              <td>Nama</td>
              <td><input type="text" name="nama_user" required></td>  
            </tr>
            <tr>
              <td>Username</td>
              <td><input type="text" name="username" required></td>  
            </tr>
            <tr>
              <td>Password</td>
              <td><input type="password" name="password" required></td>  
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" name='input' value="Tambah Data Pegawai">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>