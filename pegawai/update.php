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

$row = mysqli_fetch_array(mysqli_query($conn, "select * from user where id_user='$idupdate'"));

if(isset($_POST['update'])){
    $id = $_POST['id_user'];
    $nama = $_POST['nama_user'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $update = "update user set id_user='$id', nama_user='$nama', username='$username', password='$password' where id_user='$idupdate'";
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

if($row['id_user']!=""){
    ?>
    <!DOCTYPE html>
    <html>
    <head>
    <title>Update</title>
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
    <h1>Edit Data Pegawai</h1>
    <form action='<?php $_SERVER['PHP_SELF']; ?>' name="update" method="post">
        <table>
            <tr>
              <td>Id User</td>
              <td><input type="text" name="id_user" maxlength="5" required value='<?php echo $row['id_user']; ?>'></td>  
            </tr>
            <tr>
              <td>Nama</td>
              <td><input type="text" name="nama_user" required value='<?php echo $row['nama_user']; ?>'></td>  
            </tr>
            <tr>
              <td>Username</td>
              <td><input type="text" name="username" required value='<?php echo $row['username']; ?>'></td>  
            </tr>
            <tr>
              <td>Password</td>
              <td><input type="password" name="password" required value='<?php echo $row['password']; ?>'></td>  
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" name='update' value="Edit Data Pegawai">
                </td>
            </tr>
        </table>
    </form>
    </body>
    </html>
    <?php
}
?>