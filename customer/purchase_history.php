<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login.php");
    exit();
}

try {
    // Get customer's purchase history
    $stmt = $conn->prepare("
        SELECT s.sale_date, p.name AS product_name, p.price, 
               s.quantity, s.total_price, st.store_name
        FROM sales s
        JOIN products p ON s.product_id = p.product_id
        LEFT JOIN stores st ON p.store_id = st.store_id
        WHERE s.customer_id = :customer_id
        ORDER BY s.sale_date DESC
    ");
    $stmt->bindParam(':customer_id', $_SESSION['customer_id']);
    $stmt->execute();
    $purchases = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
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
            display: flex;
            flex-direction: column;
            background: linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(201, 214, 255, 1) 100%);
        }
        .wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 40px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 36px;
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }

        .history-header, .history-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr;
            gap: 15px;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .history-header {
            font-weight: bold;
            border-bottom: 2px solid #7494ec;
        }

        .no-history {
            text-align: center;
            padding: 50px;
            color: #777;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #7494ec;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .date {
            font-size: 14px;
            color: #777;
        }

        .total {
            font-weight: bold;
            color: #7494ec;
        }

        .store {
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <h1>Your Purchase History</h1>

            <?php if (!empty($purchases)): ?>
                <div class="history-header">
                    <div>Product</div>
                    <div>Unit Price</div>
                    <div>Quantity</div>
                    <div>Total</div>
                    <div>Store</div>
                    <div>Date</div>
                </div>

                <?php foreach ($purchases as $purchase): ?>
                    <div class="history-item">
                        <div><?php echo htmlspecialchars($purchase['product_name']); ?></div>
                        <div>$<?php echo number_format($purchase['price'], 2); ?></div>
                        <div><?php echo $purchase['quantity']; ?></div>
                        <div class="total">$<?php echo number_format($purchase['total_price'], 2); ?></div>
                        <div class="store"><?php echo htmlspecialchars($purchase['store_name'] ?? 'N/A'); ?></div>
                        <div class="date"><?php echo date('M j, Y', strtotime($purchase['sale_date'])); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-history">
                    <h2>No purchase history found</h2>
                    <p>Your purchased items will appear here</p>
                </div>
            <?php endif; ?>

            <a href="customer_dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
