# 📡 API Endpoints Documentation

## 🔐 Authentication Required

All API endpoints (except login and register) require authentication using Laravel Sanctum tokens.

### Headers Required:
```
Authorization: Bearer {your_token_here}
Accept: application/json
Content-Type: application/json
```

### Getting Auth Token:
```bash
# Login to get token
curl -X POST http://your-app.test/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Use the token in subsequent requests
curl -X GET http://your-app.test/api/orders \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

---

## 📋 Available Endpoints

### 🔐 **Authentication**
```
POST   /api/login        # User login
POST   /api/register     # User registration  
POST   /api/logout       # User logout (requires auth)
GET    /api/user         # Get current user info (requires auth)
```

---

### 👥 **Orders Management**
```
GET    /api/orders                    # List all orders (paginated)
POST   /api/orders                    # Create new order
GET    /api/orders/{id}               # Get specific order
PUT    /api/orders/{id}               # Update order
DELETE /api/orders/{id}               # Delete order
```

**Order Creation Example:**
```json
{
  "customer_id": 1,
  "order_date": "2025-01-15",
  "advance_paid": 500.00,
  "remarks": "Urgent order",
  "items": [
    {
      "product_name": "Men's Shirt",
      "garment_type": "Shirt",
      "fabric_type": "Cotton",
      "color": "Blue",
      "quantity": 2,
      "unit_price": 1500.00
    }
  ]
}
```

---

### 👨‍💼 **Staff Management**
```
GET    /api/staff                     # List all staff (paginated)
POST   /api/staff                     # Create new staff member
GET    /api/staff/{id}                # Get specific staff member
PUT    /api/staff/{id}                # Update staff member
DELETE /api/staff/{id}                # Delete staff member
```

**Staff Creation Example:**
```json
{
  "full_name": "John Doe",
  "phone": "+1234567890",
  "email": "john@example.com",
  "role_id": 1,
  "joining_date": "2025-01-01",
  "address": "123 Main St",
  "shift_start_time": "09:00",
  "shift_end_time": "17:00",
  "status": true,
  "base_salary": 50000.00
}
```

---

### 🧵 **Fabrics Management**
```
GET    /api/fabrics                   # List all fabrics (paginated)
POST   /api/fabrics                   # Create new fabric
GET    /api/fabrics/{id}              # Get specific fabric
PUT    /api/fabrics/{id}              # Update fabric
DELETE /api/fabrics/{id}              # Delete fabric
POST   /api/fabrics/import            # Import fabrics from Excel
```

**Fabric Creation Example:**
```json
{
  "fabric": "Premium Cotton",
  "description": "High quality cotton fabric"
}
```

---

### 👔 **Garments Management (Masters)**
```
GET    /api/masters                   # List all garments (paginated)
POST   /api/masters                   # Create new garment
GET    /api/masters/{id}              # Get specific garment
PUT    /api/masters/{id}              # Update garment
DELETE /api/masters/{id}              # Delete garment
POST   /api/masters/import-garments   # Import garments from Excel
```

---

### 📏 **Measurements Management**
```
GET    /api/masters/measurements      # List all measurements
POST   /api/masters/measurements      # Create new measurement field
PUT    /api/masters/measurements/{id} # Update measurement field
DELETE /api/masters/measurements/{id} # Delete measurement field
POST   /api/masters/import-measurements # Import measurements from Excel
```

**Measurement Creation Example:**
```json
{
  "label": "Chest",
  "description": "Chest measurement",
  "unit": "inches"
}
```

---

### ✅ **Attendance Management**
```
GET    /api/attendance                # List attendance records
POST   /api/attendance                # Record attendance
GET    /api/attendance/{id}           # Get specific attendance record
PUT    /api/attendance/{id}           # Update attendance record
DELETE /api/attendance/{id}           # Delete attendance record
```

---

### 📊 **Dashboard**
```
GET    /api/dashboard                 # Get dashboard statistics
```

---

### 🔗 **Relations (Garment-Measurement)**
```
GET    /api/relations                 # List all garment-measurement relations
POST   /api/relations                 # Create/update garment-measurement relation
GET    /api/relations/{id}            # Get specific relation
PUT    /api/relations/{id}            # Update relation
DELETE /api/relations/{id}            # Delete relation
GET    /api/relations/measurements/{id} # Get measurements for specific garment
```

---

### 🎭 **Roles Management**
```
GET    /api/roles                     # List all roles (paginated)
POST   /api/roles                     # Create new role
GET    /api/roles/{id}                # Get specific role
PUT    /api/roles/{id}                # Update role
DELETE /api/roles/{id}                # Delete role
POST   /api/roles/import              # Import roles from Excel
```

**Role Creation Example:**
```json
{
  "role": "Tailor",
  "description": "Professional tailor",
  "status": true
}
```

---

### 💰 **Salary Management**
```
GET    /api/salary                    # List salary records
POST   /api/salary                    # Create salary record
GET    /api/salary/{id}               # Get specific salary record
PUT    /api/salary/{id}               # Update salary record
DELETE /api/salary/{id}               # Delete salary record
GET    /api/staff/{id}/salary         # Get staff salary details
POST   /api/staff/{id}/salary         # Create staff salary record
```

---

## 📱 **Example API Usage**

### **1. Login and Get Token**
```bash
curl -X POST http://your-app.test/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com"
  },
  "token": "1|abcdef123456..."
}
```

### **2. Create a New Order**
```bash
curl -X POST http://your-app.test/api/orders \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "order_date": "2025-01-15",
    "advance_paid": 500.00,
    "remarks": "Urgent order for wedding",
    "items": [
      {
        "product_name": "Men\'s Suit",
        "garment_type": "Suit",
        "fabric_type": "Wool",
        "color": "Navy Blue",
        "quantity": 1,
        "unit_price": 5000.00
      },
      {
        "product_name": "Dress Shirt",
        "garment_type": "Shirt",
        "fabric_type": "Cotton",
        "color": "White",
        "quantity": 2,
        "unit_price": 800.00
      }
    ]
  }'
