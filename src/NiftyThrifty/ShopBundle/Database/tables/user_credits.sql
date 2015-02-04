CREATE TABLE `user_credits` (
  `user_credits_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `user_credits_date` date NOT NULL COMMENT 'Date',
  `user_credits_date_end` date NOT NULL COMMENT 'Date end',
  `user_credits_value` int(11) NOT NULL COMMENT 'Amount',
  `user_id` bigint(20) NOT NULL COMMENT 'User',
  PRIMARY KEY (`user_credits_id`)
) ENGINE=MyISAM AUTO_INCREMENT=435806 DEFAULT CHARSET=utf8 COMMENT='User credits'