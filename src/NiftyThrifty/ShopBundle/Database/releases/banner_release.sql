@banner_type.sql;

INSERT INTO banner_type (name) values ('home_upper_right');

INSERT INTO banner
  (description, url, banner_image, banner_type, is_default, rotation_start_time, rotation_end_time)
VALUES
  ('Share banner (default)',
   'https://www.niftythrifty.com/user/invite_friend',
   'images/images/Promotional_Friends_7.jpg',
   'home_upper_right',
   'yes',
   NOW(),
   NOW());

@banner.sql;

