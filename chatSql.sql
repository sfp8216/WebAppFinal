--
-- Table structure for table `546ArchChat`
--

CREATE TABLE IF NOT EXISTS `546ArchChat` (
  `messageId` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `message` varchar(200) NOT NULL,
  `timeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`messageId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `546ArchChat`
--

INSERT INTO `546ArchChat` (`messageId`, `name`, `message`, `timeStamp`) VALUES
(1, 'danny', 'Hello all', '2012-03-28 20:10:52'),
(2, 'danny', 'anyone here?', '2012-03-28 20:11:27'),
(3, 'fred', 'Hello danny...', '2012-03-28 20:12:06'),
(4, 'Jenny', 'Can I play?', '2012-04-04 10:38:16');
