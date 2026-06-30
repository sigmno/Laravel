from __future__ import annotations

import argparse
import json
import re
import sys
from collections import Counter, defaultdict
from datetime import date, datetime
from decimal import Decimal, InvalidOperation
from pathlib import Path
from typing import Any

try:
    from sqlalchemy import create_engine, text
    from sqlalchemy.engine import URL
    from sqlalchemy.exc import SQLAlchemyError
except ImportError as exc:
    create_engine = None
    text = None
    URL = None
    SQLALCHEMY_IMPORT_ERROR = exc

    class SQLAlchemyError(Exception):
        pass
else:
    SQLALCHEMY_IMPORT_ERROR = None


SUPPORTED_SORT_FIELDS = {
    "order_id": "order_id",
    "customer_name": "customer_name",
    "order_date": "order_date",
    "status": "status",
    "product_quantity": "product_quantity",
    "total_amount": "calculated_total",
}


def resolve_config_path(raw_path: str) -> Path:
    path = Path(raw_path).expanduser()
    if path.exists():
        return path

    script_relative = Path(__file__).resolve().parent / path
    if script_relative.exists():
        return script_relative

    return path


def load_config(path: Path) -> dict[str, Any] | None:
    try:
        with path.open("r", encoding="utf-8") as file:
            return json.load(file)
    except FileNotFoundError:
        print(f"Config file not found: {path}")
    except json.JSONDecodeError as exc:
        print(f"Config JSON error in {path}: {exc}")
    except OSError as exc:
        print(f"Cannot read config file {path}: {exc}")

    return None


def build_connection_url(config: dict[str, Any], config_dir: Path) -> URL | str | None:
    if SQLALCHEMY_IMPORT_ERROR is not None:
        print("SQLAlchemy is not installed. Run: pip install sqlalchemy pymysql psycopg2-binary")
        return None

    driver = str(config.get("driver", "sqlite")).lower()
    driver_config = config.get(driver, {})

    if not isinstance(driver_config, dict):
        print(f"Invalid config section for driver: {driver}")
        return None

    if driver_config.get("url"):
        return str(driver_config["url"])

    if driver == "sqlite":
        database = str(driver_config.get("database", "ManufacturingDB.sqlite"))
        database_path = Path(database).expanduser()
        if not database_path.is_absolute():
            database_path = config_dir / database_path

        return URL.create("sqlite", database=str(database_path))

    if driver in {"mysql", "postgresql"}:
        driver_name = "mysql+pymysql" if driver == "mysql" else "postgresql+psycopg2"
        return URL.create(
            driver_name,
            username=driver_config.get("username") or driver_config.get("user"),
            password=driver_config.get("password"),
            host=driver_config.get("host", "127.0.0.1"),
            port=driver_config.get("port"),
            database=driver_config.get("database", "ManufacturingDB"),
        )

    print(f"Unsupported driver: {driver}. Use sqlite, mysql, or postgresql.")
    return None


def create_checked_engine(connection_url: URL | str):
    try:
        engine = create_engine(connection_url, future=True)
        with engine.connect() as connection:
            connection.execute(text("SELECT 1"))
        return engine
    except SQLAlchemyError as exc:
        print(f"Database connection error: {exc}")
    except Exception as exc:
        print(f"Unexpected connection error: {exc}")

    return None


def product_aggregate_sql(dialect_name: str) -> str:
    if dialect_name == "postgresql":
        return "COALESCE(STRING_AGG(DISTINCT p.product_name, ', '), '') AS products"

    if dialect_name == "mysql":
        return "COALESCE(GROUP_CONCAT(DISTINCT p.product_name ORDER BY p.product_name SEPARATOR ', '), '') AS products"

    return "COALESCE(GROUP_CONCAT(DISTINCT p.product_name), '') AS products"


def build_orders_query(dialect_name: str):
    aggregate_products = product_aggregate_sql(dialect_name)

    return text(
        f"""
        SELECT
            co.customer_order_id AS order_id,
            c.customer_name AS customer_name,
            co.order_date AS order_date,
            co.status AS status,
            COALESCE(SUM(oi.quantity), 0) AS product_quantity,
            COALESCE(SUM(oi.quantity * COALESCE(oi.price_per_unit, latest_price.latest_price, 0)), 0) AS calculated_total,
            co.total_amount AS stored_total,
            {aggregate_products}
        FROM customer_order AS co
        INNER JOIN customer AS c
            ON c.customer_id = co.customer_id
        LEFT JOIN order_item AS oi
            ON oi.customer_order_id = co.customer_order_id
        LEFT JOIN product AS p
            ON p.product_id = oi.product_id
        LEFT JOIN (
            SELECT product_id, price AS latest_price
            FROM (
                SELECT
                    product_id,
                    price,
                    product_price_id,
                    ROW_NUMBER() OVER (
                        PARTITION BY product_id
                        ORDER BY price_date DESC, product_price_id DESC
                    ) AS rn
                FROM product_price
            ) AS ranked_product_price
            WHERE rn = 1
        ) AS latest_price
            ON latest_price.product_id = oi.product_id
        GROUP BY
            co.customer_order_id,
            c.customer_name,
            co.order_date,
            co.status,
            co.total_amount
        """
    )


def fetch_orders(engine) -> list[dict[str, Any]]:
    try:
        with engine.connect() as connection:
            result = connection.execute(build_orders_query(engine.dialect.name))
            return [dict(row) for row in result.mappings().all()]
    except SQLAlchemyError as exc:
        print(f"SQL execution error: {exc}")
    except Exception as exc:
        print(f"Unexpected SQL error: {exc}")

    return []


