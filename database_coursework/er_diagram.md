# ER-диаграмма ManufacturingDB

```mermaid
erDiagram
    product ||--o{ bill_of_materials : uses
    material ||--o{ bill_of_materials : included_in
    product ||--o{ product_price : has
    material ||--o{ material_price : has
    customer ||--o{ customer_order : places
    customer_order ||--o{ order_item : contains
    product ||--o{ order_item : ordered_as

    product {
        bigint product_id PK
        varchar sku UK
        varchar product_name
        varchar category
        varchar unit
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    material {
        bigint material_id PK
        varchar material_name
        varchar unit
        decimal stock_quantity
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    bill_of_materials {
        bigint bill_of_materials_id PK
        bigint product_id FK
        bigint material_id FK
        decimal quantity_required
        decimal scrap_percent
    }

    customer {
        bigint customer_id PK
        varchar customer_name
        varchar email UK
        varchar phone
        varchar city
        varchar address
        timestamp created_at
    }

    customer_order {
        bigint customer_order_id PK
        bigint customer_id FK
        date order_date
        varchar status
        decimal total_amount
        timestamp created_at
        timestamp updated_at
    }

    order_item {
        bigint order_item_id PK
        bigint customer_order_id FK
        bigint product_id FK
        decimal quantity
        decimal price_per_unit
    }

    product_price {
        bigint product_price_id PK
        bigint product_id FK
        decimal price
        date price_date
    }

    material_price {
        bigint material_price_id PK
        bigint material_id FK
        decimal price
        date price_date
    }
```

Связи:

- `product` 1:N `bill_of_materials`
- `material` 1:N `bill_of_materials`
- `product` 1:N `product_price`
- `material` 1:N `material_price`
- `customer` 1:N `customer_order`
- `customer_order` 1:N `order_item`
- `product` 1:N `order_item`
