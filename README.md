# 🏬 Chain of Stores Sales Management System

A comprehensive web-based sales management system designed to manage multiple retail stores, their employees, products, and customer interactions. Built using PHP, MySQL, HTML, and CSS.

## 📌 Features

### 🔐 Authentication
- Customer and Employee Login System
- Role-based access (Manager, Salesman, Customer)

### 🧑‍💼 Manager Module
- Dashboard with key metrics
- Add/View Employees
- Add/View Stores
- Add/View Products
- Assign Employees to Stores
- View Monthly Sales Reports

### 👨‍💼 Employee (Salesman) Module
- Dashboard
- View Assigned Stores
- Manage Sales Records
- View Personal Sales History

### 👥 Customer Module
- Register/Login
- Browse Products
- Submit Reviews
- View Purchase History

## 🧰 Technologies Used
- **Frontend**: HTML, CSS
- **Backend**: PHP
- **Database**: MySQL (`chain_of_store`)
- **Architecture**: Role-Based Access Control

## 🗂️ Project Structure
store_management/

<pre>store_management/
├── includes/                         # Configuration and core scripts
│   ├── db_connect.php                # MySQL database connection
│   └── auth.php                      # Authentication and session handling
│
├── manager/                          # Manager (Admin-level) functionalities
│   ├── manager_dashboard.php
│   ├── add_employee.php
│   ├── view_employees.php
│   ├── add_store.php
│   ├── view_stores.php
│   ├── add_product.php
│   ├── view_products.php
│   ├── assign_employee_store.php
│   └── sales_reports.php
│
├── employee/                         # Salesman role functionalities
│   ├── employee_dashboard.php
│   ├── view_assigned_stores.php
│   ├── manage_sales.php
│   └── view_sales_history.php
│
├── customer/                         # Customer-facing pages
│   ├── customer_dashboard.php
│   ├── browse_products.php
│   ├── submit_review.php
│   └── purchase_history.php
│
├── sql/                              # SQL files
│   └── chain_of_store.sql            # (Optional) SQL file to create and populate the database
│
├── register.php                      # Global customer registration
├── login.php                         # Login page for all roles
├── logout.php                        # Ends session and redirects to login
├── session.php                       # Manages active session variables
└── README.md                         # Project documentation</pre>  

# Step 1: Move to your server root (htdocs for XAMPP)
cd /path/to/xampp/htdocs

# Step 2: Clone or move the project folder
# (If using Git)
git clone https://github.com/TechySakib/store_management.git

# OR (If copying manually)
mv /your/downloads/store_management .

# Step 3: Launch phpMyAdmin in your browser
# Go to:
http://localhost/phpmyadmin/

# Step 4: Create a new database
# In phpMyAdmin:
# - Click "New"
# - Name it: chain_of_store
# - Click "Create"

# Step 5: Import the SQL file
# In phpMyAdmin:
# - Select the `chain_of_store` database
# - Go to the "Import" tab
# - Choose the .sql file (e.g., store_management/sql/chain_of_store.sql)
# - Click "Go"

# Step 6: Configure database connection
# Edit the db_connect.php file:
nano store_management/includes/db_connect.php

# Make sure your credentials look like this (for XAMPP):
# $conn = new mysqli("localhost", "root", "", "chain_of_store");

# Step 7: Start Apache and MySQL (if not already running)
# You can use the XAMPP Control Panel or:

/path/to/xampp/xampp startapache
/path/to/xampp/xampp startmysql

# Step 8: Run the project
# Open your browser and go to:
http://localhost/store_management/

