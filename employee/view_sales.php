<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['employee_id'])) {
    header("Location: ../login.php");
    exit();
}

try {
    // Get sales recorded by this employee
    $stmt = $conn->prepare("
        SELECT s.sale_id, s.sale_date, p.name AS product_name, 
               s.quantity, s.total_price, c.name AS customer_name
        FROM sales s
        JOIN products p ON s.product_id = p.product_id
        LEFT JOIN customers c ON s.customer_id = c.customer_id
        WHERE s.employee_id = :employee_id
        ORDER BY s.sale_date DESC
        LIMIT 50
    ");
    $stmt->bindParam(':employee_id', $_SESSION['employee_id']);
    $stmt->execute();
    $sales = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales History</title>
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
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
        }

        .right-panel h1 {
            font-size: 28px;
            text-align: center;
            font-weight: bold;
            color: #7494ec;
            margin-bottom: 20px;
        }

        .sales-container {
            flex-grow: 1;
            overflow-y: auto;
            padding-right: 10px;
        }

        .sales-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .sales-table th {
            background-color: #7494ec;
            color: white;
            padding: 10px;
            text-align: left;
            position: sticky;
            top: 0;
        }

        .sales-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .sales-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .currency {
            text-align: right;
        }

        .quantity {
            text-align: center;
        }

        .date {
            white-space: nowrap;
        }

        .no-sales {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }

        .back-link {
            display: inline-block;
            background-color: #7494ec;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
            margin-top: 15px;
            align-self: center;
        }

        .back-link:hover {
            background-color: #5a7bd4;
            transform: translateY(-2px);
        }

        .welcome-message {
            font-size: 16px;
            text-align: center;
            margin-bottom: 20px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="left-panel">
            <div class="left-panel-content">
                <h2>Sales History</h2>
                <p>View your recorded sales transactions</p>
            </div>
        </div>

        <div class="right-panel">
            <h1><i class="fas fa-history"></i> Your Sales</h1>
            <p class="welcome-message">Hello, <?php echo isset($_SESSION['employee_name']) ? htmlspecialchars($_SESSION['employee_name']) : 'Employee'; ?>!</p>
            
            <div class="sales-container">
                <?php if (empty($sales)): ?>
                    <div class="no-sales">
                        <p>No sales recorded yet.</p>
                    </div>
                <?php else: ?>
                    <table class="sales-table">
                        <thead>
                            <tr>
                                <th class="date">Date</th>
                                <th>Product</th>
                                <th class="quantity">Qty</th>
                                <th class="currency">Amount</th>
                                <th>Customer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <td class="date"><?php echo date('M j, Y H:i', strtotime($sale['sale_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                                    <td class="quantity"><?php echo $sale['quantity']; ?></td>
                                    <td class="currency">$<?php echo number_format($sale['total_price'], 2); ?></td>
                                    <td><?php echo $sale['customer_name'] ? htmlspecialchars($sale['customer_name']) : 'Guest'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
            <a href="employee_dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>