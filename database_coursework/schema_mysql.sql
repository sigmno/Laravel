-- ManufacturingDB schema for MySQL

CREATE DATABASE IF NOT EXISTS ManufacturingDB
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ManufacturingDB;

CREATE TABLE IF NOT EXISTS product (
    product_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    sku VARCHAR(40) NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    category VARCHAR(80) NOT NULL,
    unit VARCHAR(20) NOT NULL DEFAULT 'pcs',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (product_id),
    CONSTRAINT uq_product_sku UNIQUE (sku)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS material (
    material_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    material_name VARCHAR(150) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    stock_quantity DECIMAL(14, 3) NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (material_id),
    CONSTRAINT chk_material_stock_quantity CHECK (stock_quantity >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bill_of_materials (
    bill_of_materials_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    material_id BIGINT UNSIGNED NOT NULL,
    quantity_required DECIMAL(12, 3) NOT NULL,
    scrap_percent DECIMAL(5, 2) NOT NULL DEFAULT 0,
    PRIMARY KEY (bill_of_materials_id),
    CONSTRAINT chk_bill_of_materials_quantity CHECK (quantity_required > 0),
    CONSTRAINT chk_bill_of_materials_scrap CHECK (scrap_percent >= 0),
    CONSTRAINT uq_bill_of_materials_product_material UNIQUE (product_id, material_id),
    CONSTRAINT `FK_bill_of_materials_product` FOREIGN KEY (product_id)
        REFERENCES product (product_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `FK_bill_of_materials_material` FOREIGN KEY (material_id)
        REFERENCES material (material_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS customer (
    customer_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    customer_name VARCHAR(150) NOT NULL,
    email VARCHAR(180) NOT NULL,
    phone VARCHAR(40),
    city VARCHAR(100) NOT NULL,
    address VARCHAR(250),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (customer_id),
    CONSTRAINT uq_customer_email UNIQUE (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS customer_order (
    customer_order_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    customer_id BIGINT UNSIGNED NOT NULL,
    order_date DATE NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'new',
    total_amount DECIMAL(14, 2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (customer_order_id),
    CONSTRAINT chk_customer_order_status CHECK (status IN ('new', 'in_production', 'shipped', 'closed', 'cancelled')),
    CONSTRAINT chk_customer_order_total CHECK (total_amount >= 0),
    CONSTRAINT `FK_customer_order_customer` FOREIGN KEY (customer_id)
        REFERENCES customer (customer_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product_price (
    product_price_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    price_date DATE NOT NULL,
    PRIMARY KEY (product_price_id),
    CONSTRAINT chk_product_price_price CHECK (price >= 0),
    CONSTRAINT uq_product_price_product_date UNIQUE (product_id, price_date),
    CONSTRAINT `FK_product_price_product` FOREIGN KEY (product_id)
        REFERENCES product (product_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS material_price (
    material_price_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    material_id BIGINT UNSIGNED NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    price_date DATE NOT NULL,
    PRIMARY KEY (material_price_id),
    CONSTRAINT chk_material_price_price CHECK (price >= 0),
    CONSTRAINT uq_material_price_material_date UNIQUE (material_id, price_date),
    CONSTRAINT `FK_material_price_material` FOREIGN KEY (material_id)
        REFERENCES material (material_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_item (
    order_item_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    customer_order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity DECIMAL(12, 3) NOT NULL,
    price_per_unit DECIMAL(12, 2),
    PRIMARY KEY (order_item_id),
    CONSTRAINT chk_order_item_quantity CHECK (quantity > 0),
    CONSTRAINT chk_order_item_price CHECK (price_per_unit IS NULL OR price_per_unit >= 0),
    CONSTRAINT `FK_order_item_customer_order` FOREIGN KEY (customer_order_id)
        REFERENCES customer_order (customer_order_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `FK_order_item_product` FOREIGN KEY (product_id)
        REFERENCES product (product_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_customer_order_date ON customer_order (order_date);
CREATE INDEX idx_order_item_order ON order_item (customer_order_id);
CREATE INDEX idx_order_item_product ON order_item (product_id);
CREATE INDEX idx_product_price_product_date ON product_price (product_id, price_date);
CREATE INDEX idx_material_price_material_date ON material_price (material_id, price_date);
