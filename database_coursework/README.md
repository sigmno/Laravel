# ManufacturingDB

Отдельное задание по базам данных: производственно-сбытовая система `ManufacturingDB`.

## Состав БД

База приведена к 3НФ: справочники продукции, материалов и клиентов отделены от заказов, состава изделий и истории цен.

- `product` - выпускаемая продукция.
- `material` - материалы для производства.
- `bill_of_materials` - спецификация материалов для продукции.
- `customer` - клиенты.
- `customer_order` - заказы клиентов.
- `order_item` - позиции заказа.
- `product_price` - история цен продукции.
- `material_price` - история цен материалов.

Связи оформлены внешними ключами с именами вида `FK_table_related_table`. Схема использует `snake_case`.

## ER-диаграмма

ER-диаграмма находится в файле `er_diagram.md` и описана через Mermaid.

## Создание и заполнение

PostgreSQL:

```bash
createdb ManufacturingDB
psql -d ManufacturingDB -f schema_postgresql.sql
psql -d ManufacturingDB -f trigger_postgresql.sql
psql -d ManufacturingDB -f seed_data.sql
psql -d ManufacturingDB -f procedure_postgresql.sql
```

MySQL:

```bat
mysql -u root -p < schema_mysql.sql
mysql -u root -p ManufacturingDB < trigger_mysql.sql
mysql -u root -p ManufacturingDB < seed_data.sql
mysql -u root -p ManufacturingDB < procedure_mysql.sql
```

## Резервное копирование

PostgreSQL:

```bash
./backup_postgresql.sh
```

Будет создан файл `ManufacturingDB_backup_YYYYMMDD.sql`.

MySQL:

```bat
backup_mysql.bat
```

Можно задать пользователя через переменную `MYSQL_USER`, по умолчанию используется `root`.

## Восстановление

PostgreSQL:

```bash
./restore_postgresql.sh ManufacturingDB_backup_YYYYMMDD.sql
```

MySQL:

```bat
restore_mysql.bat ManufacturingDB_backup_YYYYMMDD.sql
```

Скрипты удаляют базу `ManufacturingDB`, создают её заново и восстанавливают данные из указанного файла.

## Процедура

`GetOrderSummaryByPeriod(start_date, end_date)` возвращает:

- имя клиента;
- количество заказов;
- количество продукции;
- сумму заказов.

PostgreSQL:

```sql
SELECT * FROM "GetOrderSummaryByPeriod"('2026-01-01', '2026-12-31');
```

MySQL:

```sql
CALL GetOrderSummaryByPeriod('2026-01-01', '2026-12-31');
```

## Триггер

`trg_calc_order_total` пересчитывает сумму заказа после добавления или изменения позиции заказа:

```sql
SUM(quantity * price_per_unit)
```

Если `price_per_unit` не указан, дополнительный триггер перед записью подставляет последнюю цену из `product_price`.

В MySQL обновление реализовано двумя триггерами, потому что MySQL создаёт отдельный триггер на каждое событие: `trg_calc_order_total` для `INSERT` и `trg_calc_order_total_update` для `UPDATE`.

## Python-модуль

Модуль `analysis_module.py` использует `SQLAlchemy`, читает настройки из `db_config.json` и поддерживает SQLite, MySQL и PostgreSQL.

Установка зависимостей:

```bash
pip install sqlalchemy pymysql psycopg2-binary
```

Примеры запуска:

```bash
python analysis_module.py --config db_config.json
python analysis_module.py --sort total_amount --desc
python analysis_module.py --client "Alpha" --search "closed"
```

Модуль умеет сортировать заказы, фильтровать по клиенту, искать с подсветкой `[MATCH]...[/MATCH]` и выводить статистику. Ошибки соединения, SQL, пустые данные и неверный ввод обрабатываются без падения программы.
