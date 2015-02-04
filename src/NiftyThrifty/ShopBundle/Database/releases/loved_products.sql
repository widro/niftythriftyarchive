-- New tables
@user_loved_product.sql
@user_viewed_product.sql

-- Add former expired basket items to the loves table
INSERT INTO user_loved_product 
  (user_id, product_id, love_type, date_loved, is_deleted)
SELECT DISTINCT b.user_id, bi.product_id, 'basket', NOW(), 0
  FROM basket_item bi, basket b
 WHERE bi.basket_id = b.basket_id;
 
UPDATE user_loved_product
   SET is_deleted = 1
 WHERE product_id IN (SELECT product_id FROM product WHERE product_availability = 'sold');
 
-- Remove the old expired and deleted stuff.
DELETE FROM basket_item WHERE basket_item_status in ('expired','deleted');

-- And change the valid enums.
ALTER TABLE basket_item MODIFY basket_item_status enum('valid','payment','deleted','expired','returned') NOT NULL DEFAULT 'valid';
ALTER TABLE product     MODIFY product_availability enum('sale','sold','reserved','etsy','ebay') NOT NULL COMMENT  'Availability';
