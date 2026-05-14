<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout System Maintenance</title>
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
            padding-top: 40px;
            padding-left: 40px;
            padding-right: 40px;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            flex: 1;
            text-align: center;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 30px;
            color: #555;
        }

        .maintenance-icon {
            font-size: 80px;
            color: #7494ec;
            margin-bottom: 30px;
        }

        .maintenance-message {
            background-color: #f8f9fe;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border-left: 5px solid #7494ec;
        }

        .maintenance-message h2 {
            color: #e74a3b;
            margin-bottom: 15px;
        }

        .maintenance-message p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #7494ec;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #5a7bd4;
        }

        .estimated-time {
            font-style: italic;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="maintenance-icon">
            <i class="fas fa-tools"></i>
        </div>
        
        <div class="maintenance-message">
            <h2>Checkout System Under Maintenance</h2>
            <p>We're currently performing scheduled maintenance on our checkout and payment processing system to improve your shopping experience. During this time, you won't be able to complete purchases, but you can still browse products and add items to your cart.</p>
            <p class="estimated-time">We expect to be back online by April 11, 2026 at 11:00 PM EST.</p>
        </div>
        
        <a href="cart.php" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Return to Cart
        </a>
        <a href="browse_products.php" class="btn btn-primary">
            <i class="fas fa-shopping-bag"></i> Continue Shopping
        </a>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>