CREATE TABLE `user` (
  `user_id`                     bigint(20)              NOT NULL                   COMMENT 'Id',
  `user_first_name`             varchar(100)            NOT NULL                   COMMENT 'First name',
  `user_last_name`              varchar(100)            NOT NULL                   COMMENT 'Last name',
  `user_email`                  varchar(100)            NOT NULL                   COMMENT 'Email',
  `user_password`               varchar(255)            NOT NULL                   COMMENT 'Password',
  `user_date_creation`          date                    NOT NULL                   COMMENT 'Creation date',
  `user_date_last_connection`   datetime                NOT NULL                   COMMENT 'Last connection date',
  `user_instagram_id`           varchar(50)                         DEFAULT NULL   COMMENT 'Instagram Id',
  `user_instagram_access_token` varchar(255)                        DEFAULT NULL   COMMENT 'Instagram access token',
  `user_fb_id`                  varchar(255)                        DEFAULT NULL   COMMENT 'Fb Id',
  `user_active`                 enum('true','false')    NOT NULL    DEFAULT 'false' COMMENT 'Active ?',
  `address_id_shipping`         bigint(20)                          DEFAULT NULL   COMMENT 'Address Shipping',
  `address_id_billing`          bigint(20)                          DEFAULT NULL   COMMENT 'Address Billing',
  `user_admin`                  enum('true','false')    NOT NULL                   COMMENT 'Is admin ?',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Users';
