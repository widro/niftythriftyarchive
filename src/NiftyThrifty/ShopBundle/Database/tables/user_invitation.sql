CREATE TABLE `user_invitation` (
  `user_invitation_id`          bigint(20)      NOT NULL,
  `user_invitation_last_name`   varchar(255)    NOT NULL,
  `user_invitation_first_name`  varchar(255)    NOT NULL,
  `user_invitation_status`      varchar(255)    NOT NULL,
  `user_invitation_date`        date            NOT NULL,
  `user_invitation_type`        varchar(255)    NOT NULL,
  `user_invitation_content`     longtext        NOT NULL,
  `user_invitation_email`       varchar(255)    NOT NULL,
  `user_invitation_fb_id`       varchar(255)    NOT NULL,
  `user_invitation_twitter_id`  varchar(255)    NOT NULL,
  `user_invitation_user_id`     bigint(20)      NOT NULL,
  `user_id`                     bigint(20)      NOT NULL,
  PRIMARY KEY (`user_invitation_id`),
  INDEX (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='User invitations' ;