db changelong

#rename order to user_order
rename table nifty_www.order to nifty_www.user_order;

#Get rid of addresses where state id = 0
update user set address_id_billing = null where address_id_billing in (select address_id from address where state_id = 0);
update user set address_id_shipping = null where address_id_shipping in (select address_id from address where state_id = 0);
delete from address where state_id = 0;

# Get rid of states that are not actual states
update address set state_id = 32 where state_id > 51;
delete from state where state_id > 51;

// States
UPDATE address SET state_id = 32 WHERE state_id > 51;
DELETE FROM state WHERE state_id > 51;

ALTER TABLE user_invitation ADD PRIMARY KEY (`user_invitation_id`); 
ALTER TABLE user ADD authorize_net_customer_id bigint(20) null;

DROP TABLE contact_email;
DROP TABLE faq;
DROP TABLE free_shipping;
DROP TABLE lookbook;
DROP TABLE message;
DROP TABLE newsletter_sunday;
DROP TABLE nifty_feed;
DROP TABLE page;
DROP TABLE press_publication;
DROP TABLE product_fake;
DROP TABLE product_look;
DROP TABLE push;
DROP TABLE push_big;
DROP TABLE shop;
DROP TABLE social;
DROP TABLE user_old;

ALTER TABLE collection DROP COLUMN collection_archetype;

# All these fields are currently set to NOT NULL DEFAULT NULL.  Only one of these things can be true.
alter table user_invitation modify column user_invitation_content       longtext    null;
ALTER TABLE user_invitation MODIFY COLUMN user_invitation_first_name    varchar(255) null;
ALTER TABLE user_invitation MODIFY COLUMN user_invitation_last_name     varchar(255) null;
ALTER TABLE user_invitation MODIFY COLUMN user_invitation_email         varchar(255) null;
ALTER TABLE user_invitation MODIFY COLUMN user_invitation_fb_id         varchar(255) null;
ALTER TABLE user_invitation MODIFY COLUMN user_invitation_twitter_id    varchar(255) null;
ALTER TABLE user_invitation MODIFY COLUMN user_invitation_user_id       bigint(20) null;
