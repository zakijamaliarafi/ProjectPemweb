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
    $_SESSION['id_transaksi'] = null;
    ?>
        <script>alert('Transaksi berhasil');</script>
    <?php
}

if(isset($_POST['bayar_cetak']) && !empty($_SESSION['id_transaksi'])){
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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>View</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/css/adminlte.min.css">
  <style>
    th, td {
      padding-right: 5px;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="../assets/img/toko.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Toko RIZ</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../assets/img/akun.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">
          <?php
            $select_user = "SELECT nama_user FROM `user` WHERE id_user = '$_SESSION[id]'";
            $query = mysqli_query($conn,$select_user);
            $row = mysqli_fetch_array($query);
            echo $row['nama_user'];
            ?>
          </a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <?php
          if($_SESSION['role']=='pegawai'){
            echo "
          <li class='nav-item'>
            <a href='../transaksi/view.php' class='nav-link'>
              <i class='nav-icon fas'><svg xmlns='http://www.w3.org/2000/svg' height='1em' viewBox='0 0 512 512'><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#ffffff}</style><path d='M64 0C46.3 0 32 14.3 32 32V96c0 17.7 14.3 32 32 32h80v32H87c-31.6 0-58.5 23.1-63.3 54.4L1.1 364.1C.4 368.8 0 373.6 0 378.4V448c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V378.4c0-4.8-.4-9.6-1.1-14.4L488.2 214.4C483.5 183.1 456.6 160 425 160H208V128h80c17.7 0 32-14.3 32-32V32c0-17.7-14.3-32-32-32H64zM96 48H256c8.8 0 16 7.2 16 16s-7.2 16-16 16H96c-8.8 0-16-7.2-16-16s7.2-16 16-16zM64 432c0-8.8 7.2-16 16-16H432c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16zm48-168a24 24 0 1 1 0-48 24 24 0 1 1 0 48zm120-24a24 24 0 1 1 -48 0 24 24 0 1 1 48 0zM160 344a24 24 0 1 1 0-48 24 24 0 1 1 0 48zM328 240a24 24 0 1 1 -48 0 24 24 0 1 1 48 0zM256 344a24 24 0 1 1 0-48 24 24 0 1 1 0 48zM424 240a24 24 0 1 1 -48 0 24 24 0 1 1 48 0zM352 344a24 24 0 1 1 0-48 24 24 0 1 1 0 48z'/></svg></i>
              <p>
                Transaksi
              </p>
            </a>
          </li>
          <li class='nav-item'>
            <a href='../riwayat/view.php' class='nav-link'>
              <i class='nav-icon fas'><svg xmlns='http://www.w3.org/2000/svg' height='1em' viewBox='0 0 384 512'><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#ffffff}</style><path d='M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM64 80c0-8.8 7.2-16 16-16h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16zm0 64c0-8.8 7.2-16 16-16h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16zm128 72c8.8 0 16 7.2 16 16v17.3c8.5 1.2 16.7 3.1 24.1 5.1c8.5 2.3 13.6 11 11.3 19.6s-11 13.6-19.6 11.3c-11.1-3-22-5.2-32.1-5.3c-8.4-.1-17.4 1.8-23.6 5.5c-5.7 3.4-8.1 7.3-8.1 12.8c0 3.7 1.3 6.5 7.3 10.1c6.9 4.1 16.6 7.1 29.2 10.9l.5 .1 0 0 0 0c11.3 3.4 25.3 7.6 36.3 14.6c12.1 7.6 22.4 19.7 22.7 38.2c.3 19.3-9.6 33.3-22.9 41.6c-7.7 4.8-16.4 7.6-25.1 9.1V440c0 8.8-7.2 16-16 16s-16-7.2-16-16V422.2c-11.2-2.1-21.7-5.7-30.9-8.9l0 0c-2.1-.7-4.2-1.4-6.2-2.1c-8.4-2.8-12.9-11.9-10.1-20.2s11.9-12.9 20.2-10.1c2.5 .8 4.8 1.6 7.1 2.4l0 0 0 0 0 0c13.6 4.6 24.6 8.4 36.3 8.7c9.1 .3 17.9-1.7 23.7-5.3c5.1-3.2 7.9-7.3 7.8-14c-.1-4.6-1.8-7.8-7.7-11.6c-6.8-4.3-16.5-7.4-29-11.2l-1.6-.5 0 0c-11-3.3-24.3-7.3-34.8-13.7c-12-7.2-22.6-18.9-22.7-37.3c-.1-19.4 10.8-32.8 23.8-40.5c7.5-4.4 15.8-7.2 24.1-8.7V232c0-8.8 7.2-16 16-16z'/></svg></i>
              <p>
                Riwayat Transaksi
              </p>
            </a>
          </li>
          <li class='nav-item'>
            <a href='../barang/view.php' class='nav-link'>
              <i class='nav-icon fas'><svg xmlns='http://www.w3.org/2000/svg' height='1em' viewBox='0 0 576 512'><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#ffffff}</style><path d='M248 0H208c-26.5 0-48 21.5-48 48V160c0 35.3 28.7 64 64 64H352c35.3 0 64-28.7 64-64V48c0-26.5-21.5-48-48-48H328V80c0 8.8-7.2 16-16 16H264c-8.8 0-16-7.2-16-16V0zM64 256c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H224c35.3 0 64-28.7 64-64V320c0-35.3-28.7-64-64-64H184v80c0 8.8-7.2 16-16 16H120c-8.8 0-16-7.2-16-16V256H64zM352 512H512c35.3 0 64-28.7 64-64V320c0-35.3-28.7-64-64-64H472v80c0 8.8-7.2 16-16 16H408c-8.8 0-16-7.2-16-16V256H352c-15 0-28.8 5.1-39.7 13.8c4.9 10.4 7.7 22 7.7 34.2V464c0 12.2-2.8 23.8-7.7 34.2C323.2 506.9 337 512 352 512z'/></svg></i>
              <p>
                Data Barang
              </p>
            </a>
          </li>
          <li class='nav-item'>
            <a href='../supplier/view.php' class='nav-link'>
              <i class='nav-icon fas'><svg xmlns='http://www.w3.org/2000/svg' height='1em' viewBox='0 0 640 512'><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#ffffff}</style><path d='M0 488V171.3c0-26.2 15.9-49.7 40.2-59.4L308.1 4.8c7.6-3.1 16.1-3.1 23.8 0L599.8 111.9c24.3 9.7 40.2 33.3 40.2 59.4V488c0 13.3-10.7 24-24 24H568c-13.3 0-24-10.7-24-24V224c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32V488c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24zm488 24l-336 0c-13.3 0-24-10.7-24-24V432H512l0 56c0 13.3-10.7 24-24 24zM128 400V336H512v64H128zm0-96V224H512l0 80H128z'/></svg></i>
              <p>
                Data Supplier
              </p>
            </a>
          </li>
          <li class='nav-item'>
            <a href='../inc/logout.php' class='nav-link'>
                <i class='nav-icon fas'><svg xmlns='http://www.w3.org/2000/svg' height='1em' viewBox='0 0 512 512'><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#ffffff}</style><path d='M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 192 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128zM160 96c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 32C43 32 0 75 0 128L0 384c0 53 43 96 96 96l64 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l64 0z'/></svg></i>
              <p>
                Log out
              </p>
            </a>
          </li>";
          } else {
              echo "<li class='nav-item'>
              <a href='../laporan/view.php' class='nav-link'>
                <i class='nav-icon fas'><svg xmlns='http://www.w3.org/2000/svg' height='1em' viewBox='0 0 512 512'><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#ffffff}</style><path d='M64 64c0-17.7-14.3-32-32-32S0 46.3 0 64V400c0 44.2 35.8 80 80 80H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H80c-8.8 0-16-7.2-16-16V64zm406.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L320 210.7l-57.4-57.4c-12.5-12.5-32.8-12.5-45.3 0l-112 112c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L240 221.3l57.4 57.4c12.5 12.5 32.8 12.5 45.3 0l128-128z'/></svg></i>
                <p>
                  Laporan
                </p>
              </a>
            </li>
            <li class='nav-item'>
              <a href='../transaksi/view.php' class='nav-link'>
                <i class='nav-icon fas'><svg xmlns='http://www.w3.org/2000/svg' height='1em' viewBox='0 0 512 512'><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#ffffff}</style><path d='M64 0C46.3 0 32 14.3 32 32V96c0 17.7 14.3 32 32 32h80v32H87c-31.6 0-58.5 23.1-63.3 54.4L1.1 364.1C.4 368.8 0 373.6 0 378.4V448c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V378.4c0-4.8-.4-9.6-1.1-14.4L488.2 214.4C483.5 183.1 456.6 160 425 160H208V128h80c17.7 0 32-14.3 32-32V32c0-17.7-14.3-32-32-32H64zM96 48H256c8.8 0 16 7.2 16 16s-7.2 16-16 16H96c-8.8 0-16-7.2-16-16s7.2-16 16-16zM64 432c0-8.8 7.2-16 16-16H432c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16zm48-168a24 24 0 1 1 0-48 24 24 0 1 1 0 48zm120-24a24 24 0 1 1 -48 0 24 24 0 1 1 48 0zM160 344a24 24 0 1 1 0-48 24 24 0 1 1 0 48zM328 240a24 24 0 1 1 -48 0 24 24 0 1 1 48 0zM256 344a24 24 0 1 1 0-48 24 24 0 1 1 0 48zM424 240a24 24 0 1 1 -48 0 24 24 0 1 1 48 0zM352 344a24 24 0 1 1 0-48 24 24 0 1 1 0 48z'/></svg></i>
                <p>
                  Transaksi
                </p>
              </a>
            </li>
            <li class='nav-item'>
              <a href='../riwayat/view.php' class='nav-link'>
                <i class='nav-icon fas'><svg xmlns='http://www.w3.org/2000/svg' height='1em' viewBox='0 0 384 512'><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#ffffff}</style><path d='M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM64 80c0-8.8 7.2-16 16-16h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16zm0 64c0-8.8 7.2-16 16-16h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16zm128 72c8.8 0 16 7.2 16 16v17.3c8.5 1.2 16.7 3.1 24.1 5.1c8.5 2.3 13.6 11 11.3 19.6s-11 13.6-19.6 11.3c-11.1-3-22-5.2-32.1-5.3c-8.4-.1-17.4 1.8-23.6 5.5c-5.7 3.4-8.1 7.3-8.1 12.8c0 3.7 1.3 6.5 7.3 10.1c6.9 4.1 16.6 7.1 29.2 10.9l.5 .1 0 0 0 0c11.3 3.4 25.3 7.6 36.3 14.6c12.1 7.6 22.4 19.7 22.7 38.2c.3 19.3-9.6 33.3-22.9 41.6c-7.7 4.8-16.4 7.6-25.1 9.1V440c0 8.8-7.2 16-16 16s-16-7.2-16-16V422.2c-11.2-2.1-21.7-5.7-30.9-8.9l0 0c-2.1-.7-4.2-1.4-6.2-2.1c-8.4-2.8-12.9-11.9-10.1-20.2s11.9-12.9 20.2-10.1c2.5 .8 4.8 1.6 7.1 2.4l0 0 0 0 0 0c13.6 4.6 24.6 8.4 36.3 8.7c9.1 .3 17.9-1.7 23.7-5.3c5.1-3.2 7.9-7.3 7.8-14c-.1-4.6-1.8-7.8-7.7-11.6c-6.8-4.3-16.5-7.4-29-11.2l-1.6-.5 0 0c-11-3.3-24.3-7.3-34.8-13.7c-12-7.2-22.6-18.9-22.7-37.3c-.1-19.4 10.8-32.8 23.8-40.5c7.5-4.4 15.8-7.2 24.1-8.7V232c0-8.8 7.2-16 16-16z'/></svg></i>
                <p>
                  Riwayat Transaksi
                </p>
              </a>
            </li>
            <li class='nav-item'>
              <a href='../pegawai/view.php' class='nav-link'>
                <i class='nav-icon fas'><svg xmlns='http://www.w3.org/2000/svg' height='1em' viewBox='0 0 640 512'><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#ffffff}</style><path d='M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z'/></svg></i>
                <p>
                  Manajemen Pegawai
                </p>
              </a>
            </li>
            <li class='nav-item'>
              <a href='../barang/view.php' class='nav-link'>
                <i class='nav-icon fas'><svg xmlns='http://www.w3.org/2000/svg' height='1em' viewBox='0 0 576 512'><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#ffffff}</style><path d='M248 0H208c-26.5 0-48 21.5-48 48V160c0 35.3 28.7 64 64 64H352c35.3 0 64-28.7 64-64V48c0-26.5-21.5-48-48-48H328V80c0 8.8-7.2 16-16 16H264c-8.8 0-16-7.2-16-16V0zM64 256c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H224c35.3 0 64-28.7 64-64V320c0-35.3-28.7-64-64-64H184v80c0 8.8-7.2 16-16 16H120c-8.8 0-16-7.2-16-16V256H64zM352 512H512c35.3 0 64-28.7 64-64V320c0-35.3-28.7-64-64-64H472v80c0 8.8-7.2 16-16 16H408c-8.8 0-16-7.2-16-16V256H352c-15 0-28.8 5.1-39.7 13.8c4.9 10.4 7.7 22 7.7 34.2V464c0 12.2-2.8 23.8-7.7 34.2C323.2 506.9 337 512 352 512z'/></svg></i>
                <p>
                  Manajemen Barang
                </p>
              </a>
            </li>
            <li class='nav-item'>
              <a href='../supplier/view.php' class='nav-link'>
                <i class='nav-icon fas'><svg xmlns='http://www.w3.org/2000/svg' height='1em' viewBox='0 0 640 512'><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#ffffff}</style><path d='M0 488V171.3c0-26.2 15.9-49.7 40.2-59.4L308.1 4.8c7.6-3.1 16.1-3.1 23.8 0L599.8 111.9c24.3 9.7 40.2 33.3 40.2 59.4V488c0 13.3-10.7 24-24 24H568c-13.3 0-24-10.7-24-24V224c0-17.7-14.3-32-32-32H128c-17.7 0-32 14.3-32 32V488c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24zm488 24l-336 0c-13.3 0-24-10.7-24-24V432H512l0 56c0 13.3-10.7 24-24 24zM128 400V336H512v64H128zm0-96V224H512l0 80H128z'/></svg></i>
                <p>
                  Manajemen Supplier
                </p>
              </a>
            </li>
            <li class='nav-item'>
              <a href='../inc/logout.php' class='nav-link'>
                  <i class='nav-icon fas'><svg xmlns='http://www.w3.org/2000/svg' height='1em' viewBox='0 0 512 512'><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#ffffff}</style><path d='M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 192 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128zM160 96c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 32C43 32 0 75 0 128L0 384c0 53 43 96 96 96l64 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l64 0z'/></svg></i>
                <p>
                  Log out
                </p>
              </a>
            </li>";
          }
          ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Transaksi</h1>
          </div>
          <div class="col-sm-6">
            
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
              <div class="float-sm-left">
                <form action='<?php $_SERVER['PHP_SELF']; ?>' name="cari" method="POST">
                    <table>
                        <tr>
                            <td>ID Barang</td>
                            <td><input type="text" name="id_barang" size="12" required></td>
                            <td><input class="btn btn-primary btn-sm" type="submit" name="cari" value="Cari"></td>
                            <td><input class="btn btn-warning btn-sm" type="reset" name="reset" value="Reset"></td>
                        </tr>
                    </table>
                </form>
              </div>
              <div class="float-sm-right">
                <table>
                  <tr>
                      <td>Tanggal</td>
                      <td>:</td>
                      <td><?php echo date("d/m/Y"); ?></td>
                  </tr>
                  <tr>
                      <td>Kasir</td>
                      <td>:</td>
                      <td><?php echo $row1['nama_user']; ?></td>
                  </tr>
                </table>
              </div>
              <div style="margin-top: 75px;">
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
                            <td><input type="number" id="jumlah" name="jumlah" min="1" oninput="check()"></td>
                            <td><input class="btn btn-success btn-sm" type="submit" id="tambah_barang" name="tambah_barang" value="Simpan" disabled></td>
                            <td><input class="btn btn-danger btn-sm" type="submit" name="reset" value="Batal"></td>
                        </tr>
                    </table>
                </form>
              </div>
              <div style="margin-top: 50px;">
                <table>
                    <tr>
                        <td style="padding-right: 20px;padding-bottom: 5px;">No</td>
                        <td style="padding-right: 70px;padding-bottom: 5px;">Nama Barang</td>
                        <td style="padding-right: 110px;padding-bottom: 5px;">Harga Barang</td>
                        <td style="padding-right: 75px;padding-bottom: 5px;">Jumlah</td>
                        <td style="padding-bottom: 5px;" colspan="2">Jumlah Harga</td>
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
                                <td>Rp. $row3[harga_jual]</td>
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
                    } else {
                      echo "
                          <tr height='20px'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>
                          <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Total Bayar</td>
                            <td>Rp. 0</td>
                            <td></td>
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
                            <td style="padding-top: 10px;"><input class="btn btn-success btn-sm" type="submit" id="bayar" name="bayar" value="Bayar" disabled>
                            <input class="btn btn-success btn-sm" type="submit" id="bayar_cetak" name="bayar_cetak" value="Bayar & Cetak" disabled>
                            </td>
                            <td style="padding-top: 10px;"><input class="btn btn-danger btn-sm" type="submit" name="batal_bayar" value="Batal"></td>
                        </tr>
                    </form>
                </table>
              </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->
<script>
    function hitung(total){
        let uang_bayar = document.getElementById("uang_bayar").value;
        let kembali = uang_bayar - total;
        document.getElementById("uang_kembali").value = kembali;
        if(kembali < 0){
            document.getElementById("bayar").disabled = true;
            document.getElementById("bayar_cetak").disabled = true;
        } else{
            document.getElementById("bayar").disabled = false;
            document.getElementById("bayar_cetak").disabled = false;
        }
    }
    function check(){
      if(document.getElementById("jumlah").value > 0){
        document.getElementById("tambah_barang").disabled = false;
      }
      if(document.getElementById("jumlah").value <= 0){
        document.getElementById("tambah_barang").disabled = true;
      }
    }
    if(document.getElementById("uang_bayar").value != ''){
        document.getElementById("bayar").disabled = false;
        document.getElementById("bayar_cetak").disabled = false;
    }
    if(document.getElementById("id_barang").value != '' &&
       document.getElementById("jumlah").value > 0  
    ){
        document.getElementById("tambah_barang").disabled = false;
    }
    
</script>
<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/plugins/jszip/jszip.min.js"></script>
<script src="../assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="../assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../assets/js/demo.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
</body>
</html>