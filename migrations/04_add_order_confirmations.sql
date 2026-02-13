-- Migration to add confirmation columns for manual order completion
ALTER TABLE `orders` 
ADD COLUMN `buyer_confirmed` TINYINT(1) NOT NULL DEFAULT 0 AFTER `delivery_date`,
ADD COLUMN `seller_confirmed` TINYINT(1) NOT NULL DEFAULT 0 AFTER `buyer_confirmed`;
