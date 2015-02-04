CREATE TABLE IF NOT EXISTS `product_tag` (
  `product_tag_id`      int(11)         NOT NULL COMMENT 'Product Tag Id',
  `product_tag_name`    varchar(255)    NOT NULL COMMENT 'Product Tag Name',
  `product_tag_slug`    varchar(255)    NOT NULL COMMENT 'Tag Slug',
  `product_tagtype_id`  int(11)         NOT NULL COMMENT 'Product Tag Type Id',
  PRIMARY KEY (`product_tag_id`),
  INDEX (`product_tagtype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;