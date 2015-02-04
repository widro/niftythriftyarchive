CREATE TABLE `address` (
  `address_id`          bigint(20)      NOT NULL AUTO_INCREMENT,
  `user_id`             bigint(20)      NOT NULL,
  `address_first_name`  varchar(64)     NOT NULL,
  `address_last_name`   varchar(64)     NOT NULL,
  `address_street`      varchar(255)    NOT NULL,
  `address_city`        varchar(64)     NOT NULL,
  `state_id`            bigint(20)      NOT NULL,
  `address_zipcode`     varchar(20)     NOT NULL,
  `address_country`     varchar(255)    NOT NULL,
  PRIMARY KEY (`address_id`),
  INDEX (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Table stores many different addresses for users' 