```

### **3. Get All Orders**
```bash
curl -X GET http://your-app.test/api/orders \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "customer_id": 1,
      "order_date": "2025-01-15",
      "total_amount": 6600.00,
      "advance_paid": 500.00,
      "pending_amount": 6100.00,
      "status": "pending",
      "remarks": "Urgent order for wedding",
      "customer": {
        "id": 1,
        "name": "John Smith",
        "phone": "+1234567890"
      },
      "items": [
        {
          "id": 1,
          "order_id": 1,
          "product_name": "Men's Suit",
          "garment_type": "Suit",
          "fabric_type": "Wool",
          "color": "Navy Blue",
          "quantity": 1,
          "unit_price": 5000.00
        }
      ]
    }
  ],
  "current_page": 1,
  "per_page": 15,
  "total": 25,
  "last_page": 2
}
```

### **4. Update Staff Member**
```bash
curl -X PUT http://your-app.test/api/staff/1 \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "John Doe Updated",
    "phone": "+1234567890",
    "email": "john.updated@example.com",
    "role_id": 2,
    "joining_date": "2025-01-01",
    "address": "456 Updated Street",
    "shift_start_time": "09:00",
    "shift_end_time": "17:00",
    "status": true
  }'
```

---

## 🔍 **Error Responses**

### **Validation Error (422)**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

### **Authentication Error (401)**
```json
{
  "message": "Unauthenticated."
}
```

### **Not Found Error (404)**
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### **Server Error (500)**
```json
{
  "success": false,
  "message": "An error occurred: Something went wrong"
}
```

---

## 🧪 **Testing with Postman**

### **Environment Setup:**
1. Create a new environment in Postman
2. Add variables:
   - `base_url`: `http://your-app.test/api`
   - `token`: `{{your_token_here}}`

### **Test Collection Structure:**
```
📁 Boutique Management API
  ├── 🔐 Authentication
  │   ├── POST Login
  │   ├── POST Register
  │   └── POST Logout
  ├── 👥 Orders
  │   ├── GET Orders
  │   ├── POST Create Order
  │   ├── GET Order by ID
  │   ├── PUT Update Order
  │   └── DELETE Order
  ├── 👨‍💼 Staff
  │   ├── GET Staff List
  │   ├── POST Create Staff
  │   ├── GET Staff by ID
  │   ├── PUT Update Staff
  │   └── DELETE Staff
  └── 🧵 Fabrics
      ├── GET Fabrics
      ├── POST Create Fabric
      ├── GET Fabric by ID
      ├── PUT Update Fabric
      └── DELETE Fabric
```

### **Pre-request Script for Authentication:**
```javascript
// Add this to collection level pre-request script
if (!pm.environment.get('token')) {
    console.log('No token found. Please login first.');
    return;
}

pm.request.headers.add({
    key: 'Authorization',
    value: 'Bearer ' + pm.environment.get('token')
});
```

---

## 🚀 **Quick Start Guide**

1. **Install Laravel Sanctum** (if not done):
   ```bash
   composer require laravel/sanctum
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   php artisan migrate
   ```

2. **Configure CORS** for mobile apps:
   ```bash
   composer require fruitcake/laravel-cors
   ```

3. **Test the APIs** using the examples above

4. **Integrate with your mobile app** using the endpoints

Your Laravel backend is now ready to serve both web and mobile applications! 🎉