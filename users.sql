SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `forename` varchar(25) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `email_address` varchar(150) NOT NULL,
  `old_password` varchar(200) NOT NULL,
  `old_password_salt` varchar(10) NOT NULL,
  `registered` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `password` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `forename`, `surname`, `email_address`, `old_password`, `old_password_salt`, `registered`, `last_login`, `password`) VALUES
(1, 'Joe', 'Bloggs', 'jbloggs@bostonseeds.co.uk', '45ff178a1fb2b4a6bbb8b553231c1503', 'prmrfhvq4x', '2020-09-01 09:00:00', '2020-10-11 09:00:00', NULL),
(2, 'John', 'Doe', 'jdoe@bostonseeds.co.uk', '4b7e2f11f6ef4083bf5fa5bbbf6f1c4a', 'bs1xxogy56', '2020-09-01 09:00:00', '2020-10-19 09:00:00', NULL),
(3, 'Foo', 'Bar', 'fbar@bostonseeds.co.uk', 'bae2911a2ef2e4cb371180565bc4d008', 'a4icm8uo79', '2020-09-01 09:00:00', '2020-10-12 09:00:00', NULL);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
