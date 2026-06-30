-- Procedure-like reporting function for PostgreSQL.

CREATE OR REPLACE FUNCTION "GetOrderSummaryByPeriod"(start_date DATE, end_date DATE)
RETURNS TABLE (
    customer_name VARCHAR(150),
    order_count BIGINT,
    product_quantity NUMERIC(12, 3),
    order_total NUMERIC(14, 2)
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF start_date IS NULL OR end_date IS NULL OR start_date > end_date THEN
        RAISE EXCEPTION 'Invalid period: start_date must be less than or equal to end_date';
    END IF;

    RETURN QUERY
    SELECT
        c.customer_name,
        COUNT(order_summary.customer_order_id)::BIGINT AS order_count,
        COALESCE(SUM(order_summary.product_quantity), 0::NUMERIC)::NUMERIC(12, 3) AS product_quantity,
        COALESCE(SUM(order_summary.total_amount), 0::NUMERIC)::NUMERIC(14, 2) AS order_total
    FROM customer AS c
    INNER JOIN (
        SELECT
            co.customer_order_id,
            co.customer_id,
            co.total_amount,
            COALESCE(SUM(oi.quantity), 0::NUMERIC) AS product_quantity
        FROM customer_order AS co
        LEFT JOIN order_item AS oi
            ON oi.customer_order_id = co.customer_order_id
        WHERE co.order_date BETWEEN start_date AND end_date
        GROUP BY co.customer_order_id, co.customer_id, co.total_amount
    ) AS order_summary
        ON order_summary.customer_id = c.customer_id
    GROUP BY c.customer_id, c.customer_name
    ORDER BY 4 DESC, 1 ASC;
END;
$$;

-- Example:
-- SELECT * FROM "GetOrderSummaryByPeriod"('2026-01-01', '2026-12-31');