def contains(value: Any, query: str) -> bool:
    return query.lower() in str(value or "").lower()


def filter_orders(
    orders: list[dict[str, Any]],
    client_filter: str | None = None,
    search_query: str | None = None,
) -> list[dict[str, Any]]:
    filtered = orders

    if client_filter:
        filtered = [row for row in filtered if contains(row.get("customer_name"), client_filter)]

    if search_query:
        filtered = [
            row
            for row in filtered
            if contains(row.get("order_id"), search_query)
            or contains(row.get("customer_name"), search_query)
            or contains(row.get("status"), search_query)
            or contains(row.get("products"), search_query)
        ]

    return filtered


def normalized_sort_value(value: Any) -> Any:
    if value is None:
        return ""

    if isinstance(value, Decimal):
        return float(value)

    if isinstance(value, (int, float)):
        return value

    if isinstance(value, (date, datetime)):
        return value.isoformat()

    return str(value).lower()


def sort_orders(orders: list[dict[str, Any]], sort_by: str, descending: bool) -> list[dict[str, Any]]:
    sort_field = SUPPORTED_SORT_FIELDS.get(sort_by)
    if sort_field is None:
        print(f"Invalid sort field '{sort_by}'. Using 'order_date'.")
        sort_field = SUPPORTED_SORT_FIELDS["order_date"]

    return sorted(
        orders,
        key=lambda row: normalized_sort_value(row.get(sort_field)),
        reverse=descending,
    )


def highlight_match(value: Any, query: str | None) -> str:
    text_value = str(value or "")
    if not query:
        return text_value

    pattern = re.compile(re.escape(query), re.IGNORECASE)
    return pattern.sub(lambda match: f"[MATCH]{match.group(0)}[/MATCH]", text_value)


def to_decimal(value: Any) -> Decimal:
    try:
        return Decimal(str(value or 0))
    except (InvalidOperation, ValueError):
        return Decimal("0")


def print_orders(orders: list[dict[str, Any]], search_query: str | None) -> None:
    if not orders:
        print("No order data found for the selected filters.")
        return

    print("Orders")
    print("-" * 110)
    print(f"{'ID':>4} | {'Date':<10} | {'Customer':<28} | {'Status':<14} | {'Qty':>10} | {'Total':>12} | Products")
    print("-" * 110)

    for row in orders:
        order_id = highlight_match(row.get("order_id"), search_query)
        customer_name = highlight_match(row.get("customer_name"), search_query)
        status = highlight_match(row.get("status"), search_query)
        products = highlight_match(row.get("products"), search_query)
        quantity = to_decimal(row.get("product_quantity"))
        total = to_decimal(row.get("calculated_total"))

        print(
            f"{order_id:>4} | "
            f"{str(row.get('order_date')):<10} | "
            f"{customer_name[:28]:<28} | "
            f"{status[:14]:<14} | "
            f"{quantity:>10.3f} | "
            f"{total:>12.2f} | "
            f"{products}"
        )


def print_statistics(orders: list[dict[str, Any]]) -> None:
    if not orders:
        print("Statistics are unavailable because there is no data.")
        return

    total_quantity = sum((to_decimal(row.get("product_quantity")) for row in orders), Decimal("0"))
    total_amount = sum((to_decimal(row.get("calculated_total")) for row in orders), Decimal("0"))
    average_amount = total_amount / Decimal(len(orders))

    status_counter = Counter(str(row.get("status") or "unknown") for row in orders)
    customer_totals: dict[str, Decimal] = defaultdict(lambda: Decimal("0"))

    for row in orders:
        customer_totals[str(row.get("customer_name") or "unknown")] += to_decimal(row.get("calculated_total"))

    top_customer, top_customer_total = max(customer_totals.items(), key=lambda item: item[1])

    print("")
    print("Statistics")
    print("-" * 110)
    print(f"Orders count: {len(orders)}")
    print(f"Total quantity: {total_quantity:.3f}")
    print(f"Total amount: {total_amount:.2f}")
    print(f"Average order amount: {average_amount:.2f}")
    print(f"Top customer: {top_customer} ({top_customer_total:.2f})")
    print("Statuses: " + ", ".join(f"{status}={count}" for status, count in sorted(status_counter.items())))


def parse_args(argv: list[str]) -> argparse.Namespace:
    default_config = Path(__file__).with_name("db_config.json")

    parser = argparse.ArgumentParser(description="ManufacturingDB order analysis")
    parser.add_argument("--config", default=str(default_config), help="Path to db_config.json")
    parser.add_argument(
        "--sort",
        default="order_date",
        help="Sort by: " + ", ".join(SUPPORTED_SORT_FIELDS.keys()),
    )
    parser.add_argument("--desc", action="store_true", help="Sort descending")
    parser.add_argument("--client", default="", help="Filter by customer name")
    parser.add_argument("--search", default="", help="Search in order id, customer, status, and products")

    return parser.parse_args(argv)


def main(argv: list[str] | None = None) -> int:
    args = parse_args(argv or sys.argv[1:])
    config_path = resolve_config_path(args.config)
    config = load_config(config_path)
    if config is None:
        return 1

    connection_url = build_connection_url(config, config_path.parent)
    if connection_url is None:
        return 1

    engine = create_checked_engine(connection_url)
    if engine is None:
        return 1

    orders = fetch_orders(engine)
    if not orders:
        print("The query returned no rows or could not be completed.")
        return 0

    filtered_orders = filter_orders(orders, args.client.strip(), args.search.strip())
    sorted_orders = sort_orders(filtered_orders, args.sort.strip(), args.desc)

    print_orders(sorted_orders, args.search.strip())
    print_statistics(sorted_orders)

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
