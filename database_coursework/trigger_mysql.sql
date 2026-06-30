-- Trigger support for recalculating customer_order.total_amount in MySQL.

DROP TRIGGER IF EXISTS trg_fill_order_item_price_insert;
DROP TRIGGER IF EXISTS trg_fill_order_item_price_update;
DROP TRIGGER IF EXISTS trg_calc_order_total;
DROP TRIGGER IF EXISTS trg_calc_order_total_update;

DELIMITER $$

CREATE TRIGGER trg_fill_order_item_price_insert
BEFORE INSERT ON order_item
FOR EACH ROW
BEGIN
    DECLARE latest_price DECIMAL(12, 2);

    IF NEW.price_per_unit IS NULL THEN
        SET latest_price = (
            SELECT pp.price
            FROM product_price AS pp
            WHERE pp.product_id = NEW.product_id
            ORDER BY pp.price_date DESC, pp.product_price_id DESC
            LIMIT 1
        );

        IF latest_price IS NULL THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'No product price found for order item';
        END IF;

        SET NEW.price_per_unit = latest_price;
    END IF;
END$$

CREATE TRIGGER trg_fill_order_item_price_update
BEFORE UPDATE ON order_item
FOR EACH ROW
BEGIN
    DECLARE latest_price DECIMAL(12, 2);

    IF NEW.price_per_unit IS NULL THEN
        SET latest_price = (
            SELECT pp.price
            FROM product_price AS pp
            WHERE pp.product_id = NEW.product_id
            ORDER BY pp.price_date DESC, pp.product_price_id DESC
            LIMIT 1
        );

        IF latest_price IS NULL THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'No product price found for order item';
        END IF;

        SET NEW.price_per_unit = latest_price;
    END IF;
END$$

CREATE TRIGGER trg_calc_order_total
AFTER INSERT ON order_item
FOR EACH ROW
BEGIN
    UPDATE customer_order AS co
    SET
        total_amount = (
            SELECT COALESCE(SUM(oi.quantity * COALESCE(oi.price_per_unit, (
                SELECT pp.price
                FROM product_price AS pp
                WHERE pp.product_id = oi.product_id
                ORDER BY pp.price_date DESC, pp.product_price_id DESC
                LIMIT 1
            ), 0)), 0)
            FROM order_item AS oi
            WHERE oi.customer_order_id = NEW.customer_order_id
        ),
        updated_at = CURRENT_TIMESTAMP
    WHERE co.customer_order_id = NEW.customer_order_id;
END$$

CREATE TRIGGER trg_calc_order_total_update
AFTER UPDATE ON order_item
FOR EACH ROW
BEGIN
    UPDATE customer_order AS co
    SET
        total_amount = (
            SELECT COALESCE(SUM(oi.quantity * COALESCE(oi.price_per_unit, (
                SELECT pp.price
                FROM product_price AS pp
                WHERE pp.product_id = oi.product_id
                ORDER BY pp.price_date DESC, pp.product_price_id DESC
                LIMIT 1
            ), 0)), 0)
            FROM order_item AS oi
            WHERE oi.customer_order_id = NEW.customer_order_id
        ),
        updated_at = CURRENT_TIMESTAMP
    WHERE co.customer_order_id = NEW.customer_order_id;

    IF OLD.customer_order_id <> NEW.customer_order_id THEN
        UPDATE customer_order AS co
        SET
            total_amount = (
                SELECT COALESCE(SUM(oi.quantity * COALESCE(oi.price_per_unit, (
                    SELECT pp.price
                    FROM product_price AS pp
                    WHERE pp.product_id = oi.product_id
                    ORDER BY pp.price_date DESC, pp.product_price_id DESC
                    LIMIT 1
                ), 0)), 0)
                FROM order_item AS oi
                WHERE oi.customer_order_id = OLD.customer_order_id
            ),
            updated_at = CURRENT_TIMESTAMP
        WHERE co.customer_order_id = OLD.customer_order_id;
    END IF;
END$$

DELIMITER ;
