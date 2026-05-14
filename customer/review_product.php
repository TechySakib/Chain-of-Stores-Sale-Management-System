<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../includes/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_SESSION['customer_id'];
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO reviews (customer_id, product_id, rating, review_text) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$customer_id, $product_id, $rating, $review_text]);
        
        $success = "Thank you for your review!";
    } catch (PDOException $e) {
        $error = "Error submitting review: " . $e->getMessage();
    }
}

// Fetch products for dropdown
try {
    $stmt = $conn->query("SELECT * FROM products");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Product</title>
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
            max-width: 800px;
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

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        .rating-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .rating-star {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating-star:hover,
        .rating-star.active {
            color: #ffcc00;
        }

        .btn {
            background-color: #7494ec;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: block;
            width: 100%;
            text-align: center;
        }

        .btn:hover {
            background-color: #5a7bd4;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #7494ec;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <h1>Review a Product</h1>

            <?php if (isset($success)): ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php elseif (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="review_product.php">
                <div class="form-group">
                    <label for="product_id">Select Product</label>
                    <select name="product_id" id="product_id" required>
                        <option value="">-- Select a product --</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['product_id']; ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Your Rating</label>
                    <div class="rating-container">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star rating-star" data-rating="<?php echo $i; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="rating" id="rating" value="" required>
                </div>

                <div class="form-group">
                    <label for="review_text">Your Review</label>
                    <textarea name="review_text" id="review_text" required></textarea>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-star"></i> Submit Review
                </button>
            </form>

            <a href="customer_dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script>
        // Handle star rating selection
        document.querySelectorAll('.rating-star').forEach(star => {
            star.addEventListener('click', function () {
                const rating = this.getAttribute('data-rating');
                document.getElementById('rating').value = rating;

                // Update star display
                document.querySelectorAll('.rating-star').forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
