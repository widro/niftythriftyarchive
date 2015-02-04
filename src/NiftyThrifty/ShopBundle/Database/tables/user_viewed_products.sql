CREATE TABLE user_viewed_product (
    user_id     bigint(20)  NOT NULL,
    product_id  bigint(20)  NOT NULL,
    date_viewed datetime    NOT NULL,
    PRIMARY KEY(user_id, product_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Items the user has viewed.';

ALTER TABLE user_viewed_product ADD INDEX user_viewed_product_ix0 (user_id, date_viewed);
