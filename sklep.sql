-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2023 at 01:32 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sklep`
--

-- --------------------------------------------------------

--
-- Table structure for table `dostawa`
--

CREATE TABLE `dostawa` (
  `dostawa_id` tinyint(4) NOT NULL,
  `nazwa_dostawy` varchar(50) NOT NULL,
  `koszt` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `dostawa`
--

INSERT INTO `dostawa` (`dostawa_id`, `nazwa_dostawy`, `koszt`) VALUES
(1, 'Kurier', '12.99'),
(2, 'Paczkomat InPost', '9.99'),
(3, 'Sklep ŻABKA', '14.99'),
(4, 'Punkt RUCH', '10.99');

-- --------------------------------------------------------

--
-- Table structure for table `kategoria`
--

CREATE TABLE `kategoria` (
  `kategoria_id` tinyint(4) NOT NULL,
  `nazwa` varchar(25) NOT NULL,
  `zdjecie` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `kategoria`
--

INSERT INTO `kategoria` (`kategoria_id`, `nazwa`, `zdjecie`) VALUES
(1, 'Laptopy', 'laptop.jpg'),
(52, 'Smartfony', 'smartphones.jpg'),
(54, 'Tablety', 'tablets.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `parametr`
--

CREATE TABLE `parametr` (
  `parametr_id` int(11) NOT NULL,
  `parametr` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `parametr`
--

INSERT INTO `parametr` (`parametr_id`, `parametr`) VALUES
(1, 'Procesor'),
(2, 'Pamięć RAM'),
(3, 'Dysk'),
(4, 'System operacyjny');

-- --------------------------------------------------------

--
-- Table structure for table `platnosc`
--

CREATE TABLE `platnosc` (
  `platnosc_id` tinyint(4) NOT NULL,
  `nazwa_platnosci` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `platnosc`
--

INSERT INTO `platnosc` (`platnosc_id`, `nazwa_platnosci`) VALUES
(1, 'Karta płatnicza'),
(2, 'Google Pay'),
(3, 'BLIK');

-- --------------------------------------------------------

--
-- Table structure for table `produkt`
--

CREATE TABLE `produkt` (
  `produkt_id` int(11) NOT NULL,
  `nazwa` varchar(200) NOT NULL,
  `opis` varchar(5000) NOT NULL,
  `cena` decimal(8,2) NOT NULL,
  `promocja` decimal(8,2) DEFAULT NULL,
  `najlepsza_cecha` varchar(30) DEFAULT NULL,
  `ilosc` mediumint(9) NOT NULL,
  `gwarancja` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `produkt`
--

INSERT INTO `produkt` (`produkt_id`, `nazwa`, `opis`, `cena`, `promocja`, `najlepsza_cecha`, `ilosc`, `gwarancja`) VALUES
(1, 'Laptop DELL G15 5511-6228 15.6\" i5-11260H 16GB RAM 512GB SSD GeForce RTX3050 Windows 11 Professional', 'Odkryj najnowsze wcielenie G15. Wszechstronny laptop do pracy i gamingu. Stworzony z myślą o miłośnikach gier, łączy w sobie moc procesora Intel Core 11-stej generacji z kartą graficzną NVIDIA GeForce RTX gwarantującymi płynność i wydajność oraz innowacyjny system chłodzenia gotowy na najgorętsze akcje.Jeśli szukasz laptopa, który utrzyma wysokie tempo pracy i rozrywki, postaw na G15. Wielozadaniowy procesor Intel Core 11-stej generacji z serii H z łatwością sprosta wszystkim postawionym przed nim zadaniom, zarówno w pracy zawodowej czy nauce zdalnej, jak i w czasie wolnym. Szczególnie, gdy jesteś fanem gamingu.', '5099.99', '4849.99', 'Wydajne chłodzenie', 1, 24),
(25, 'iPhone 14 Pro Max', 'iPhone 14 Pro Max wyposażono w funkcję bezpieczeństwa, aparat 48MP oraz topowy czip! Zwiększone możliwości Twojego smartfona i dopasowanie do Twoich wymagań były priorytetem przy tworzeniu tego modelu!', '6299.99', NULL, 'Wykrywanie wypadków', 1, 12),
(28, 'Laptop APPLE MacBook Air 2022 13.6\" 8GB 256GB SSD', 'Trzymaj się mocno, bo oto przed Tobą niezawodnie szybki i ultralekki MacBook Air. Zaprojektowany z myślą o spełnieniu oczekiwań swojego użytkownika i doprecyzowany pod każdym kątem, stanowi wydajne rozwiązanie do pracy, codziennych czynności i grania w gry. Sprawdź potęgę ulepszonej generacji układów scalonych, które oferują jeszcze większą szybkość i energooszczędność.', '6199.99', '5899.00', '18 godzin pracy na baterii', 63, 12);

-- --------------------------------------------------------

--
-- Table structure for table `produkt_kategoria`
--

CREATE TABLE `produkt_kategoria` (
  `produkt_kategoria_id` int(11) NOT NULL,
  `produkt_id` int(11) NOT NULL,
  `kategoria_id` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `produkt_kategoria`
--

INSERT INTO `produkt_kategoria` (`produkt_kategoria_id`, `produkt_id`, `kategoria_id`) VALUES
(65, 28, 1),
(67, 1, 1),
(70, 25, 52);

-- --------------------------------------------------------

--
-- Table structure for table `produkt_opinia`
--

CREATE TABLE `produkt_opinia` (
  `produkt_opinia_id` int(11) NOT NULL,
  `opinia` decimal(2,1) NOT NULL,
  `opis` varchar(500) DEFAULT NULL,
  `produkt_id` int(11) NOT NULL,
  `uzytkownik_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `produkt_opinia`
