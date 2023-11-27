-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 27, 2023 at 06:29 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kasir`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` varchar(5) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `stok` int(11) NOT NULL,
  `harga_beli` int(11) NOT NULL,
  `harga_jual` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `stok`, `harga_beli`, `harga_jual`, `id_supplier`) VALUES
('CP001', 'Hula-hula', 50, 3500, 5000, 9),
('CP002', 'Cashew Nut', 50, 8500, 10000, 9),
('IF001', 'Indomie', 50, 2000, 3000, 6),
('IF002', 'Kecap Manis', 50, 10000, 12000, 6),
('IF003', 'Saus Tomat', 50, 8500, 10000, 6),
('IF004', 'Sambal Pedas', 50, 8500, 10000, 6),
('IF005', 'Maxicorn', 86, 10000, 12000, 6),
('IF006', 'Susu Kental Manis', 50, 10000, 12000, 6),
('IF007', 'Qtela', 85, 10000, 13000, 6),
('IF008', 'Chitato', 87, 10000, 13000, 6),
('IF009', 'Chiki Twist', 46, 13000, 15000, 6),
('IF010', 'Chiki Ball', 46, 13000, 15000, 6),
('NE001', 'Air Mineral', 42, 3500, 5000, 7),
('NE002', 'Bear Brand', 47, 9000, 11000, 7),
('NE003', 'Dancow', 24, 30000, 35000, 7),
('UN001', 'Buavita', 42, 3500, 5000, 8),
('UN002', 'Lifebuoy', 50, 12000, 14000, 8);

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_barang` varchar(5) NOT NULL,
  `jumlah_barang` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_transaksi`, `id_barang`, `jumlah_barang`, `subtotal`) VALUES
(58, 'IF005', 2, 24000),
(58, 'IF007', 2, 26000),
(59, 'IF008', 3, 39000),
(59, 'IF009', 2, 30000),
(59, 'IF010', 2, 30000),
(60, 'IF007', 2, 26000),
(60, 'IF008', 2, 26000),
(61, 'IF005', 2, 24000),
(61, 'IF008', 2, 26000),
(62, 'IF005', 2, 24000),
(62, 'NE001', 2, 10000),
(63, 'IF005', 3, 36000),
(63, 'NE001', 2, 10000),
(64, 'IF007', 2, 26000),
(64, 'UN001', 2, 10000),
(65, 'IF007', 2, 26000),
(65, 'NE002', 3, 33000),
(66, 'NE003', 1, 35000),
(67, 'IF005', 1, 12000),
(67, 'IF007', 1, 13000),
(68, 'IF007', 2, 26000),
(68, 'IF008', 3, 39000),
(69, 'NE001', 2, 10000),
(69, 'UN001', 2, 10000),
(70, 'IF005', 1, 12000),
(70, 'IF007', 1, 13000),
(70, 'IF008', 1, 13000),
(70, 'NE001', 1, 5000),
(71, 'IF008', 1, 13000),
(71, 'IF009', 1, 15000),
(71, 'IF010', 1, 15000),
(71, 'UN001', 1, 5000),
(72, 'IF005', 1, 12000),
(72, 'IF007', 1, 13000),
(72, 'IF009', 1, 15000),
(73, 'IF005', 1, 12000),
(73, 'UN001', 2, 10000),
(74, 'IF007', 1, 13000),
(74, 'UN001', 1, 5000),
(75, 'IF007', 1, 13000),
(75, 'IF008', 1, 13000),
(75, 'NE001', 1, 5000),
(77, 'IF005', 1, 12000),
(77, 'IF010', 1, 15000);

--
-- Triggers `detail_transaksi`
--
DELIMITER $$
CREATE TRIGGER `kurang_stok` BEFORE INSERT ON `detail_transaksi` FOR EACH ROW UPDATE barang SET stok=(stok-NEW.jumlah_barang) WHERE id_barang = NEW.id_barang
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tambah_stok` BEFORE DELETE ON `detail_transaksi` FOR EACH ROW UPDATE barang SET stok=(stok+OLD.jumlah_barang) WHERE id_barang = OLD.id_barang
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id_supplier` int(11) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `kota` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id_supplier`, `nama_supplier`, `kota`) VALUES
(6, 'Indofood', 'Jakarta'),
(7, 'Nestle', 'Jakarta'),
(8, 'Unilever', 'Jakarta'),
(9, 'Campina', 'Surabaya');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `tgl_transaksi` datetime NOT NULL,
  `id_user` varchar(5) NOT NULL,
  `total_transaksi` int(11) DEFAULT NULL,
  `bayar` int(11) DEFAULT NULL,
  `kembali` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `tgl_transaksi`, `id_user`, `total_transaksi`, `bayar`, `kembali`) VALUES
(58, '2023-11-20 21:39:20', 'PG001', 50000, 50000, 0),
(59, '2023-11-20 21:41:02', 'PG001', 99000, 100000, 1000),
(60, '2023-11-21 21:42:13', 'PG001', 52000, 55000, 3000),
(61, '2023-11-21 21:43:06', 'PG001', 50000, 50000, 0),
(62, '2023-11-22 21:43:51', 'PG001', 34000, 35000, 1000),
(63, '2023-11-22 21:44:51', 'PG002', 46000, 50000, 4000),
(64, '2023-11-23 21:45:29', 'PG002', 36000, 40000, 4000),
(65, '2023-11-23 21:46:12', 'PG002', 59000, 60000, 1000),
(66, '2023-11-24 21:46:54', 'PG002', 35000, 35000, 0),
(67, '2023-11-24 21:47:23', 'PG002', 25000, 25000, 0),
(68, '2023-11-25 21:48:42', 'PG003', 65000, 65000, 0),
(69, '2023-11-25 21:49:07', 'PG003', 20000, 20000, 0),
(70, '2023-11-25 21:49:37', 'PG003', 43000, 45000, 2000),
(71, '2023-11-26 21:50:26', 'PG003', 48000, 50000, 2000),
(72, '2023-11-26 21:51:17', 'PG003', 40000, 40000, 0),
(73, '2023-11-26 21:52:39', 'PG001', 22000, 25000, 3000),
(74, '2023-11-26 21:52:59', 'PG001', 18000, 20000, 2000),
(75, '2023-11-27 07:22:54', 'PG001', 31000, 35000, 4000),
(77, '2023-11-27 11:47:03', 'PG001', 27000, 27000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` varchar(5) NOT NULL,
  `nama_user` varchar(50) NOT NULL,
  `role_user` varchar(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama_user`, `role_user`, `username`, `password`) VALUES
('AD001', 'Admin', 'admin', 'admin', '1234'),
('MN001', 'Manajer', 'manajer', 'manajer', '1234'),
('PG001', 'Ahmad', 'pegawai', 'ahmad', '1234'),
('PG002', 'Dewi', 'pegawai', 'dewi', '1234'),
('PG003', 'Abdul', 'pegawai', 'abdul', '1234');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `id_supplier` (`id_supplier`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_transaksi`,`id_barang`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`);

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
