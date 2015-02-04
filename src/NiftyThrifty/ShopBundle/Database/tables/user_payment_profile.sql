CREATE TABLE user_payment_profile (
    user_payment_profile_id     BIGINT(20)      NOT NULL AUTO_INCREMENT COMMENT 'internal pk id for payment profiles',
    user_id                     BIGINT(20)      NOT NULL                COMMENT 'user id',
    card_digits                 VARCHAR(100)    NOT NULL                COMMENT 'last 4 digits of the users card',
    expiration_date             VARCHAR(100)    NOT NULL                COMMENT 'card expiration date',
    authorize_net_profile_id    BIGINT(20)      NOT NULL                COMMENT 'authorize.net payment id',
    PRIMARY KEY (user_payment_profile_id),
    INDEX (user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Payment profiles';