<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../includes/db.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = $_GET['id'];
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity']++;
    } else {
        $_SESSION['cart'][$product_id] = ['quantity' => 1];
    }
    
    header("Location: cart.php");
    exit();
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header("Location: cart.php");
    exit();
}

// Handle quantity update
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
    }
}

// Calculate total
$total = 0;
$cart_items = [];

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    try {
        $stmt = $conn->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
        $stmt->execute($product_ids);
        $products = $stmt->fetchAll();
        
        foreach ($products as $product) {
            $quantity = $_SESSION['cart'][$product['product_id']]['quantity'];
            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;
            
            $cart_items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
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
        }

        h1 {
            font-size: 36px;
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }

        .cart-header, .cart-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            gap: 20px;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-header {
            font-weight: bold;
            border-bottom: 2px solid #7494ec;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .quantity-input {
            width: 60px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }

        .remove-btn {
            color: #ff6b6b;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
        }

        .cart-total {
            margin-top: 30px;
            text-align: right;
            font-size: 24px;
            font-weight: bold;
        }

        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: #7494ec;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #5a7bd4;
        }

        .btn-secondary {
            background-color: #f1f1f1;
            color: #7494ec;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #e1e1e1;
        }

        .empty-cart {
            text-align: center;
            padding: 50px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Shopping Cart</h1>
        
        <?php if (!empty($cart_items)): ?>
            <form method="post" action="cart.php">
                <div class="cart-header">
                    <div>Product</div>
                    <div>Price</div>
                    <div>Quantity</div>
                    <div>Subtotal</div>
                    <div>Action</div>
                </div>
                
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="product-info">
                        <img src="<?php echo htmlspecialchars($item['product']['image_path'] ?? 'default.webp'); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                            <div><?php echo htmlspecialchars($item['product']['name']); ?></div>
                        </div>
                        <div>$<?php echo number_format($item['product']['price'], 2); ?></div>
                        <div>
                            <input type="number" name="quantity[<?php echo $item['product']['product_id']; ?>]" 
                                   value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                        </div>
                        <div>$<?php echo number_format($item['subtotal'], 2); ?></div>
                        <div>
                            <a href="cart.php?remove=<?php echo $item['product']['product_id']; ?>" class="remove-btn">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="cart-total">
                    Total: $<?php echo number_format($total, 2); ?>
                </div>
                
                <div class="cart-actions">
                    <a href="browse_products.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                    <button type="submit" name="update_cart" class="btn btn-secondary">
                        <i class="fas fa-sync-alt"></i> Update Cart
                    </button>
                    <a href="checkout.php" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> Proceed to Checkout
                    </a>
                </div>
            </form>
        <?php else: ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Browse our products and add some items to your cart</p>
                <a href="browse_products.php" class="btn btn-primary" style="margin-top: 20px;">
                    <i class="fas fa-shopping-bag"></i> Shop Now
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>