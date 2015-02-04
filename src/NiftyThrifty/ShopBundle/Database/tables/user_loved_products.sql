CREATE TABLE user_loved_product (
    user_id     bigint(20)              NOT NULL,
    product_id  bigint(20)              NOT NULL,
    love_type   enum('link','basket')   NOT NULL,
    date_loved  datetime                NOT NULL,
    is_deleted  tinyint(1)              NOT NULL DEFAULT 0,
    PRIMARY KEY(user_id, product_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Items that the user has either loved or added to a basket.';

ALTER TABLE user_loved_product ADD INDEX user_loved_products_ix0 (user_id, date_loved);