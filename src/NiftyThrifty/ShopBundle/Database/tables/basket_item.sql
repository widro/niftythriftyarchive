 CREATE TABLE `basket_item` (
  `basket_item_id`          bigint(20)      NOT NULL,
  `basket_id`               bigint(20)      NOT NULL,
  `product_id`              bigint(20)      NOT NULL,
  `basket_item_date_add`    datetime        NOT NULL,
  `basket_item_date_end`    datetime        NOT NULL,
  `basket_item_price`       int(11)         NOT NULL,
  `basket_item_discount`    int(11)         NOT NULL,
  `basket_item_status`      varchar(255)    NOT NULL,
  PRIMARY KEY (`basket_item_id`),
  INDEX (`basket_id`),
  INDEX (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Basket Items';

alter table basket_item add index (basket_item_date_end, basket_item_status, product_id);  
alter table basket_item add index (basket_item_date_end, basket_item_status);  
