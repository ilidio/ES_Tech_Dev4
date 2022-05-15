DELIMITER $$

DROP PROCEDURE IF EXISTS `Select_Lowest_Price`;

CREATE PROCEDURE Select_Lowest_Price(IN account_id_val INT, IN product_id_val INT)
BEGIN
DECLARE sku CHAR(255) DEFAULT null;
DECLARE public_sku CHAR(255) DEFAULT null;
DECLARE result FLOAT DEFAULT null;
DECLARE price FLOAT DEFAULT null;
DECLARE public_price INT DEFAULT null;

SELECT p.sku, MIN(value) as value
FROM prices
    INNER JOIN products p on prices.product_id = p.id
WHERE account_id=account_id_val and product_id=product_id_val and prices.deleted_at IS null
GROUP by p.sku
ORDER BY value desc into sku, price;

IF account_id_val IS NOT NULL and price IS NOT NULL THEN
SELECT sku, price;
ELSE
SELECT p.sku, MIN(value) as value
FROM prices
    INNER JOIN products p on prices.product_id = p.id
WHERE product_id=product_id_val and account_id IS NULL and prices.deleted_at IS null
GROUP by p.sku
ORDER BY value desc into sku, price;
SELECT sku, price;
END IF;

END $$

DELIMITER ;

