# Restaurant Management System - Frontend API Documentation

**Base URL:** `http://localhost:8000/api`
**Authentication:** Laravel Sanctum Token (`Authorization: Bearer <token>`)
**Total Endpoints:** 151

---

## Table of Contents

1. [Authentication](#1-authentication)
2. [Menu Management](#2-menu-management)
3. [Customers](#3-customers)
4. [Promotions](#4-promotions)
5. [Orders](#5-orders)
6. [Deliveries](#6-deliveries)
7. [Restaurant Management](#7-restaurant-management)
8. [Staff Management](#8-staff-management)
9. [Inventory Management](#9-inventory-management)
10. [Payments](#10-payments)
11. [Roles & Permissions](#11-roles--permissions)

---

## Standard Response Format

All endpoints return JSON with this structure:

```json
{
  "success": true,
  "message": "Operation completed",
  "data": { ... }
}
```

Error response:
```json
{
  "success": false,
  "message": "Error description",
  "errors": { ... }
}
```

---

## 1. Authentication

### 1.1 Register
```
POST /api/register
```
**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "phone": "1234567890"
}
```
**Response (201):**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": { "id": 1, "name": "John Doe", "email": "john@example.com", "roles": [] },
    "token": "1|abc123..."
  }
}
```

### 1.2 Login
```
POST /api/login
```
**Request:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```
**Response (200):**
```json
{
  "success": true,
  "message": "Logged in successfully",
  "data": {
    "user": { "id": 1, "name": "John Doe", "email": "john@example.com", "roles": [...] },
    "token": "1|abc123..."
  }
}
```
**Error (401):**
```json
{ "success": false, "message": "Invalid credentials" }
```

### 1.3 Send OTP
```
POST /api/login/otp
```
**Request:**
```json
{ "email": "john@example.com" }
```
**Response (200):**
```json
{
  "success": true,
  "message": "Sign-in code sent to your email",
  "data": { "email": "john@example.com", "expires_in_minutes": 10, "dev_otp": "123456" }
}
```

### 1.4 Resend OTP
```
POST /api/login/otp/resend
```
**Request/Response:** Same as Send OTP

### 1.5 Verify OTP
```
POST /api/login/verify
```
**Request:**
```json
{ "email": "john@example.com", "otp": "123456" }
```
**Response (200):**
```json
{
  "success": true,
  "message": "Sign-in code verified successfully",
  "data": { "user": {...}, "token": "1|abc123..." }
}
```

### 1.6 Google Authentication
```
POST /api/auth/google
```
**Request:**
```json
{ "credential": "google_id_token_jwt_string" }
```
**Response (200):**
```json
{
  "success": true,
  "message": "Sign-in code sent to your email",
  "data": { "email": "user@gmail.com", "expires_in_minutes": 10, "dev_otp": "123456" }
}
```

### 1.7 Get Current User
```
GET /api/user
Headers: Authorization: Bearer <token>
```
**Response (200):**
```json
{
  "success": true,
  "data": { "id": 1, "name": "John Doe", "email": "john@example.com", "roles": [...] }
}
```

### 1.8 Logout
```
POST /api/logout
Headers: Authorization: Bearer <token>
```
**Response (200):**
```json
{ "success": true, "message": "Logged out successfully" }
```

---

## 2. Menu Management

### 2.1 Menu Tree (Complete Menu)
```
GET /api/menu/tree
Headers: Authorization: Bearer <token>
```
**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Main Course",
      "foods": [
        {
          "id": 1,
          "name": "Grilled Chicken",
          "price": 12.99,
          "variants": [{"id": 1, "name": "Large", "price": 15.99}],
          "addons": [{"id": 1, "name": "Extra Cheese", "items": [{"id": 1, "name": "Cheddar", "price": 1.50}]}]
        }
      ]
    }
  ]
}
```

---

### 2.2 Food Categories

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/food-categories` | List all categories |
| POST | `/api/food-categories` | Create category |
| GET | `/api/food-categories/{id}` | Get category |
| PUT | `/api/food-categories/{id}` | Update category |
| DELETE | `/api/food-categories/{id}` | Delete category |
| GET | `/api/food-categories/{id}/foods` | Get foods in category |
| PATCH | `/api/food-categories/{id}/toggle-active` | Toggle active status |

**Create/Update Request:**
```json
{
  "name": "Main Course",
  "description": "Delicious main courses",
  "image": "https://example.com/image.jpg",
  "is_active": true,
  "sort_order": 1
}
```

**Create Response (201):**
```json
{
  "success": true,
  "message": "Food category created successfully",
  "data": { "id": 1, "name": "Main Course", "is_active": true, "sort_order": 1, "foods": [...] }
}
```

---

### 2.3 Foods

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/foods` | List all foods |
| POST | `/api/foods` | Create food |
| GET | `/api/foods/{id}` | Get food |
| PUT | `/api/foods/{id}` | Update food |
| DELETE | `/api/foods/{id}` | Delete food |
| GET | `/api/foods/{id}/full` | Get food with all relations |
| GET | `/api/foods/{id}/variants` | Get food variants |
| GET | `/api/foods/{id}/addons` | Get food addons |
| PATCH | `/api/foods/{id}/toggle-availability` | Toggle availability |

**Create/Update Request:**
```json
{
  "category_id": 1,
  "name": "Grilled Chicken",
  "description": "Perfectly grilled chicken breast",
  "price": 12.99,
  "image": "https://example.com/chicken.jpg",
  "is_available": true
}
```

**Create Response (201):**
```json
{
  "success": true,
  "message": "Food created successfully",
  "data": { "id": 1, "category_id": 1, "name": "Grilled Chicken", "price": 12.99, "category": {...} }
}
```

---

### 2.4 Food Variants

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/food-variants` | List all variants |
| POST | `/api/food-variants` | Create variant |
| GET | `/api/food-variants/{id}` | Get variant |
| PUT | `/api/food-variants/{id}` | Update variant |
| DELETE | `/api/food-variants/{id}` | Delete variant |
| PATCH | `/api/food-variants/{id}/toggle-availability` | Toggle availability |

**Create/Update Request:**
```json
{
  "food_id": 1,
  "name": "Large",
  "price": 15.99,
  "is_available": true
}
```

---

### 2.5 Food Addons

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/food-addons` | List all addons |
| POST | `/api/food-addons` | Create addon |
| GET | `/api/food-addons/{id}` | Get addon |
| PUT | `/api/food-addons/{id}` | Update addon |
| DELETE | `/api/food-addons/{id}` | Delete addon |
| GET | `/api/food-addons/{id}/items` | Get addon items |
| PATCH | `/api/food-addons/{id}/toggle-availability` | Toggle availability |

**Create/Update Request:**
```json
{
  "food_id": 1,
  "name": "Toppings",
  "is_required": false,
  "max_items": 3,
  "is_available": true
}
```

---

### 2.6 Food Addon Items

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/food-addon-items` | List all addon items |
| POST | `/api/food-addon-items` | Create addon item |
| GET | `/api/food-addon-items/{id}` | Get addon item |
| PUT | `/api/food-addon-items/{id}` | Update addon item |
| DELETE | `/api/food-addon-items/{id}` | Delete addon item |
| PATCH | `/api/food-addon-items/{id}/toggle-availability` | Toggle availability |

**Create/Update Request:**
```json
{
  "addon_id": 1,
  "name": "Extra Cheese",
  "price": 1.50,
  "is_available": true
}
```

---

## 3. Customers

### 3.1 Customers

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/customers` | List all customers |
| POST | `/api/customers` | Create customer |
| GET | `/api/customers/{id}` | Get customer |
| PUT | `/api/customers/{id}` | Update customer |
| DELETE | `/api/customers/{id}` | Delete customer |

**Create/Update Request:**
```json
{
  "name": "Jane Smith",
  "phone": "0987654321",
  "email": "jane@example.com"
}
```

---

### 3.2 Customer Addresses

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/customer-addresses` | List all addresses |
| POST | `/api/customer-addresses` | Create address |
| GET | `/api/customer-addresses/{id}` | Get address |
| PUT | `/api/customer-addresses/{id}` | Update address |
| DELETE | `/api/customer-addresses/{id}` | Delete address |

**Create/Update Request:**
```json
{
  "customer_id": 1,
  "label": "Home",
  "address": "123 Main Street, Apt 4B",
  "city": "New York",
  "phone": "0987654321",
  "latitude": 40.7128,
  "longitude": -74.0060,
  "is_default": true
}
```

---

## 4. Promotions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/promotions` | List all promotions |
| POST | `/api/promotions` | Create promotion |
| GET | `/api/promotions/{id}` | Get promotion |
| PUT | `/api/promotions/{id}` | Update promotion |
| DELETE | `/api/promotions/{id}` | Delete promotion |

**Create/Update Request:**
```json
{
  "name": "Summer Sale",
  "code": "SUMMER20",
  "description": "20% off all orders",
  "discount_type": "percentage",
  "discount_value": 20,
  "min_order_amount": 50,
  "start_date": "2024-06-01",
  "end_date": "2024-08-31",
  "is_active": true
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Promotion created successfully",
  "data": { "id": 1, "name": "Summer Sale", "code": "SUMMER20", "discount_type": "percentage", "discount_value": 20 }
}
```

---

## 5. Orders

### 5.1 Orders

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/orders` | List all orders |
| POST | `/api/orders` | Create order |
| GET | `/api/orders/{id}` | Get order |
| PUT | `/api/orders/{id}` | Update order |
| DELETE | `/api/orders/{id}` | Delete order |

**Create/Update Request:**
```json
{
  "branch_id": 1,
  "table_id": 5,
  "customer_id": 1,
  "promotion_id": 1,
  "order_number": "ORD-2024-001",
  "order_type": "dine_in",
  "status": "pending",
  "subtotal": 45.97,
  "discount": 9.19,
  "tax": 3.28,
  "service_charge": 2.00,
  "delivery_fee": 0,
  "grand_total": 42.06,
  "notes": "No onions please"
}
```

**Order Types:** `dine_in`, `takeaway`, `delivery`
**Order Statuses:** `pending`, `confirmed`, `preparing`, `ready`, `out_for_delivery`, `completed`, `cancelled`

---

### 5.2 Order Items

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/order-items` | List all order items |
| POST | `/api/order-items` | Create order item |
| GET | `/api/order-items/{id}` | Get order item |
| PUT | `/api/order-items/{id}` | Update order item |
| DELETE | `/api/order-items/{id}` | Delete order item |

**Create/Update Request:**
```json
{
  "order_id": 1,
  "food_id": 1,
  "variant_id": 2,
  "quantity": 2,
  "unit_price": 12.99,
  "subtotal": 25.98,
  "notes": "Extra spicy"
}
```

---

### 5.3 Order Item Addons

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/order-item-addons` | List all |
| POST | `/api/order-item-addons` | Create |
| GET | `/api/order-item-addons/{id}` | Get one |
| PUT | `/api/order-item-addons/{id}` | Update |
| DELETE | `/api/order-item-addons/{id}` | Delete |

**Create/Update Request:**
```json
{
  "order_item_id": 1,
  "addon_item_id": 3,
  "price": 1.50
}
```

---

### 5.4 Order Status History

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/order-status-history` | List all |
| POST | `/api/order-status-history` | Create |
| GET | `/api/order-status-history/{id}` | Get one |
| PUT | `/api/order-status-history/{id}` | Update |
| DELETE | `/api/order-status-history/{id}` | Delete |

**Create/Update Request:**
```json
{
  "order_id": 1,
  "status": "confirmed",
  "changed_by": "Admin",
  "notes": "Order confirmed by manager"
}
```

---

## 6. Deliveries

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/deliveries` | List all deliveries |
| POST | `/api/deliveries` | Create delivery |
| GET | `/api/deliveries/{id}` | Get delivery |
| PUT | `/api/deliveries/{id}` | Update delivery |
| DELETE | `/api/deliveries/{id}` | Delete delivery |

**Create/Update Request:**
```json
{
  "order_id": 1,
  "delivery_driver_id": 2,
  "customer_address_id": 3,
  "status": "pending",
  "delivery_fee": 5.00,
  "notes": "Ring doorbell twice"
}
```

**Delivery Statuses:** `pending`, `assigned`, `picked_up`, `out_for_delivery`, `delivered`, `failed`

---

## 7. Restaurant Management

### 7.1 Restaurants

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/restaurants` | List all restaurants |
| POST | `/api/restaurants` | Create restaurant |
| GET | `/api/restaurants/{id}` | Get restaurant |
| PUT | `/api/restaurants/{id}` | Update restaurant |
| DELETE | `/api/restaurants/{id}` | Delete restaurant |

**Create/Update Request:**
```json
{
  "name": "Best Pizza Place",
  "phone": "555-0123",
  "email": "info@bestpizza.com",
  "address": "456 Food Street"
}
```

---

### 7.2 Restaurant Branches

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/restaurant-branches` | List all branches |
| POST | `/api/restaurant-branches` | Create branch |
| GET | `/api/restaurant-branches/{id}` | Get branch |
| PUT | `/api/restaurant-branches/{id}` | Update branch |
| DELETE | `/api/restaurant-branches/{id}` | Delete branch |

**Create/Update Request:**
```json
{
  "restaurant_id": 1,
  "name": "Downtown Branch",
  "phone": "555-0456",
  "address": "789 Main Avenue"
}
```

---

### 7.3 Restaurant Tables

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/restaurant-tables` | List all tables |
| POST | `/api/restaurant-tables` | Create table |
| GET | `/api/restaurant-tables/{id}` | Get table |
| PUT | `/api/restaurant-tables/{id}` | Update table |
| DELETE | `/api/restaurant-tables/{id}` | Delete table |

**Create/Update Request:**
```json
{
  "branch_id": 1,
  "table_number": "T-01",
  "capacity": 4,
  "status": "available"
}
```

**Table Statuses:** `available`, `occupied`, `reserved`

---

## 8. Staff Management

### 8.1 Staff Positions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/staff-positions` | List all positions |
| POST | `/api/staff-positions` | Create position |
| GET | `/api/staff-positions/{id}` | Get position |
| PUT | `/api/staff-positions/{id}` | Update position |
| DELETE | `/api/staff-positions/{id}` | Delete position |

**Create/Update Request:**
```json
{
  "title": "Head Chef",
  "base_salary": 3500.00
}
```

---

### 8.2 Employees

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/employees` | List all employees |
| POST | `/api/employees` | Create employee |
| GET | `/api/employees/{id}` | Get employee |
| PUT | `/api/employees/{id}` | Update employee |
| DELETE | `/api/employees/{id}` | Delete employee |

**Create/Update Request:**
```json
{
  "user_id": 1,
  "branch_id": 1,
  "position_id": 2,
  "hire_date": "2024-01-15",
  "salary": 3000.00
}
```

---

### 8.3 Delivery Drivers

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/delivery-drivers` | List all drivers |
| POST | `/api/delivery-drivers` | Create driver |
| GET | `/api/delivery-drivers/{id}` | Get driver |
| PUT | `/api/delivery-drivers/{id}` | Update driver |
| DELETE | `/api/delivery-drivers/{id}` | Delete driver |

**Create/Update Request:**
```json
{
  "user_id": 3,
  "branch_id": 1,
  "phone": "555-0789",
  "vehicle_type": "Motorcycle",
  "license_plate": "ABC-1234",
  "status": "available"
}
```

**Driver Statuses:** `available`, `busy`, `offline`

---

## 9. Inventory Management

### 9.1 Suppliers

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/suppliers` | List all suppliers |
| POST | `/api/suppliers` | Create supplier |
| GET | `/api/suppliers/{id}` | Get supplier |
| PUT | `/api/suppliers/{id}` | Update supplier |
| DELETE | `/api/suppliers/{id}` | Delete supplier |

**Create/Update Request:**
```json
{
  "name": "Fresh Foods Co.",
  "contact_name": "Mike Johnson",
  "phone": "555-1111",
  "email": "mike@freshfoods.com"
}
```

---

### 9.2 Ingredients

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/ingredients` | List all ingredients |
| POST | `/api/ingredients` | Create ingredient |
| GET | `/api/ingredients/{id}` | Get ingredient |
| PUT | `/api/ingredients/{id}` | Update ingredient |
| DELETE | `/api/ingredients/{id}` | Delete ingredient |

**Create/Update Request:**
```json
{
  "name": "Chicken Breast",
  "unit": "kg",
  "reorder_level": 10
}
```

---

### 9.3 Inventory (Stock Levels)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/inventory` | List all stock levels |
| POST | `/api/inventory` | Create stock level |
| GET | `/api/inventory/{id}` | Get stock level |
| PUT | `/api/inventory/{id}` | Update stock level |
| DELETE | `/api/inventory/{id}` | Delete stock level |

**Create/Update Request:**
```json
{
  "branch_id": 1,
  "ingredient_id": 1,
  "quantity": 50
}
```

---

### 9.4 Inventory Transactions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/inventory-transactions` | List all transactions |
| POST | `/api/inventory-transactions` | Create transaction |
| GET | `/api/inventory-transactions/{id}` | Get transaction |
| PUT | `/api/inventory-transactions/{id}` | Update transaction |
| DELETE | `/api/inventory-transactions/{id}` | Delete transaction |

**Create/Update Request:**
```json
{
  "inventory_id": 1,
  "supplier_id": 1,
  "transaction_type": "restock",
  "quantity": 20,
  "unit_cost": 5.50,
  "notes": "Weekly restock"
}
```

**Transaction Types:** `restock`, `usage`, `waste`, `adjustment`

---

### 9.5 Recipes

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/recipes` | List all recipes |
| POST | `/api/recipes` | Create recipe |
| GET | `/api/recipes/{id}` | Get recipe |
| PUT | `/api/recipes/{id}` | Update recipe |
| DELETE | `/api/recipes/{id}` | Delete recipe |

**Create/Update Request:**
```json
{
  "food_id": 1,
  "variant_id": 1,
  "instructions": "Marinate chicken for 30 minutes, then grill on medium heat for 6-8 minutes each side."
}
```

---

### 9.6 Recipe Items

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/recipe-items` | List all recipe items |
| POST | `/api/recipe-items` | Create recipe item |
| GET | `/api/recipe-items/{id}` | Get recipe item |
| PUT | `/api/recipe-items/{id}` | Update recipe item |
| DELETE | `/api/recipe-items/{id}` | Delete recipe item |

**Create/Update Request:**
```json
{
  "recipe_id": 1,
  "ingredient_id": 1,
  "quantity_required": 0.5
}
```

---

## 10. Payments

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/payments` | List all payments |
| POST | `/api/payments` | Create payment |
| GET | `/api/payments/{id}` | Get payment |
| PUT | `/api/payments/{id}` | Update payment |
| DELETE | `/api/payments/{id}` | Delete payment |

**Create/Update Request:**
```json
{
  "order_id": 1,
  "payment_method": "credit_card",
  "amount": 42.06,
  "status": "paid",
  "transaction_reference": "TXN-2024-001"
}
```

**Payment Methods:** `cash`, `credit_card`, `qr_code`
**Payment Statuses:** `pending`, `paid`, `failed`, `refunded`

---

## 11. Roles & Permissions

### 11.1 Roles

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/roles` | List all roles |
| POST | `/api/roles` | Create role |
| GET | `/api/roles/{id}` | Get role |
| PUT | `/api/roles/{id}` | Update role |
| DELETE | `/api/roles/{id}` | Delete role |

**Create/Update Request:**
```json
{
  "name": "admin",
  "description": "Full system access"
}
```

---

### 11.2 User Roles

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/user-roles` | List all user-role assignments |
| POST | `/api/user-roles` | Assign role to user |
| GET | `/api/user-roles/{id}` | Get assignment |
| PUT | `/api/user-roles/{id}` | Update assignment |
| DELETE | `/api/user-roles/{id}` | Remove assignment |

**Create/Update Request:**
```json
{
  "user_id": 1,
  "role_id": 1
}
```

---

## Testing Tips

### Quick Start with cURL

**Register:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123"}'
```

**Login:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'
```

**Use Token:**
```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Create Food Category:**
```bash
curl -X POST http://localhost:8000/api/food-categories \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"name":"Appetizers","description":"Start your meal right","is_active":true,"sort_order":1}'
```

**Get Menu Tree:**
```bash
curl -X GET http://localhost:8000/api/menu/tree \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Recommended Testing Order

1. **Auth first:** Register → Login → Get token
2. **Setup restaurant:** Create Restaurant → Create Branch → Create Tables
3. **Create menu:** Categories → Foods → Variants → Addons → Addon Items
4. **Manage staff:** Positions → Employees
5. **Create test customer:** Customer → Customer Addresses
6. **Create promotions:** Promotions
7. **Test orders:** Create Order → Add Order Items → Add Order Item Addons → Update Status
8. **Test payments:** Create Payment for order
9. **Test inventory:** Suppliers → Ingredients → Inventory → Transactions → Recipes → Recipe Items
10. **Test roles:** Roles → User Roles

---

## Postman Collection Import

For easy testing, you can import these endpoints into Postman:
1. Create a new Collection called "Restaurant API"
2. Add requests following the documentation above
3. Set environment variables: `base_url = http://localhost:8000/api` and `token = <your_token>`
4. Use `{{base_url}}` and `Authorization: Bearer {{token}}` in headers

---

*Last Updated: September 2026*
