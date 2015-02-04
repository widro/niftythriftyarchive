CREATE TABLE IF NOT EXISTS `xproduct_tag` (
  `product_id`      int(11) NOT NULL COMMENT 'Product ID',
  `product_tag_id`  int(11) NOT NULL COMMENT 'Product Tag ID',
  PRIMARY KEY (`product_tag_id`, `product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;