<?php
require('fpdf.php');
include "../inc/koneksi.php";
session_start();

$id=$_GET['id'];

$pdf = new FPDF('P','mm',array(100,150));
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(80,6,'TOKO RIZ',0,1,'C');
$pdf->SetFont('Arial','',8);
$pdf->Cell(80,4,'Jl. Perintis Kemerdekaan',0,1,'C');
$pdf->Ln(5);

$select_kasir = "SELECT nama_user,DATE_FORMAT(transaksi.tgl_transaksi,'%d/%m/%Y') as tgl FROM `user` JOIN `transaksi` ON transaksi.id_user=user.id_user WHERE transaksi.id_transaksi = '$id'";
$query = mysqli_query($conn,$select_kasir);
$row = mysqli_fetch_array($query);
$pdf->Cell(12,4,'Kasir',0,0,'L');
$pdf->Cell(2,4,':',0,0,'C');
$pdf->Cell(15,4,$row['nama_user'],0,1,'L');
$pdf->Cell(12,4,'Tanggal',0,0,'L');
$pdf->Cell(2,4,':',0,0,'C');
$pdf->Cell(16,4,$row['tgl'],0,1,'L');
$pdf->Ln(5);

$pdf->Line(10, 35, 90, 35);

$pdf->SetFont('Arial','B',8);
$pdf->Cell(6,4,'No',0,0,'L');
$pdf->Cell(18,4,'Nama',0,0,'L');
$pdf->Cell(18,4,'Harga',0,0,'L');
$pdf->Cell(18,4,'Jumlah',0,0,'L');
$pdf->Cell(20,4,'Jumlah harga',0,1,'L');

$pdf->SetFont('Arial','',8);
$no = 1;
$select = "SELECT * FROM `detail_transaksi`,`barang` WHERE detail_transaksi.id_barang=barang.id_barang AND id_transaksi='$id'";
$query = mysqli_query($conn, $select);
while($row3 = mysqli_fetch_array($query)){
    $pdf->Cell(6,4,$no,0,0,'L');
    $pdf->Cell(18,4,$row3['nama_barang'],0,0,'L');
    $pdf->Cell(18,4,$row3['harga_jual'],0,0,'L');
    $pdf->Cell(18,4,$row3['jumlah_barang'],0,0,'L');
    $pdf->Cell(20,4,$row3['subtotal'],0,1,'L');
    $no++;
}
$pdf->Ln(2);

$select = "SELECT * FROM `transaksi` WHERE id_transaksi='$id'";
$query = mysqli_query($conn, $select);
$row = mysqli_fetch_array($query);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(42,4,'',0,0,'');
$pdf->Cell(16,4,'Total',0,0,'L');
$pdf->Cell(2,4,':',0,0,'C');
$pdf->Cell(20,4,$row['total_transaksi'],0,1,'L');

$pdf->SetFont('Arial','',8);
$pdf->Cell(42,4,'',0,0,'');
$pdf->Cell(16,4,'Bayar',0,0,'L');
$pdf->Cell(2,4,':',0,0,'C');
$pdf->Cell(20,4,$row['bayar'],0,1,'L');

$pdf->SetFont('Arial','',8);
$pdf->Cell(42,4,'',0,0,'');
$pdf->Cell(16,4,'Kembali',0,0,'L');
$pdf->Cell(2,4,':',0,0,'C');
$pdf->Cell(20,4,$row['kembali'],0,1,'L');
$pdf->Ln(5);

$pdf->Cell(80,4,'-Terima Kasih-',0,1,'C');

$pdf->Output();
?>