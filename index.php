<?php
include 'inc/koneksi.php'; 
session_start();
 
if (isset($_SESSION['id'])) {
    header("Location: routing.php");
}
 
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
 
    $sql = "SELECT * FROM user WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    if ($result->num_rows > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['id'] = $row['id_user'];
        $_SESSION['role'] = $row['role_user'];
        header("Location: routing.php");
    } else {
        echo "<script>alert('Username atau password Anda salah. Silahkan coba lagi!')</script>";
    }
}
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    
        <h1>Login</h1>
        
            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                <table>
                    <tr>
                        <td><label for="username" >Username</label></td>
                        <td><input type="text" id="username" name="username"></td>
                    </tr>
                    <tr>
                        <td><label for="password" >Password</label></td>
                        <td><input type="password" id="password" name="password"></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="login" name="login"></td>
                    </tr>
                </table>
            </form>
        
    
</body>
</html>