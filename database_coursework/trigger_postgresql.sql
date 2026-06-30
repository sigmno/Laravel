-- Trigger support for recalculating customer_order.total_amount in PostgreSQL.

CREATE OR REPLACE FUNCTION fill_order_item_price()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    latest_price NUMERIC(12, 2);
BEGIN
    IF NEW.price_per_unit IS NULL THEN
        SELECT pp.price
        INTO latest_price
        FROM product_price AS pp
        WHERE pp.product_id = NEW.product_id
        ORDER BY pp.price_date DESC, pp.product_price_id DESC
        LIMIT 1;

        IF latest_price IS NULL THEN
            RAISE EXCEPTION 'No product price found for product_id %', NEW.product_id;
        END IF;

        NEW.price_per_unit := latest_price;
    END IF;

    RETURN NEW;
END;
$$;

CREATE OR REPLACE FUNCTION recalculate_order_total(target_order_id BIGINT)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN
    UPDATE customer_order AS co
    SET
        total_amount = (
            SELECT COALESCE(SUM(oi.quantity * COALESCE(oi.price_per_unit, latest_price.price, 0)), 0)
            FROM order_item AS oi
            LEFT JOIN LATERAL (
                SELECT pp.price
                FROM product_price AS pp
                WHERE pp.product_id = oi.product_id
                ORDER BY pp.price_date DESC, pp.product_price_id DESC
                LIMIT 1
            ) AS latest_price ON TRUE
            WHERE oi.customer_order_id = target_order_id
        ),
        updated_at = CURRENT_TIMESTAMP
    WHERE co.customer_order_id = target_order_id;
END;
$$;

CREATE OR REPLACE FUNCTION calc_order_total_after_item_change()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    PERFORM recalculate_order_total(NEW.customer_order_id);

    IF TG_OP = 'UPDATE' THEN
        IF OLD.customer_order_id <> NEW.customer_order_id THEN
            PERFORM recalculate_order_total(OLD.customer_order_id);
        END IF;
    END IF;

    RETURN NULL;
END;
$$;

DROP TRIGGER IF EXISTS trg_fill_order_item_price ON order_item;
CREATE TRIGGER trg_fill_order_item_price
BEFORE INSERT OR UPDATE OF product_id, price_per_unit
ON order_item
FOR EACH ROW
EXECUTE FUNCTION fill_order_item_price();

DROP TRIGGER IF EXISTS trg_calc_order_total ON order_item;
CREATE TRIGGER trg_calc_order_total
AFTER INSERT OR UPDATE
ON order_item
FOR EACH ROW
EXECUTE FUNCTION calc_order_total_after_item_change();
