CREATE TABLE IF NOT EXISTS `product_tagtype` (
  `product_tagtype_id`      int(11)         NOT NULL COMMENT 'Product Tag Type Id',
  `product_tagtype_name`    varchar(255)    NOT NULL COMMENT 'Product Tag Type Name',
  PRIMARY KEY (`product_tagtype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;