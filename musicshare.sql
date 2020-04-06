-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 25, 2020 at 04:33 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `musicshare`
--

-- --------------------------------------------------------

--
-- Table structure for table `groupcomments`
--

CREATE TABLE `groupcomments` (
  `id` int(15) NOT NULL,
  `commenttext` varchar(1000) NOT NULL,
  `postid` int(12) NOT NULL,
  `commenter` varchar(16) NOT NULL,
  `commentdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `likes` int(6) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `groupcomments`
--

INSERT INTO `groupcomments` (`id`, `commenttext`, `postid`, `commenter`, `commentdate`, `likes`) VALUES
(2, 'test comment in browser phpmyadmin', 2, 'joe', '2020-02-12 06:33:38', 0),
(15, 'test comment', 37, 'joe', '2020-02-13 01:11:35', 0),
(17, 'test', 37, 'joe', '2020-02-13 01:11:55', 0),
(18, 'dont judge my top songs', 2, 'josh', '2020-02-13 01:38:53', 0),
(20, 'big test', 37, 'josh', '2020-02-13 03:48:43', 0),
(22, 'send comment', 37, 'joe', '2020-02-14 02:50:20', 0),
(25, 'tes', 43, 'joe', '2020-02-14 03:30:56', 0),
(26, 'soft hairs', 43, 'joe', '2020-02-14 04:28:19', 0),
(27, 'oh sure fires', 45, 'joe', '2020-02-14 04:36:09', 0),
(28, 'border line is 🔥', 47, 'joe', '2020-02-15 01:26:13', 0),
(35, 'ajax yo', 48, 'josh', '2020-02-15 08:08:25', 0),
(45, 'lucky 13', 48, 'josh', '2020-02-15 08:18:02', 0),
(49, 'ajaxxxxx', 48, 'josh', '2020-02-15 08:24:58', 0),
(50, 'ajax', 48, 'josh', '2020-02-15 17:43:31', 0),
(51, 'saturdayyy', 48, 'josh', '2020-02-15 17:45:35', 0),
(54, 'satttt', 48, 'josh', '2020-02-15 17:50:36', 0),
(55, 'delegation', 48, 'josh', '2020-02-15 18:56:34', 0),
(56, 'DELEGATION!!!', 48, 'josh', '2020-02-15 18:56:46', 0),
(66, 'nice', 44, 'josh', '2020-02-15 21:54:20', 0),
(71, 'great album', 47, 'josh', '2020-02-15 22:45:13', 0),
(74, 'test', 47, 'josh', '2020-02-16 02:05:25', 0),
(76, 'this playlist sucks and so do you', 61, 'josh', '2020-02-16 19:38:38', 0),
(79, 'comment', 63, 'josh', '2020-02-18 05:57:43', 0),
(80, 'well that could have been better', 105, 'josh', '2020-02-19 16:04:02', 0),
(85, 'great song\n', 98, 'josh', '2020-02-22 03:54:08', 0),
(86, 'comment', 116, 'josh', '2020-02-22 07:16:26', 0),
(87, 'test', 121, 'josh', '2020-02-22 16:51:17', 0),
(89, 'test', 121, 'josh', '2020-02-23 04:17:37', 0),
(90, 't', 121, 'josh', '2020-02-23 18:47:28', 0);

-- --------------------------------------------------------

--
-- Table structure for table `grouplikes`
--

CREATE TABLE `grouplikes` (
  `id` int(11) NOT NULL,
  `uid` tinytext NOT NULL,
  `groupid` int(11) NOT NULL,
  `unseenposts` int(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `grouplikes`
--

INSERT INTO `grouplikes` (`id`, `uid`, `groupid`, `unseenposts`) VALUES
(1, 'joe', 14, 0),
(2, 'joe', 15, 0),
(5, 'josh', 1, 0),
(8, 'josh', 2, 0),
(9, 'joe', 1, 9),
(10, 'joe', 21, 0),
(16, 'joe', 20, 1),
(17, 'joe', 18, 0),
(19, 'josh', 20, 0),
(21, 'joe', 23, 0),
(22, 'joe', 24, 0),
(23, 'testing', 24, 0),
(24, 'joe', 22, 0),
(26, 'josh', 17, 0),
(27, 'josh', 25, 0),
(35, 'josh', 32, 0),
(36, 'josh', 33, 0),
(37, 'joe', 33, 1),
(38, 'joshua', 33, 1),
(42, 'bobbo', 20, 1),
(43, 'bobbo', 1, 9),
(45, 'bobbo', 2, 0),
(49, 'bobbo', 18, 0),
(62, 'josh', 21, 0);

-- --------------------------------------------------------

--
-- Table structure for table `groupPostlikes`
--

CREATE TABLE `groupPostlikes` (
  `id` int(15) NOT NULL,
  `postid` int(15) NOT NULL,
  `uid` varchar(16) NOT NULL,
  `likedate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `groupPostlikes`
--

INSERT INTO `groupPostlikes` (`id`, `postid`, `uid`, `likedate`) VALUES
(1, 37, 'joe', '2020-02-20 03:40:01'),
(5, 37, 'joe', '2020-02-20 03:40:01'),
(6, 30, 'joe', '2020-02-20 03:40:01'),
(37, 44, 'josh', '2020-02-20 03:40:01'),
(38, 47, 'josh', '2020-02-20 03:40:01'),
(39, 47, 'josh', '2020-02-20 03:40:01'),
(40, 48, 'josh', '2020-02-20 03:40:01'),
(42, 2, 'josh', '2020-02-20 03:40:01'),
(46, 31, 'josh', '2020-02-20 03:40:01'),
(50, 64, 'josh', '2020-02-20 03:40:01'),
(58, 53, 'josh', '2020-02-20 03:40:01'),
(66, 98, 'josh', '2020-02-20 05:03:01'),
(67, 45, 'josh', '2020-02-20 05:11:54'),
(68, 100, 'josh', '2020-02-22 03:53:59');

-- --------------------------------------------------------

--
-- Table structure for table `groupposts`
--

CREATE TABLE `groupposts` (
  `id` int(12) NOT NULL,
  `link` varchar(100) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `postdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `groupid` int(12) NOT NULL,
  `poster` varchar(16) NOT NULL,
  `likes` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `groupposts`
--

INSERT INTO `groupposts` (`id`, `link`, `type`, `description`, `postdate`, `groupid`, `poster`, `likes`) VALUES
(1, '5DBmXF7QO43Cuy9yqva116', 'spotLink', 'This is my first post. I hope it works', '2020-02-23 22:54:06', 1, 'josh', 0),
(2, '37i9dQZF1E9T3z8JnpNQes', 'spotPlaylist', 'Okay lets try a playlist then', '2020-02-23 22:54:08', 1, 'josh', 0),
(20, '', 'text', '          tets comment', '2020-02-08 05:19:28', 15, 'joe', 0),
(21, '33ihZWqvTNPd6MfNDGZTUX', 'spotLink', '          testing this group', '2020-02-23 22:54:55', 15, 'joe', 0),
(22, '', 'text', '          just a comment', '2020-02-09 19:10:19', 15, 'joe', 0),
(23, '33ihZWqvTNPd6MfNDGZTUX', 'spotLink', '          red wine agin', '2020-02-23 22:59:19', 15, 'joe', 0),
(24, '33ihZWqvTNPd6MfNDGZTUX', 'spotLink', '          red wine again', '2020-02-23 23:00:18', 17, 'joe', 0),
(27, '', 'text', 'test text post here', '2020-02-10 05:23:42', 16, 'joe', 0),
(30, '3TVXtAsR1Inumwj472S9r4', 'spotArtist', 'lets try an artist for real now', '2020-02-23 23:00:01', 1, 'joe', 0),
(31, '1A3nVEWRJ8yvlPzawHI1pQ', 'spotAlbum', 'let\r\ns try up an album also for sizing', '2020-02-23 23:00:10', 1, 'joe', 0),
(37, '', 'text', 'text post here', '2020-02-12 06:13:05', 1, 'joe', 0),
(43, '5IR7Ui6MB7MrFZfF5hsoIH', 'spotLink', 'hair', '2020-02-23 22:50:40', 1, 'joe', 0),
(44, '33ihZWqvTNPd6MfNDGZTUX', 'spotLink', 'red wines', '2020-02-23 23:00:37', 1, 'joe', 0),
(45, '2N2gukfZet8Oe4aYR5Apd6', 'spotLink', 'surefires', '2020-02-23 23:00:42', 1, 'joe', 0),
(47, '31qVWUdRrlb8thMvts0yYL', 'spotAlbum', 'check out the new album. comment on your fav song. Keep typing stuff to see how it looks in the website lsdkjfladflskjflksdjflskdfasjdfsjd', '2020-02-23 23:00:32', 1, 'joe', 0),
(48, '', 'text', 'texts post here', '2020-02-15 01:48:51', 1, 'josh', 0),
(51, '3Ni6ZPaSxnvzWTpP9chwY4', 'spotLink', 'testing submit function', '2020-02-23 23:00:48', 20, 'josh', 0),
(53, '0aA9rYw8PEv9G7tVIJ9dKg', 'spotAlbum', 'album link', '2020-02-23 23:00:54', 20, 'josh', 0),
(57, '7pBrj5rt4SSxXwFKOyZfHR', 'spotLink', 'abso', '2020-02-23 23:01:00', 1, 'josh', 0),
(58, '1NfrmcXk8xNennyxQ57JcW', 'spotAlbum', 'born sinner for kyle', '2020-02-23 23:01:05', 1, 'josh', 0),
(59, '', 'spotArtist', 'rhcp', '2020-02-23 22:54:12', 1, 'josh', 0),
(61, '6gU2UCKIFkSGduu7eNCjBn', 'spotPlaylist', 'kyle playlist', '2020-02-23 23:01:11', 1, 'josh', 0),
(63, '3arNdjotCvtiiLFfjKngMc', 'spotAlbum', 'ALLA', '2020-02-23 23:01:16', 1, 'josh', 0),
(64, '1yAwtBaoHLEDWAnWR87hBT', 'spotArtist', 'the mouse', '2020-02-23 23:01:22', 1, 'josh', 0),
(87, '1PS1QMdUqOal0ai3Gt7sDQ', 'spotLink', 'track', '2020-02-23 23:01:27', 1, 'josh', 0),
(92, '7qvsl2pYzrsYgPeFBN5jxp', 'spotLink', 'kyles fav song', '2020-02-23 23:01:48', 1, 'josh', 0),
(93, '20fAoPjfYltmd3K3bO7gbt', 'spotLink', 'test', '2020-02-23 23:02:10', 25, 'josh', 0),
(94, '27ZhKzjHoE0UUlyMXu0ZFa', 'spotLink', 'classic track', '2020-02-23 23:02:32', 1, 'josh', 0),
(98, '5zVW76plgG9B4904OXXXZd', 'spotLink', 'new post', '2020-02-23 23:02:38', 1, 'josh', 0),
(100, '', 'text', 'asfdafsdf', '2020-02-19 15:49:12', 1, 'josh', 0),
(105, '4ejyZ4uJEXUURcgKp1kP8v', 'spotLink', 'test new song', '2020-02-23 23:02:45', 1, 'josh', 0),
(106, '5yY9lUy8nbvjM1Uyo1Uqoc', 'spotLink', 'test', '2020-02-23 23:02:51', 33, 'bobbo', 0),
(107, '7HaggpJGAfEgH8wyMLOIPl', 'spotLink', 'test', '2020-02-23 23:02:56', 33, 'bobbo', 0),
(108, '', 'text', 'ldkjfldkf', '2020-02-19 19:56:47', 33, 'bobbo', 0),
(115, '4LwU4Vp6od3Sb08CsP99GC', 'spotLink', 'testing', '2020-02-23 23:03:02', 1, 'josh', 0),
(116, '3TVXtAsR1Inumwj472S9r4', 'spotArtist', 'drizzyyy', '2020-02-23 23:03:07', 1, 'josh', 0),
(117, '19gEmPjfqSZT0ulDRfjl0m', 'spotLink', 'free shmurda', '2020-02-23 23:03:17', 20, 'josh', 0),
(120, '3gQZdiGlObKaY274Q3lnoH', 'spotLink', '', '2020-02-23 23:03:22', 1, 'josh', 0),
(121, '27LyQFdQbui4TnRs05cZ8C', 'spotLink', '', '2020-02-23 23:03:27', 1, 'josh', 0),
(125, '658MpWKxDiBr29XMvxRV2s', 'spotLink', '', '2020-02-23 23:03:32', 1, 'josh', 0);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(12) NOT NULL,
  `name` varchar(25) NOT NULL,
  `owner` varchar(16) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT 0,
  `postcount` int(6) NOT NULL DEFAULT 0,
  `likecount` int(6) NOT NULL DEFAULT 1,
  `genre` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `owner`, `private`, `postcount`, `likecount`, `genre`) VALUES
(1, 'josh_group', 'josh', 0, 35, 3, 'hip-hop'),
(2, 'josh-group-2', 'josh', 0, 0, 3, 'hip-hop'),
(3, 'random_words', 'jimbo', 0, 0, 1, 'hip-hop'),
(4, 'joesgroup', 'joe', 1, 0, 1, 'hip-hop'),
(5, 'joespubgroup', 'joe', 1, 0, 1, 'hip-hop'),
(6, 'joey', 'joe', 1, 0, 1, 'hip-hop'),
(7, 'joshh22', 'joe', 1, 0, 1, 'hip-hop'),
(8, 'joshh', 'joe', 1, 0, 1, 'hip-hop'),
(9, 'jheg', 'joe', 1, 0, 1, 'hip-hop'),
(10, 'jheg22', 'joe', 1, 0, 1, 'hip-hop'),
(11, 'jhhhh', 'joe', 1, 0, 1, 'hip-hop'),
(12, 'jjhh', 'joe', 1, 0, 1, 'hip-hop'),
(13, 'kyles group', 'joe', 1, 0, 1, 'hip-hop'),
(14, 'kkllkk', 'joe', 1, 0, 2, 'hip-hop'),
(15, 'joshtest', 'joe', 1, 4, 2, 'hip-hop'),
(16, 'josssh', 'joe', 0, 1, 1, 'hip-hop'),
(17, 'josh2222', 'joe', 0, 1, 3, 'hip-hop'),
(18, 'josh1234', 'joe', 0, 0, 3, 'hip-hop'),
(19, 'josh123333', 'joe', 1, 0, 1, 'hip-hop'),
(20, 'joshhhhyyy', 'joe', 0, 3, 2, 'hip-hop'),
(21, 'testingroup', 'joe', 0, 0, 2, 'hip-hop'),
(22, 'group2', 'joe', 0, 0, 1, 'hiphop'),
(23, 'jehtest', 'joe', 0, 0, 1, 'variety'),
(24, 'bigboys23', 'joe', 0, 0, 2, 'randb'),
(25, 'josh long teset group', 'josh', 1, 1, 1, 'variety'),
(32, 'jehh123', 'josh', 0, 0, 1, ''),
(33, 'jehh12345', 'josh', 0, 3, 1, 'pop');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `uid` varchar(16) NOT NULL,
  `id` int(15) NOT NULL,
  `type` varchar(16) NOT NULL,
  `contentid` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`uid`, `id`, `type`, `contentid`) VALUES
('joe', 1, 'group', 1);

-- --------------------------------------------------------

--
-- Table structure for table `outstandinginvites`
--

CREATE TABLE `outstandinginvites` (
  `id` int(11) NOT NULL,
  `email` varchar(40) NOT NULL,
  `groupid` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `outstandinginvites`
--

INSERT INTO `outstandinginvites` (`id`, `email`, `groupid`) VALUES
(1, 'heggen.josh@gmail.com', 23),
(4, 'jheggen@wisc.edu', 25);

-- --------------------------------------------------------

--
-- Table structure for table `publiccomments`
--

CREATE TABLE `publiccomments` (
  `id` int(15) NOT NULL,
  `commenttext` varchar(1000) NOT NULL,
  `postid` int(12) NOT NULL,
  `commenter` varchar(16) NOT NULL,
  `commentdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `likes` int(6) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `publicPostlikes`
--

CREATE TABLE `publicPostlikes` (
  `id` int(15) NOT NULL,
  `postid` int(15) NOT NULL,
  `uid` varchar(16) NOT NULL,
  `likedate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `publicPostlikes`
--

INSERT INTO `publicPostlikes` (`id`, `postid`, `uid`, `likedate`) VALUES
(1, 4, 'josh', '2020-02-22 06:02:08'),
(4, 7, 'josh', '2020-02-23 03:52:04'),
(5, 5, 'josh', '2020-02-23 03:52:12');

-- --------------------------------------------------------

--
-- Table structure for table `publicposts`
--

CREATE TABLE `publicposts` (
  `id` int(11) NOT NULL,
  `genre` varchar(30) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `link` varchar(100) NOT NULL,
  `poster` varchar(16) NOT NULL,
  `type` varchar(16) NOT NULL,
  `postdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `likes` int(6) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `publicposts`
--

INSERT INTO `publicposts` (`id`, `genre`, `description`, `link`, `poster`, `type`, `postdate`, `likes`) VALUES
(1, 'indie', 'first song for the public feed', '33ihZWqvTNPd6MfNDGZTUX', 'josh', 'spotLink', '2020-02-10 00:59:26', 1),
(2, 'hiphop', 'test hiphop feed', '2BuBXn15gj4MNYZydyWgrk', 'josh', 'spotLink', '2020-02-15 02:18:56', 0),
(3, 'hiphop', 'Joe$$', '1wvPULq0vY0ajlfTriADX7', 'josh', 'spotLink', '2020-02-22 05:34:01', 0),
(5, 'rock', '', '0ScgmigVOJr2mFsAtwFQmz', 'josh', 'spotLink', '2020-02-22 06:08:47', 0),
(6, 'rock', 'test', '5ikdUUm6JbnEVnp35c7dvy', 'josh', 'spotLink', '2020-02-22 06:10:33', 0),
(7, 'hiphop', '', '46OFHBw45fNi7QNjSetITR', 'josh', 'spotLink', '2020-02-22 17:24:54', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(12) NOT NULL,
  `uid` tinytext NOT NULL,
  `password` longtext NOT NULL,
  `email` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uid`, `password`, `email`) VALUES
(1, 'jheggen', 'timmy123', 'timm@gmail.com'),
(2, 'joe', '$2y$10$2lzMDwYzdFLjkeMy9baLcu1ZfD7lLvKP.34zvSi/PlAF6HSuzCDbS', 'joe@gmail.com'),
(4, 'bob', '$2y$10$jkLl.PLuCP52TdIZFDWdOe91GIzGDLaU5xwYg0YUfLneSpoGaIFlC', 'bob@epic.com'),
(5, 'josh', '$2y$10$0iS5GJdRF1nT/8VREHga6OdyXodqptUEEIfSknHW32WjfQ7uyQ7MC', 'josh@gmail.com'),
(6, 'josh12', '$2y$10$dt1j4GPQcuFqPmlWGK4tpOBNWTvfrYXfmlyShihYPo8DiWh4oiCpe', 'joshua@gmail.com'),
(7, 'jheeee', '$2y$10$7lG7Fw3gbuq6YbT2ScTv4O40DanE7zLQft1hFeRaqxEGVuxrLM.re', 'jheee@gmail.com'),
(8, 'jimm', '$2y$10$3E7phRczawGVzyEfWLGsMuzG6joymAIJoRpdXaFEboSOiVZrFWGn2', 'jimm@gmail.com'),
(9, 'jimmmm', '$2y$10$jwVtC9YIIGgsYdq6Yt1enecXUtrJDjufcdTuxRWOFQmvwhBt7qmoK', 'timmy@gmial.com'),
(10, 'jimmmm1', '$2y$10$sjxSq8TFS6RdLLKMeG4tZeBP8z3WLLxIRGMBr2NNhP7KjBOn2JPBK', 'timm1y@gmial.com'),
(11, '123123', '$2y$10$VwZHnZ2f5d.dO3ObcKEpbeOWvtxmQH14eOAvUIQlRrI0UHx2zBKje', '123123@gmail.com'),
(12, 'tim28', '$2y$10$NX6V21plEpd9l/xf4UqWgOfA5QAmiAtcmDIqDDrnV.WWGPEOhOVK6', 'tim@gmilll.com'),
(13, 'bobb', '$2y$10$xvDREl4mVHsSkXrImbseVePeTp3UqWZSyc6.YLDICDxJruECx1kuu', 'last@gmail.com'),
(14, 'joshua', '$2y$10$zy0DMKYayTuEiVd9ZRgEc./mc9r1CnZs5yw5cxPxqKVerTZ4pVQs.', 'jheggen@wisc.edu'),
(15, 'testing', '$2y$10$g6833BHGjSu8NIQpLPNqcewMxNQ0EVxMpyCciX.fKZiY791sbBOiu', 'testing@gmail.com'),
(16, 'test22', '$2y$10$YKXFPeP63T3l3d6CUqUTWuzWsGMwFBOLL6Ztv0WktVP.q2OXYUHNi', 'tst22@gmail.com'),
(17, 'bobbo', '$2y$10$gIOoT8Sc7rTsg97qVkRXK.DzRSSeaMiWXxGN.qLC8fKVhz9v6BLlK', 'bobby22@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groupcomments`
--
ALTER TABLE `groupcomments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grouplikes`
--
ALTER TABLE `grouplikes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groupPostlikes`
--
ALTER TABLE `groupPostlikes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groupposts`
--
ALTER TABLE `groupposts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `outstandinginvites`
--
ALTER TABLE `outstandinginvites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publiccomments`
--
ALTER TABLE `publiccomments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publicPostlikes`
--
ALTER TABLE `publicPostlikes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publicposts`
--
ALTER TABLE `publicposts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groupcomments`
--
ALTER TABLE `groupcomments`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `grouplikes`
--
ALTER TABLE `grouplikes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `groupPostlikes`
--
ALTER TABLE `groupPostlikes`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `groupposts`
--
ALTER TABLE `groupposts`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `outstandinginvites`
--
ALTER TABLE `outstandinginvites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `publiccomments`
--
ALTER TABLE `publiccomments`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publicPostlikes`
--
ALTER TABLE `publicPostlikes`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `publicposts`
--
ALTER TABLE `publicposts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
