<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['employee_id'])) {
    header("Location: ../login.php");
    exit();
}

$errors = [];
$success = '';

try {
    // Get the assigned store ID for the employee
    $stmt = $conn->prepare("
        SELECT store_id 
        FROM employee_store_assignments 
        WHERE employee_id = :employee_id
    ");
    $stmt->bindParam(':employee_id', $_SESSION['employee_id']);
    $stmt->execute();
    $assignment = $stmt->fetch();

    if ($assignment) {
        $store_id = $assignment['store_id'];
        
        // Fetch products from the store
        $stmt = $conn->prepare("
            SELECT product_id, name, price, stock_quantity 
            FROM products 
            WHERE store_id = :store_id
            ORDER BY name
        ");
        $stmt->bindParam(':store_id', $store_id);
        $stmt->execute();
        $products = $stmt->fetchAll();
    } else {
        $products = [];
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product_id = $_POST['product_id'] ?? '';
        $quantity = $_POST['quantity'] ?? '';
        $customer_id = !empty($_POST['customer_id']) ? $_POST['customer_id'] : null;

        // Validation
        if (empty($product_id)) {
            $errors[] = "Product selection is required.";
        }

        if (empty($quantity) || !is_numeric($quantity) || $quantity <= 0) {
            $errors[] = "Valid quantity is required.";
        }

        // Validate customer exists if provided
        if (!empty($customer_id)) {
            $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE customer_id = :customer_id");
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                $errors[] = "Customer with ID $customer_id does not exist.";
            }
        }

        // Get product price and check stock
        if (empty($errors)) {
            $stmt = $conn->prepare("
                SELECT price, stock_quantity 
                FROM products 
                WHERE product_id = :product_id
            ");
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
            $product = $stmt->fetch();

            if (!$product) {
                $errors[] = "Selected product not found.";
            } elseif ($product['stock_quantity'] < $quantity) {
                $errors[] = "Not enough stock available. Only {$product['stock_quantity']} left.";
            } else {
                $total_price = $product['price'] * $quantity;
            }
        }

        // Record the sale
        if (empty($errors)) {
            $conn->beginTransaction();

            try {
                // Insert sale record with employee_id
                $stmt = $conn->prepare("
                    INSERT INTO sales (customer_id, product_id, quantity, total_price, sale_date, employee_id)
                    VALUES (:customer_id, :product_id, :quantity, :total_price, NOW(), :employee_id)
                ");
                
                // Properly handle NULL customer_id
                if (empty($customer_id)) {
                    $stmt->bindValue(':customer_id', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindParam(':customer_id', $customer_id);
                }
                
                $stmt->bindParam(':product_id', $product_id);
                $stmt->bindParam(':quantity', $quantity);
                $stmt->bindParam(':total_price', $total_price);
                $stmt->bindParam(':employee_id', $_SESSION['employee_id']);
                $stmt->execute();

                // Update product stock
                $stmt = $conn->prepare("
                    UPDATE products 
                    SET stock_quantity = stock_quantity - :quantity 
                    WHERE product_id = :product_id
                ");
                $stmt->bindParam(':quantity', $quantity);
                $stmt->bindParam(':product_id', $product_id);
                $stmt->execute();

                $conn->commit();
                $success = "Sale recorded successfully!";
            } catch (PDOException $e) {
                $conn->rollBack();
                $errors[] = "Error recording sale: " . $e->getMessage();
            }
        }
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Sales</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        html, body {
            height: 100%;
        }

        body {
            background: linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(201, 214, 255, 1) 100%);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .dashboard-container {
            width: 850px;
            height: 550px;
            background-color: #fff;
            border-radius: 30px;
            margin: 150px auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
        }

        .left-panel {
            background-color: #7494ec;
            width: 50%;
            height: 100%;
            border-radius: 30px 30% 30% 30px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .left-panel-content h2 {
            font-size: 36px;
            font-weight: bolder;
            margin-bottom: 20px;
        }

        .right-panel {
            width: 50%;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right-panel h1 {
            font-size: 28px;
            text-align: center;
            font-weight: bold;
            color: #7494ec;
            margin-bottom: 20px;
        }

        .welcome-message {
            font-size: 16px;
            text-align: center;
            margin-bottom: 25px;
            color: #555;
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

        select, input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        select:focus, input:focus {
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
            margin-top: 10px;
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

        .total-price {
            font-size: 18px;
            font-weight: 600;
            color: #7494ec;
            margin: 15px 0;
            text-align: center;
        }

        .back-link {
            display: inline-block;
            margin-top: 15px;
            background-color: #f1f1f1;
            color: #7494ec;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
            width: 100%;
        }

        .back-link:hover {
            background-color: #e1e1e1;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="left-panel">
            <div class="left-panel-content">
                <h2>Record Sale</h2>
                <p>Enter new sales transaction details</p>
            </div>
        </div>

        <div class="right-panel">
            <h1><i class="fas fa-cash-register"></i> New Sale</h1>
            <p class="welcome-message">Hello, <?php echo isset($_SESSION['employee_name']) ? htmlspecialchars($_SESSION['employee_name']) : 'Employee'; ?>!</p>
            
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
            
            <form method="post" id="saleForm">
                <div class="form-group">
                    <label for="product_id">Product:</label>
                    <select name="product_id" id="product_id" required>
                        <option value="">Select a product</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['product_id']; ?>" 
                                    data-price="<?php echo $product['price']; ?>">
                                <?php echo htmlspecialchars($product['name']); ?> 
                                ($<?php echo number_format($product['price'], 2); ?>)
                                (Stock: <?php echo $product['stock_quantity']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" min="1" required>
                </div>
                
                <div class="form-group">
                    <label for="customer_id">Customer ID (optional):</label>
                    <input type="number" name="customer_id" id="customer_id" min="1" placeholder="Leave blank for anonymous sale">
                </div>
                
                <div id="totalPriceDisplay" class="total-price" style="display: none;">
                    Total Price: $<span id="totalPrice">0.00</span>
                </div>
                
                <button type="submit" class="btn">Record Sale</button>
                <a href="employee_dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            </form>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>

    <script>
        // Calculate and display total price in real-time
        document.getElementById('product_id').addEventListener('change', calculateTotal);
        document.getElementById('quantity').addEventListener('input', calculateTotal);

        function calculateTotal() {
            const productSelect = document.getElementById('product_id');
            const quantityInput = document.getElementById('quantity');
            const totalPriceDisplay = document.getElementById('totalPriceDisplay');
            const totalPriceSpan = document.getElementById('totalPrice');
            
            if (productSelect.value && quantityInput.value) {
                const price = parseFloat(productSelect.options[productSelect.selectedIndex].getAttribute('data-price'));
                const quantity = parseInt(quantityInput.value);
                const total = price * quantity;
                
                totalPriceSpan.textContent = total.toFixed(2);
                totalPriceDisplay.style.display = 'block';
            } else {
                totalPriceDisplay.style.display = 'none';
            }
        }
    </script>
</body>
</html>