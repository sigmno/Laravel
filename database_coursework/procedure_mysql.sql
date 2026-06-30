-- Reporting procedure for MySQL.

DROP PROCEDURE IF EXISTS GetOrderSummaryByPeriod;

DELIMITER $$

CREATE PROCEDURE GetOrderSummaryByPeriod(
    IN start_date DATE,
    IN end_date DATE
)
BEGIN
    IF start_date IS NULL OR end_date IS NULL OR start_date > end_date THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Invalid period: start_date must be less than or equal to end_date';
    END IF;

    SELECT
        c.customer_name AS customer_name,
        COUNT(order_summary.customer_order_id) AS order_count,
        COALESCE(SUM(order_summary.product_quantity), 0) AS product_quantity,
        COALESCE(SUM(order_summary.total_amount), 0) AS order_total
    FROM customer AS c
    INNER JOIN (
        SELECT
            co.customer_order_id,
            co.customer_id,
            co.total_amount,
            COALESCE(SUM(oi.quantity), 0) AS product_quantity
        FROM customer_order AS co
        LEFT JOIN order_item AS oi
            ON oi.customer_order_id = co.customer_order_id
        WHERE co.order_date BETWEEN start_date AND end_date
        GROUP BY co.customer_order_id, co.customer_id, co.total_amount
    ) AS order_summary
        ON order_summary.customer_id = c.customer_id
    GROUP BY c.customer_id, c.customer_name
    ORDER BY order_total DESC, customer_name ASC;
END$$

DELIMITER ;

-- Example:
-- CALL GetOrderSummaryByPeriod('2026-01-01', '2026-12-31');