--

INSERT INTO `produkt_opinia` (`produkt_opinia_id`, `opinia`, `opis`, `produkt_id`, `uzytkownik_id`) VALUES
(1, '5.0', 'Świetny laptop! Polecam.', 1, 1),
(5, '4.6', 'Jestem zadowolony z zakupu, jednakże bateria mogłaby trochę więcej trzymać.', 1, 5),
(31, '5.0', 'nnn', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `produkt_parametr`
--

CREATE TABLE `produkt_parametr` (
  `produkt_parametr_id` int(11) NOT NULL,
  `produkt_id` int(11) NOT NULL,
  `parametr_id` int(11) NOT NULL,
  `wartosc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `produkt_parametr`
--

INSERT INTO `produkt_parametr` (`produkt_parametr_id`, `produkt_id`, `parametr_id`, `wartosc`) VALUES
(49, 28, 1, 'Apple M2'),
(50, 28, 4, 'macOS Monterey'),
(52, 1, 1, 'Intel Core i5-11260H'),
(58, 25, 2, '6GB'),
(61, 25, 1, 'A16'),
(62, 25, 4, 'iOS 16'),
(63, 25, 3, '128GB');

-- --------------------------------------------------------

--
-- Table structure for table `produkt_zamowienie`
--

CREATE TABLE `produkt_zamowienie` (
  `produkt_zamowienie_id` bigint(20) NOT NULL,
  `zamowienie_id` int(11) NOT NULL,
  `produkt_id` int(11) NOT NULL,
  `ilosc` mediumint(9) NOT NULL,
  `cena_za_sztuke` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `produkt_zamowienie`
--

INSERT INTO `produkt_zamowienie` (`produkt_zamowienie_id`, `zamowienie_id`, `produkt_id`, `ilosc`, `cena_za_sztuke`) VALUES
(1, 2, 25, 5, '6299.99'),
(2, 3, 25, 2, '6299.99'),
(3, 3, 1, 1, '4849.99'),
(4, 4, 25, 2, '6299.99'),
(5, 4, 28, 1, '5899.00'),
(6, 4, 1, 1, '4849.99'),
(7, 5, 25, 6, '6299.99'),
(8, 5, 28, 11, '5899.00'),
(9, 5, 1, 1, '4849.99'),
(10, 6, 1, 1, '4849.99'),
(11, 7, 1, 1, '4849.99');

-- --------------------------------------------------------

--
-- Table structure for table `stanowisko`
--

CREATE TABLE `stanowisko` (
  `stanowisko_id` tinyint(4) NOT NULL,
  `nazwa` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `stanowisko`
--

INSERT INTO `stanowisko` (`stanowisko_id`, `nazwa`) VALUES
(1, 'Klient'),
(2, 'Administrator'),
(3, 'Twórca');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `status_id` tinyint(4) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`status_id`, `status`) VALUES
(1, 'Zarejestrowano dane przesyłki'),
(2, 'Przesyłka odebrana przez kuriera'),
(3, 'Przesyłka przyjęta w oddziale'),
(4, 'Przekazano do doręczenia'),
(5, 'Przesyłka doręczona');

-- --------------------------------------------------------

--
-- Table structure for table `uzytkownik`
--

CREATE TABLE `uzytkownik` (
  `uzytkownik_id` int(11) NOT NULL,
  `nazwa_uzytkownika` varchar(15) DEFAULT NULL,
  `haslo` varchar(255) DEFAULT NULL,
  `email` varchar(254) NOT NULL,
  `data_urodzenia` date DEFAULT NULL,
  `numer_telefonu` varchar(20) DEFAULT NULL,
  `imie` varchar(30) DEFAULT NULL,
  `nazwisko` varchar(50) DEFAULT NULL,
  `kod_pocztowy` varchar(6) DEFAULT NULL,
  `miasto` varchar(50) DEFAULT NULL,
  `ulica` varchar(50) DEFAULT NULL,
  `nr_domu` varchar(10) DEFAULT NULL,
  `czy_zalogowany` tinyint(1) NOT NULL,
  `stanowisko_id` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `uzytkownik`
--

INSERT INTO `uzytkownik` (`uzytkownik_id`, `nazwa_uzytkownika`, `haslo`, `email`, `data_urodzenia`, `numer_telefonu`, `imie`, `nazwisko`, `kod_pocztowy`, `miasto`, `ulica`, `nr_domu`, `czy_zalogowany`, `stanowisko_id`) VALUES
(1, 'MatiHalek', '$2y$10$0l./zHIsCRqYdt7qLHQkt.vNN86gzbVheu3GKaufnImsbHREBMXv2', 'matihalektest@gmail.com', '1999-08-15', '123456789', 'Mati', 'Hałek', '12-345', 'Warszawa', 'Zielona', '12/10', 1, 3),
(5, 'Mati', '$2y$10$SWFdVJ3M3Ln.bGbzkCIuI.v/pOZT2TaQ3ZsAynhRTQAipOjANFYKW', 'matixd@gmail.com', '2007-01-23', '', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1),
(6, NULL, NULL, 'matihalektest@gmail.com', NULL, '123456789', 'Mati', 'Hałek', '12-345', 'Warszawa', 'Zielona', '12/10', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `zamowienie`
--

CREATE TABLE `zamowienie` (
  `zamowienie_id` int(11) NOT NULL,
  `uzytkownik_id` int(11) NOT NULL,
  `data_zamowienia` datetime NOT NULL,
  `dostawa_id` tinyint(4) NOT NULL,
  `platnosc_id` tinyint(4) NOT NULL,
  `status_id` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `zamowienie`
--

INSERT INTO `zamowienie` (`zamowienie_id`, `uzytkownik_id`, `data_zamowienia`, `dostawa_id`, `platnosc_id`, `status_id`) VALUES
(2, 1, '2023-05-07 21:08:58', 1, 1, 3),
(3, 6, '2023-05-07 21:22:10', 2, 3, 4),
(4, 1, '2023-05-15 00:01:33', 1, 2, 3),
(5, 1, '2023-06-28 18:02:05', 1, 2, 1),
(6, 1, '2023-07-04 02:35:19', 1, 3, 1),
(7, 1, '2023-07-04 02:35:53', 2, 3, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dostawa`
--
ALTER TABLE `dostawa`
  ADD PRIMARY KEY (`dostawa_id`);

--
-- Indexes for table `kategoria`
--
ALTER TABLE `kategoria`
  ADD PRIMARY KEY (`kategoria_id`);

--
-- Indexes for table `parametr`
--
ALTER TABLE `parametr`
  ADD PRIMARY KEY (`parametr_id`);

--
-- Indexes for table `platnosc`
--
ALTER TABLE `platnosc`
  ADD PRIMARY KEY (`platnosc_id`);

--
-- Indexes for table `produkt`
--
ALTER TABLE `produkt`
  ADD PRIMARY KEY (`produkt_id`);

--
-- Indexes for table `produkt_kategoria`
--
ALTER TABLE `produkt_kategoria`
  ADD PRIMARY KEY (`produkt_kategoria_id`),
  ADD KEY `produkt_id` (`produkt_id`),
  ADD KEY `kategoria_id` (`kategoria_id`);

--
-- Indexes for table `produkt_opinia`
--
ALTER TABLE `produkt_opinia`
  ADD PRIMARY KEY (`produkt_opinia_id`),
  ADD KEY `produkt_id` (`produkt_id`),
  ADD KEY `uzytkownik_id` (`uzytkownik_id`);

--
-- Indexes for table `produkt_parametr`
--
ALTER TABLE `produkt_parametr`
  ADD PRIMARY KEY (`produkt_parametr_id`),
  ADD KEY `produkt_id` (`produkt_id`,`parametr_id`),
  ADD KEY `parametr_id` (`parametr_id`);

--
-- Indexes for table `produkt_zamowienie`
--
ALTER TABLE `produkt_zamowienie`
  ADD PRIMARY KEY (`produkt_zamowienie_id`),
  ADD KEY `zamowienie_id` (`zamowienie_id`),
  ADD KEY `produkt_id` (`produkt_id`);

--
-- Indexes for table `stanowisko`
--
ALTER TABLE `stanowisko`
  ADD PRIMARY KEY (`stanowisko_id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `uzytkownik`
--
ALTER TABLE `uzytkownik`
  ADD PRIMARY KEY (`uzytkownik_id`),
  ADD KEY `stanowisko_id` (`stanowisko_id`);

--
-- Indexes for table `zamowienie`
--
ALTER TABLE `zamowienie`
  ADD PRIMARY KEY (`zamowienie_id`),
  ADD KEY `uzytkownik_id` (`uzytkownik_id`),
  ADD KEY `dostawa_id` (`dostawa_id`),
  ADD KEY `platnosc_id` (`platnosc_id`),
  ADD KEY `status_id` (`status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dostawa`
--
ALTER TABLE `dostawa`
  MODIFY `dostawa_id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kategoria`
--
ALTER TABLE `kategoria`
  MODIFY `kategoria_id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `parametr`
--
ALTER TABLE `parametr`
  MODIFY `parametr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `platnosc`
--
ALTER TABLE `platnosc`
  MODIFY `platnosc_id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `produkt`
--
ALTER TABLE `produkt`
  MODIFY `produkt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `produkt_kategoria`
--
ALTER TABLE `produkt_kategoria`
  MODIFY `produkt_kategoria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `produkt_opinia`
--
ALTER TABLE `produkt_opinia`
  MODIFY `produkt_opinia_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `produkt_parametr`
--
ALTER TABLE `produkt_parametr`
  MODIFY `produkt_parametr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `produkt_zamowienie`
--
ALTER TABLE `produkt_zamowienie`
  MODIFY `produkt_zamowienie_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `stanowisko`
--
ALTER TABLE `stanowisko`
  MODIFY `stanowisko_id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `status_id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `uzytkownik`
--
ALTER TABLE `uzytkownik`
  MODIFY `uzytkownik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `zamowienie`
--
ALTER TABLE `zamowienie`
  MODIFY `zamowienie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `produkt_kategoria`
--
ALTER TABLE `produkt_kategoria`
  ADD CONSTRAINT `produkt_kategoria_ibfk_1` FOREIGN KEY (`produkt_id`) REFERENCES `produkt` (`produkt_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produkt_kategoria_ibfk_2` FOREIGN KEY (`kategoria_id`) REFERENCES `kategoria` (`kategoria_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `produkt_opinia`
--
ALTER TABLE `produkt_opinia`
  ADD CONSTRAINT `produkt_opinia_ibfk_1` FOREIGN KEY (`produkt_id`) REFERENCES `produkt` (`produkt_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produkt_opinia_ibfk_2` FOREIGN KEY (`uzytkownik_id`) REFERENCES `uzytkownik` (`uzytkownik_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `produkt_parametr`
--
ALTER TABLE `produkt_parametr`
  ADD CONSTRAINT `produkt_parametr_ibfk_1` FOREIGN KEY (`produkt_id`) REFERENCES `produkt` (`produkt_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produkt_parametr_ibfk_2` FOREIGN KEY (`parametr_id`) REFERENCES `parametr` (`parametr_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `produkt_zamowienie`
--
ALTER TABLE `produkt_zamowienie`
  ADD CONSTRAINT `produkt_zamowienie_ibfk_1` FOREIGN KEY (`zamowienie_id`) REFERENCES `zamowienie` (`zamowienie_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produkt_zamowienie_ibfk_2` FOREIGN KEY (`produkt_id`) REFERENCES `produkt` (`produkt_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `uzytkownik`
--
ALTER TABLE `uzytkownik`
  ADD CONSTRAINT `uzytkownik_ibfk_1` FOREIGN KEY (`stanowisko_id`) REFERENCES `stanowisko` (`stanowisko_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `zamowienie`
--
ALTER TABLE `zamowienie`
  ADD CONSTRAINT `zamowienie_ibfk_1` FOREIGN KEY (`uzytkownik_id`) REFERENCES `uzytkownik` (`uzytkownik_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `zamowienie_ibfk_2` FOREIGN KEY (`dostawa_id`) REFERENCES `dostawa` (`dostawa_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `zamowienie_ibfk_3` FOREIGN KEY (`platnosc_id`) REFERENCES `platnosc` (`platnosc_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `zamowienie_ibfk_4` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
