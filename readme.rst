# Inventory API - Flash Sale Challenge

## Overview

Inventory API adalah REST API sederhana yang dibangun menggunakan CodeIgniter 3 untuk mensimulasikan proses pembelian produk saat flash sale.

Fitur utama:

* Melihat daftar produk
* Membuat pesanan (order)
* Mengurangi stok secara otomatis
* Mencegah overselling menggunakan database transaction dan row locking (`SELECT ... FOR UPDATE`)
* Simulasi concurrent requests menggunakan PHP cURL Multi

---

## Tech Stack

* PHP 7.4+
* CodeIgniter 3
* MySQL / MariaDB
* InnoDB Engine

---

## Installation

### 1. Clone Repository

```bash
git clone https://github.com/username/inventory-api.git
cd inventory-api
```

### 2. Create Database

Masuk ke MySQL:

```sql
CREATE DATABASE db_inventory;
```

Import schema:

```bash
mysql -u root -p db_inventory < schema.sql
```

### 3. Configure Database

Edit file:

```text
application/config/database.php
```

Contoh:

```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'db_inventory',
    'dbdriver' => 'mysqli',
);
```

### 4. Run Application

Menggunakan Laragon atau Apache:

```text
http://localhost:8080/inventory-api
```

---

## Database Structure

### products

| Column     | Type      |
| ---------- | --------- |
| id         | INT       |
| name       | VARCHAR   |
| price      | DECIMAL   |
| inventory  | INT       |
| created_at | TIMESTAMP |

### orders

| Column       | Type      |
| ------------ | --------- |
| id           | INT       |
| total_amount | DECIMAL   |
| created_at   | TIMESTAMP |

### order_items

| Column     | Type      |
| ---------- | --------- |
| id         | INT       |
| order_id   | INT       |
| product_id | INT       |
| qty        | INT       |
| price      | DECIMAL   |
| created_at | TIMESTAMP |

---

## API Endpoints

### Get Products

Request:

```http
GET /api/products
```

Response:

```json
{
  "success": true,
  "data": [
    {
      "id": "1",
      "name": "Flash Sale Product",
      "price": "100000.00",
      "inventory": "10"
    }
  ]
}
```

---

### Create Order

Request:

```http
POST /api/orders
Content-Type: application/json
```

Body:

```json
{
  "product_id": 1,
  "qty": 1
}
```

Success Response:

```json
{
  "success": true,
  "message": "Order created",
  "order_id": 1
}
```

Failed Response:

```json
{
  "success": false,
  "message": "Insufficient inventory"
}
```

---

## Testing

### Reset Inventory

```sql
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE order_items;
TRUNCATE TABLE orders;

SET FOREIGN_KEY_CHECKS = 1;

UPDATE products
SET inventory = 10
WHERE id = 1;
```

---

### Run Flash Sale Test

```bash
php tests/FlashSaleTest.php
```

Expected Result:

```text
SUCCESS : 10
FAILED  : 90
```

Karena stok produk hanya tersedia 10 unit.

---

## Concurrency Handling

Untuk mencegah overselling saat flash sale, aplikasi menggunakan:

* Database Transaction
* SELECT ... FOR UPDATE
* InnoDB Row Locking

Contoh:

```sql
SELECT *
FROM products
WHERE id = ?
FOR UPDATE;
```

Dengan pendekatan ini, hanya satu transaksi yang dapat mengubah stok produk pada satu waktu.

---

## Project Structure

```text
application/
├── controllers/
│   └── api/
│       ├── Products.php
│       └── Orders.php
├── models/
│   ├── Product_model.php
│   └── Order_model.php
├── core/
│   └── MY_Controller.php

tests/
└── FlashSaleTest.php

schema.sql
README.md
```

---

## Author

Developed as a Flash Sale Inventory API Challenge using CodeIgniter 3 and MySQL.
