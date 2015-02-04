insert into user 
  (user_id, user_first_name, user_last_name, user_email, user_password, user_date_creation, user_date_last_connection, user_active, user_admin) 
values 
  (1, 'User', 'Test', 'ut_user', 'ut_userpass', NOW(''), NOW(''), 'true', 'false');
  
insert into user 
  (user_id, user_first_name, user_last_name, user_email, user_password, user_date_creation, user_date_last_connection, user_active, user_admin) 
values 
  (2, 'User', 'Admin', 'ut_admin', 'ut_adminpass', NOW(''), NOW(''), 'true', 'true');
  

