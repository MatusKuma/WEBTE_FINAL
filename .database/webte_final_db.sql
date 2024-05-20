-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+jammy2
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: localhost:3306
-- Čas generovania: Po 20.Máj 2024, 18:08
-- Verzia serveru: 8.0.36-0ubuntu0.22.04.1
-- Verzia PHP: 8.3.3-1+ubuntu22.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `webte_final_db`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `answers_open`
--

CREATE TABLE `answers_open` (
  `id` int NOT NULL,
  `question_id` int NOT NULL,
  `timestamp` datetime NOT NULL,
  `answer` varchar(255) NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `answers_options`
--

CREATE TABLE `answers_options` (
  `id` int NOT NULL,
  `question_id` int NOT NULL,
  `timestamp` datetime NOT NULL,
  `answer` varchar(255) NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `questions_open`
--

CREATE TABLE `questions_open` (
  `id` int NOT NULL,
  `creator_id` int NOT NULL,
  `timestamp` datetime NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `code` varchar(5) NOT NULL,
  `type` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `questions_options`
--

CREATE TABLE `questions_options` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `correct_answer` varchar(255) NOT NULL,
  `option_1` varchar(255) DEFAULT NULL,
  `option_2` varchar(255) DEFAULT NULL,
  `option_3` varchar(255) DEFAULT NULL,
  `option_4` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL,
  `creator_id` int NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `code` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Sťahujem dáta pre tabuľku `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `isAdmin`) VALUES
(1, 'admin', '$argon2id$v=19$m=65536,t=4,p=1$a2tUcC8yUkdXcktVYkx0WQ$DfZWqnv3ub3Z5XaDLXTnceUzTrJTK+F+1/UqSplCrT8', 1),
(19, 'timotej', '$argon2id$v=19$m=65536,t=4,p=1$LjNMR3JORGhqTWtGVFBhSA$eMlYFKV/jWYeRePCyn8Bo5CUnQJrvpUW3Mbh4hoIWAc', 0),
(29, 'matus', '$argon2id$v=19$m=65536,t=4,p=1$VGN4U2h5U0lmVWo0eXZkbw$SIVSbeWu5821f/DD2VZhAkykocft/EJlvy+8qfQnpVw', 0);

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `answers_open`
--
ALTER TABLE `answers_open`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pre tabuľku `answers_options`
--
ALTER TABLE `answers_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pre tabuľku `questions_open`
--
ALTER TABLE `questions_open`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_fk1` (`creator_id`);

--
-- Indexy pre tabuľku `questions_options`
--
ALTER TABLE `questions_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_fk2` (`creator_id`);

--
-- Indexy pre tabuľku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `answers_open`
--
ALTER TABLE `answers_open`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pre tabuľku `answers_options`
--
ALTER TABLE `answers_options`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pre tabuľku `questions_open`
--
ALTER TABLE `questions_open`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pre tabuľku `questions_options`
--
ALTER TABLE `questions_options`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pre tabuľku `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `questions_open`
--
ALTER TABLE `questions_open`
  ADD CONSTRAINT `creator_fk1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Obmedzenie pre tabuľku `questions_options`
--
ALTER TABLE `questions_options`
  ADD CONSTRAINT `creator_fk2` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
