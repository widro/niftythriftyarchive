CREATE TABLE `newsletter` (
  `newsletter_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `newsletter_name` varchar(64) NOT NULL COMMENT 'Name',
  `newsletter_title` varchar(255) NOT NULL COMMENT 'Title',
  `newsletter_link` varchar(255) DEFAULT 'https://www.niftythrifty.com/' COMMENT 'Link',
  `newsletter_collection_img` varchar(255) NOT NULL COMMENT 'Collection img',
  `newsletter_product1_link` varchar(255) DEFAULT NULL COMMENT 'Product 1 Link',
  `newsletter_product1_img` varchar(255) DEFAULT NULL COMMENT 'Product1 img',
  `newsletter_product2_link` varchar(255) DEFAULT NULL COMMENT 'Product 2 Link',
  `newsletter_product2_img` varchar(255) DEFAULT NULL COMMENT 'Product2 img',
  `newsletter_blast_id` bigint(20) DEFAULT NULL COMMENT 'Blast ID',
  `newsletter_blast_schedule_time` varchar(255) DEFAULT NULL COMMENT 'Blast Schedule time',
  PRIMARY KEY (`newsletter_id`)
) ENGINE=MyISAM AUTO_INCREMENT=389 DEFAULT CHARSET=utf8 COMMENT='Newsletters' 