<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../includes/db.php';

try {
    $stmt = $conn->prepare("
        SELECT p.*, s.store_name 
        FROM products p
        LEFT JOIN stores s ON p.store_id = s.store_id
        ORDER BY p.product_id DESC
    ");
    $stmt->execute();
    $products = $stmt->fetchAll();
    
    foreach ($products as &$product) {
        $product['image_path'] = !empty($product['image_path']) 
            ? $product['image_path'] 
            : 'https://via.placeholder.com/300x300?text=No+Image';
        $product['price'] = number_format((float)$product['price'], 2);
    }
    unset($product);
    
    if (empty($products)) {
        $no_products = "<p class='no-products'>No products available at the moment.</p>";
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
    <title>Browse Products</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4a6bff;
            --text: #333333;
            --text-light: #666666;
            --border: #e0e0e0;
            --bg: #f9f9f9;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg);
            padding: 30px 20px 0px 20px;
            color: var(--text);
        }

        .container {
            max-width: 1500px;
            margin: 0 auto;
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border);
        }

        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .product-image-container {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            padding: 20px;
            border-bottom: 1px solid var(--border);
        }

        .product-image {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        .product-info {
            padding: 16px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-name {
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .product-store {
            font-size: 12px;
            color: var(--text-light);
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
            margin: 8px 0 12px;
        }

        .product-description {
            font-size: 13px;
            color: var(--text-light);
            line-height: 1.4;
            margin-bottom: 16px;
            flex-grow: 1;
        }

        .product-actions {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
        }

        .btn {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #3a5aef;
        }

        .btn-secondary {
            background: none;
            color: var(--primary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: #f5f5f5;
        }

        /* Hover Popup Styles - Fixed Version */
        .details-container {
            position: relative;
            display: inline-block;
        }

        .details-popup {
            position: absolute;
            bottom: calc(100% + 10px);
            left: 0;
            width: 280px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            transform: translateY(5px);
            border: 1px solid var(--border);
        }

        .details-container:hover .details-popup {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .popup-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text);
        }

        .popup-specs {
            font-size: 13px;
            color: var(--text-light);
            margin-bottom: 10px;
        }

        .popup-specs div {
            margin-bottom: 5px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 30px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }

        .no-products {
            text-align: center;
            padding: 40px;
            color: var(--text-light);
        }

        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }
            
            .product-image-container {
                height: 160px;
                padding: 15px;
            }
            
            .details-popup {
                width: 240px;
            }
        }

        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .product-name {
                font-size: 14px;
            }
            
            .btn {
                padding: 7px 10px;
                font-size: 12px;
            }
            
            .details-popup {
                width: 200px;
                left: auto;
                right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Browse Products</h1>
        
        <?php echo $no_products ?? ''; ?>
        
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             class="product-image"
                             loading="lazy">
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <?php if (!empty($product['store_name'])): ?>
                            <div class="product-store"><?php echo htmlspecialchars($product['store_name']); ?></div>
                        <?php endif; ?>
                        <div class="product-price">$<?php echo $product['price']; ?></div>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="product-actions">
                            <div class="details-container">
                                <a href="?id=<?php echo $product['product_id']; ?>" class="btn btn-secondary">
                                    <i class="far fa-eye"></i> Details
                                </a>
                                <div class="details-popup">
                                    <div class="popup-title"><?php echo htmlspecialchars($product['name']); ?></div>
                                    <div class="popup-specs">
                                        <div><strong>Store:</strong> <?php echo htmlspecialchars($product['store_name'] ?? 'All Stores'); ?></div>
                                        <div><strong>Price:</strong> $<?php echo $product['price']; ?></div>
                                        <div><strong>In Stock:</strong> <?php echo htmlspecialchars($product['stock_quantity'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div><?php echo htmlspecialchars($product['description']); ?></div>
                                </div>
                            </div>
                            <a href="cart.php?action=add&id=<?php echo $product['product_id']; ?>" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <a href="customer_dashboard.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>