CREATE TABLE `product_category` (
  `product_category_id`     bigint(20)  NOT NULL            COMMENT 'Id',
  `product_category_name`   varchar(63) NOT NULL            COMMENT 'Name',
  `in_navigation`           tinyint(1)  NOT NULL DEFAULT 0  COMMENT 'Display in navigation or not',
  `navigation_order`        tinyint(1)  NOT NULL DEFAULT 0  COMMENT 'Navigation bar order',
  PRIMARY KEY (`product_category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Product Categories' ;