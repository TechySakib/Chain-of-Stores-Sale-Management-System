# 🏬 Chain of Stores Sales Management System

A comprehensive web-based sales management system designed to manage multiple retail stores, their employees, products, and customer interactions. Built using PHP, MySQL, HTML, and CSS.

## 📌 Features

### 🔐 Authentication
- Unified Login System for Customers and Employees
- Role-based access control (Manager, Salesman, Customer)

### 🧑‍💼 Manager Module
- Administrative Dashboard
- Employee Management (Add/View/Delete)
- Store Management
- Product Management
- Employee-to-Store Assignments
- Sales Analytics and Reports

### 👨‍💼 Employee (Salesman) Module
- Personal Dashboard
- Assigned Store Details
- Real-time Sales Recording
- Personal Sales History

### 👥 Customer Module
- Account Registration
- Product Browsing with Detailed Popups
- Product Reviews and Ratings
- Shopping Cart and Purchase History

## 🧰 Technologies Used
- **Frontend**: HTML5, CSS3 (Vanilla), FontAwesome
- **Backend**: PHP (PDO & MySQLi Hybrid)
- **Database**: MySQL
- **Design**: Modern UI with Glassmorphism and Responsive Layouts

## 🗂️ Project Structure

<pre>
├── includes/                         # Core components
│   ├── db.php                        # Centralized PDO/MySQLi connection
│   └── footer.php                     # Shared footer template
│
├── manager/                          # Administrative functionalities
│   ├── manager_dashboard.php
│   ├── add_employee.php
│   ├── view_employees.php
│   ├── add_store.php
│   ├── add_product.php
│   ├── view_products.php
│   ├── assign_employee_store.php
│   └── sales_reports.php
│
├── employee/                         # Salesman functionalities
│   ├── employee_dashboard.php
│   ├── view_assigned_store.php
│   ├── record_sales.php
│   └── view_sales.php
│
├── customer/                         # Customer portal
│   ├── customer_dashboard.php
│   ├── browse_products.php
│   ├── review_product.php
│   ├── purchase_history.php
│   └── cart.php
│
├── login.php                         # Unified login
├── register.php                      # Customer registration
├── logout.php                        # Session termination
├── chain_of_store.sql                # Database schema
└── README.md                         # Documentation
</pre>

## 🚀 Installation & Setup

### Step 1: Server Environment
Move the project folder to your local server root (e.g., `htdocs` for XAMPP or `www` for WAMP).

### Step 2: Database Setup
1. Open **phpMyAdmin**.
2. Create a new database named `chain_of_store`.
3. Select the database and go to the **Import** tab.
4. Upload and import the `chain_of_store.sql` file located in the root directory.

### Step 3: Configuration
Edit `includes/db.php` if your MySQL credentials differ from the defaults:
```php
$host = '127.0.0.1';
$db   = 'chain_of_store';
$user = 'root';
$pass = ''; // Set your password here
```

### Step 4: Run the Project
Open your browser and navigate to:
`http://localhost/Chain-of-Stores-Sale-Management-System/`

## 🔐 Default Credentials (Test Accounts)
- **Manager**: `manager@store.com` / `password`
- **Salesman**: `salesman@store.com` / `password`
