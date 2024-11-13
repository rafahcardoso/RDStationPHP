CREATE TABLE `app_credentials` (
  `client_id` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `client_secret` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `redirect_url` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refresh_token` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `token_rdsm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refresh` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `token` varchar(900) COLLATE latin1_general_ci NOT NULL,
  `update_date` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
