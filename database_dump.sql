-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2017 at 10:08 PM
-- Server version: 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rp2`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategorije`
--

CREATE TABLE `kategorije` (
  `id` int(11) NOT NULL,
  `categoryName` varchar(100) COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `kategorije`
--

INSERT INTO `kategorije` (`id`, `categoryName`) VALUES
(36, 'temp4'),
(20, 'Treca'),
(21, 'nova'),
(23, 'Zemljopis'),
(35, 'temp3'),
(33, 'tenp'),
(34, 'temp2');

-- --------------------------------------------------------

--
-- Table structure for table `korisnici`
--

CREATE TABLE `korisnici` (
  `id` int(11) NOT NULL,
  `user` varchar(100) COLLATE utf8_bin NOT NULL,
  `pass` varchar(100) COLLATE utf8_bin NOT NULL,
  `email` varchar(100) COLLATE utf8_bin NOT NULL,
  `userType` int(1) NOT NULL,
  `bestResults` int(11) NOT NULL DEFAULT '0',
  `bestResultsCategory` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `korisnici`
--

INSERT INTO `korisnici` (`id`, `user`, `pass`, `email`, `userType`, `bestResults`, `bestResultsCategory`) VALUES
(2, 'atrava', '3d4f2bf07dc1be38b20cd6e46949a1071f9d0e3d', 'a@a.com', 1, 230, 'Sve kategorije'),
(3, 'anatrava', '3d4f2bf07dc1be38b20cd6e46949a1071f9d0e3d', 'a.bc@a.com', 1, 10, 'name'),
(4, 'ananeadmin', '3d4f2bf07dc1be38b20cd6e46949a1071f9d0e3d', 'ana@g.com', 2, 60, 'nova');

-- --------------------------------------------------------

--
-- Table structure for table `pitanja`
--

CREATE TABLE `pitanja` (
  `id` int(11) NOT NULL,
  `question` text COLLATE utf8_bin NOT NULL,
  `questionType` int(1) NOT NULL,
  `questionScore` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `imageForQuestion` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `correctAnswer` text COLLATE utf8_bin NOT NULL,
  `questionExplanation` text COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `pitanja`
--

INSERT INTO `pitanja` (`id`, `question`, `questionType`, `questionScore`, `categoryId`, `imageForQuestion`, `correctAnswer`, `questionExplanation`) VALUES
(1, 'pitanje', 1, 70, 17, '', 'Tocan odgovor', 'Ovo je objasnjenje'),
(5, 'drugo', 2, 70, 17, '', '', 'objanjenje'),
(6, 'sada', 2, 30, 17, '', '', 'skćskf'),
(7, 'peta', 2, 30, 17, '', '', 'sdflčk'),
(8, 'pppppp slika', 3, 34, 17, 'slikeZaPitanja/1499499090508_278.jpg', 'sdfaf', 'saddas'),
(9, 'pitanje', 1, 70, 17, '', 'tocan', 'ovo je objajs'),
(10, 'pitanje', 2, 60, 20, '', '', 'tocnooo'),
(11, 'dsffds', 3, 45, 17, 'slikeZaPitanja/1499499389708_245.ico', 'sdada', 'sfdsfd'),
(12, 'Tovno netocn', 4, 45, 17, '', 'false', 'netocno'),
(13, 'Ana je wonderwoman', 4, 100, 17, '', 'true', 'Ovo je tocno'),
(14, '&scaron;đčćž', 1, 11, 17, '', 'p&scaron;đđćžčžćč', 'žćč&scaron;đ'),
(15, '&scaron;đčćž', 1, 23, 17, '', '&scaron;đčćž', 'sada'),
(16, 'pitanje sa lsikom', 3, 50, 21, 'slikeZaPitanja/1499626306869_309.jpg', 'Zagreb', 'Ovo je zagreb'),
(17, 'samo pitanje', 1, 45, 17, '', 'Zagreb', 'Ovo je opet zagreb'),
(18, 'Samo pitanje', 1, 45, 21, '', 'Zagreb', 'Ovo je opet zagreb'),
(19, 'Izaberi zagreb', 2, 10, 21, '', '', 'Opet zagreb'),
(20, 'Zagreb je glavnio  grad hrvatske', 4, 10, 21, '', 'true', 'Ovo je tocno'),
(21, 'samo pitajklfhsadjkfas', 1, 60, 33, '', 'tocan', 'asffasdfsadfsda'),
(22, 'Oov je pianje sa slikomasdfa sadkljfćas\r\nasfčkaćsdfkčćaskdf člsdkfčć asdlčfk člasdkfč ksadčfk p&scaron;we &scaron;psp&scaron;fk sčak čskfopk sfjiasf joisf', 3, 77, 34, 'slikeZaPitanja/1499805701374_780.jpg', 'tocan', 'obvbfsfs'),
(23, 'ponudjenj', 2, 34, 35, '', '', 'treci je toacn'),
(24, 'tocno jeeeee', 4, 12, 36, '', 'true', 'sadasd');

-- --------------------------------------------------------

--
-- Table structure for table `ponudjeniodgovori`
--

CREATE TABLE `ponudjeniodgovori` (
  `id` int(11) NOT NULL,
  `questionId` int(11) NOT NULL,
  `textAnswer` text COLLATE utf8_bin NOT NULL,
  `isCorrect` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `ponudjeniodgovori`
--

INSERT INTO `ponudjeniodgovori` (`id`, `questionId`, `textAnswer`, `isCorrect`) VALUES
(1, 7, '1', 0),
(2, 7, '2', 0),
(3, 7, '3 i tocan', 1),
(4, 7, 'ce4', 0),
(5, 10, 'odg1', 0),
(6, 10, 'odg2', 1),
(7, 10, 'odg3', 0),
(8, 10, 'odg4', 0),
(9, 19, 'Capljina', 0),
(10, 19, 'Zagreb', 1),
(11, 19, 'Osijek', 0),
(12, 19, 'Vukovar', 0),
(13, 23, 'Prvo pitanje', 0),
(14, 23, 'Drugo', 0),
(15, 23, 'Tocan sam', 1),
(16, 23, 'nisam', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategorije`
--
ALTER TABLE `kategorije`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `korisnici`
--
ALTER TABLE `korisnici`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pitanja`
--
ALTER TABLE `pitanja`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ponudjeniodgovori`
--
ALTER TABLE `ponudjeniodgovori`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategorije`
--
ALTER TABLE `kategorije`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `korisnici`
--
ALTER TABLE `korisnici`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `pitanja`
--
ALTER TABLE `pitanja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `ponudjeniodgovori`
--
ALTER TABLE `ponudjeniodgovori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
