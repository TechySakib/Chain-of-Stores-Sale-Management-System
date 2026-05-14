<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: ../login.php");
    exit();
}

// DB connection
include '../includes/db.php';
$conn = $mysqli_conn;

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$store_id = '';
$product_name = '';
$product_price = '';
$product_quantity = '';
$errors = [];
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input
    $store_id = trim($_POST['store_id']);
    $product_name = trim($_POST['product_name']);
    $product_price = trim($_POST['product_price']);
    $product_quantity = trim($_POST['product_quantity']);

    // Validation
    if (empty($store_id)) {
        $errors[] = "Store selection is required.";
    }

    if (empty($product_name)) {
        $errors[] = "Product name is required.";
    }

    if (empty($product_price)) {
        $errors[] = "Price is required.";
    } elseif (!is_numeric($product_price) || $product_price <= 0) {
        $errors[] = "Price must be a positive number.";
    }

    if (empty($product_quantity)) {
        $errors[] = "Quantity is required.";
    } elseif (!is_numeric($product_quantity) || $product_quantity < 0) {
        $errors[] = "Quantity must be a non-negative number.";
    }

    // Insert into database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, stock_quantity, store_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdii", $product_name, $product_price, $product_quantity, $store_id);

        if ($stmt->execute()) {
            $success = "Product added successfully!";
            $product_name = $product_price = $product_quantity = ""; // Clear form
        } else {
            $errors[] = "Error adding product. Please try again.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(201, 214, 255, 1) 100%);
            min-height: 100vh;
            padding-top: 121px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding-top: 60px;
            padding-left: 40px;
            padding-right: 40px;
            padding-bottom: 100px;
        }

        h1 {
            color: #7494ec;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #7494ec;
            outline: none;
            box-shadow: 0 0 0 3px rgba(116, 148, 236, 0.2);
        }

        .btn {
            background-color: #7494ec;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
        }

        .btn:hover {
            background-color: #5a7bd4;
            transform: translateY(-2px);
        }

        .error {
            color: #d9534f;
            background-color: #f2dede;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .success {
            color: #3c763d;
            background-color: #dff0d8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #7494ec;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .back-link:hover {
            color: #5a7bd4;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-box"></i> Add New Product</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="store_id">Store:</label>
                <select name="store_id" id="store_id" required>
                    <option value="">Select a store</option>
                    <?php
                    $stores = $conn->query("SELECT store_id, store_name FROM stores");
                    while ($store = $stores->fetch_assoc()) {
                        $selected = ($store['store_id'] == $store_id) ? 'selected' : '';
                        echo "<option value='{$store['store_id']}' $selected>{$store['store_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="product_price">Price:</label>
                <input type="number" id="product_price" name="product_price" step="0.01" min="0.01" value="<?php echo htmlspecialchars($product_price); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="product_quantity">Quantity:</label>
                <input type="number" id="product_quantity" name="product_quantity" min="0" value="<?php echo htmlspecialchars($product_quantity); ?>" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Add Product</button>
            </div>
        </form>
        
        <a href="manager_dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>