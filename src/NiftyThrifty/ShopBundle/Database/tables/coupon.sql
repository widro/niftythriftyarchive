CREATE TABLE `coupon` (
  `coupon_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `coupon_code` varchar(255) NOT NULL COMMENT 'Code',
  `coupon_date_start` date DEFAULT NULL COMMENT 'Date Start',
  `coupon_date_end` date DEFAULT NULL COMMENT 'Date End',
  `coupon_percent` float DEFAULT NULL COMMENT 'Reduction Percent',
  `coupon_amount` float DEFAULT NULL COMMENT 'Reduction Amount',
  `coupon_quantity_limited` enum('true','false') NOT NULL DEFAULT 'false' COMMENT 'Quantity Limited ?',
  `coupon_quantity` int(11) DEFAULT NULL COMMENT 'Quantity Available',
  `coupon_unique` enum('true','false') NOT NULL DEFAULT 'false' COMMENT 'Unique',
  `coupon_date_add` datetime NOT NULL COMMENT 'Date Add',
  `coupon_free_shipping` enum('true','false') NOT NULL COMMENT 'Free Shipping',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'User',
  PRIMARY KEY (`coupon_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COMMENT='Coupons' 