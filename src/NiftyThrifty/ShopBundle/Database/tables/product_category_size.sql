CREATE TABLE `product_category_size` (
  `product_category_size_id`    bigint(20)  NOT NULL,
  `product_category_size_name`  varchar(63) NOT NULL,
  `product_category_size_value` varchar(63) NOT NULL,
  `product_category_size_order` bigint(20)  NOT NULL,
  `product_category_id`         bigint(20)  NOT NULL,
  PRIMARY KEY (`product_category_size_id`),
  INDEX (`product_category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Product category sizes. These are per category';