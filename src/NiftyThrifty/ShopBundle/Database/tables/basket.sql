CREATE TABLE `basket` (
  `basket_id`               bigint(20)      NOT NULL,
  `basket_date_creation`    datetime        NOT NULL,
  `basket_date_update`      datetime        NOT NULL,
  `basket_status`           varchar(255)    NOT NULL,
  `user_id`                 bigint(20)      NOT NULL,
  PRIMARY KEY (`basket_id`),
  INDEX (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='The basket of a user';