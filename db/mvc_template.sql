SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databse: `mvc_template`
--


-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `log_login` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` varchar(100) DEFAULT NULL COMMENT 'Integer if user exists, string if did not exists when the attempt to log in',
  `msg_type` int(11) NOT NULL,
  `msg_key` varchar(100) DEFAULT NULL COMMENT 'Key of the error to convert it into a string',
  `ip` varchar(40) DEFAULT NULL,
  `referer_url` varchar(500) DEFAULT NULL,
  `browser_raw` varchar(500) DEFAULT NULL,
  `browser_fix` varchar(500) DEFAULT NULL COMMENT 'Fixed by the script',
  `browser_version` varchar(100) DEFAULT NULL,
  `operating_system` varchar(100) DEFAULT NULL,
  `date_insert` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `log_tracking` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint(20) unsigned DEFAULT NULL,
  `hash_visit` varchar(32) NOT NULL COMMENT 'Unique hash per visit',
  `ip` varchar(40) DEFAULT NULL,
  `referer_url` varchar(500) DEFAULT NULL,
  `referer_hash_page` varchar(32) DEFAULT NULL,
  `referer_hash_full` varchar(32) DEFAULT NULL COMMENT '8-characters hash of referer_url',
  `browser_raw` varchar(500) DEFAULT NULL,
  `browser_fix` varchar(500) DEFAULT NULL COMMENT 'Fixed by the script',
  `browser_version` varchar(100) DEFAULT NULL,
  `operating_system` varchar(100) DEFAULT NULL,
  `date_insert` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `roles_data` (
  `id` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `date_update` datetime NOT NULL,
  `date_insert` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `roles_data` (`id`, `name`, `date_update`, `date_insert`) VALUES
('ROLE_ADMIN', 'Administrator', '0000-00-00 00:00:00', '2016-10-06 00:00:00'),
('ROLE_SUPERADMIN', 'SuperAdmin', '0000-00-00 00:00:00', '2016-10-06 00:00:00'),
('ROLE_USER', 'User', '0000-00-00 00:00:00', '2016-10-06 00:00:00');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `roles_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `date_update` datetime NOT NULL,
  `date_insert` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `roles_groups` (`id`, `name`, `date_update`, `date_insert`) VALUES
(1, 'SuperAdmin', '0000-00-00 00:00:00', '2016-10-06 00:00:00'),
(2, 'Administrator', '0000-00-00 00:00:00', '2016-10-06 00:00:00'),
(3, 'User', '0000-00-00 00:00:00', '2016-10-06 00:00:00');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `roles_rel` (
  `id_role_group` bigint(20) unsigned NOT NULL,
  `id_role` varchar(50) NOT NULL,
  `date_insert` datetime NOT NULL,
  PRIMARY KEY (`id_role_group`,`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `roles_rel` (`id_role_group`, `id_role`, `date_insert`) VALUES
(1, 'ROLE_SUPERADMIN', '2016-10-06 00:00:00'),
(2, 'ROLE_ADMIN', '2016-10-06 00:00:00'),
(2, 'ROLE_USER', '2016-10-06 00:00:00'),
(3, 'ROLE_USER', '2016-10-06 00:00:00');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `session` (
  `id_user` bigint(20) unsigned NOT NULL,
  `cookie` varchar(32) NOT NULL,
  `date_update` datetime NOT NULL COMMENT 'Last date update of the cookie',
  `date_insert` datetime NOT NULL,
  UNIQUE KEY `cookie` (`cookie`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users_data` (
  `id_user` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `email` varchar(200) NOT NULL,
  `id_role_group` bigint(20) unsigned NOT NULL COMMENT '0: no role',
  `date_update` datetime NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `users_data`
--

INSERT INTO `users_data` (`id_user`, `name`, `surname`, `alias`, `email`, `id_role_group`, `date_update`) VALUES
(1, 'Dimas', 'Muñoz', NULL, 'email@hosting.com', 1, '2017-01-01 00:00:00');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users_login` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `last_online` datetime DEFAULT NULL,
  `date_update` datetime NOT NULL,
  `date_insert` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `users_login` (`id`, `username`, `password`, `is_active`, `last_online`, `date_update`, `date_insert`) VALUES
(1, 'username', '$2y$12$jcRt8gZkK0dSE4UVTtNHheZz2R.NoZK6GIhXUapUIF5EK8zI/jxE.', 1, '2017-04-25 23:38:56', '2016-09-25 12:31:09', '2016-06-28 00:00:00');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
