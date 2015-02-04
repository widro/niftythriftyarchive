 CREATE TABLE `collection` (
  `collection_id`                   bigint(20)                                  NOT NULL    COMMENT 'Id',
  `collection_code`                 varchar(5)                  DEFAULT NULL                COMMENT 'Collection Letter Code',
  `collection_name`                 varchar(63)                                 NOT NULL    COMMENT 'Name',
  `collection_description`          longtext                                    NOT NULL    COMMENT 'Description',
  `collection_type`                 enum('Women','Men','Home')  DEFAULT 'Women'             COMMENT 'Type',
  `collection_date_start`           datetime                                    NOT NULL    COMMENT 'Start date',
  `collection_date_end`             datetime                                    NOT NULL    COMMENT 'End date',
  `collection_active`               enum('yes','no')                            NOT NULL    COMMENT 'Active ?',
  `collection_visual_home_hero`     varchar(255)                DEFAULT NULL                COMMENT 'Visual Home Large Hero',
  `collection_visual_main_panel`    varchar(255)                DEFAULT NULL                COMMENT 'Visual Home/New Sales Panel',
  `collection_visual_sale_hero`     varchar(255)                DEFAULT NULL                COMMENT 'Visual Sale Page Hero',
  PRIMARY KEY (`collection_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Collections';