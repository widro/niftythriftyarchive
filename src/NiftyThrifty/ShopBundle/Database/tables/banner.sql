CREATE TABLE banner (
    banner_id           bigint(20)              NOT NULL    AUTO_INCREMENT,
    description         varchar(50)             NOT NULL,
    url                 varchar(255)                        DEFAULT NULL,
    banner_image        varchar(255)            NOT NULL,
    banner_type         varchar(50)             NOT NULL,
    is_default          enum('yes','no')        NOT NULL    DEFAULT 'yes',
    rotation_start_time datetime                NOT NULL,
    rotation_end_time   datetime                NOT NULL,

    PRIMARY KEY (banner_id),
    CONSTRAINT FOREIGN KEY (banner_type) REFERENCES banner_type(name),
    INDEX (rotation_start_time, rotation_end_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Configurable on-site banners.';
