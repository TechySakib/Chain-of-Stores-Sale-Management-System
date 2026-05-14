<?php
session_start();
include '../includes/db.php';

try {
    // Get employee's assigned store
    $stmt = $conn->prepare("
        SELECT s.store_name, s.location 
        FROM employee_store_assignments esa
        JOIN stores s ON esa.store_id = s.store_id 
        WHERE esa.employee_id = :employee_id
    ");
    $stmt->bindParam(':employee_id', $_SESSION['employee_id']);
    $stmt->execute();
    $store = $stmt->fetch();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Store</title>
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
            font-size: 36px;
            text-align: center;
            font-weight: bold;
            opacity: 70%;
            margin-bottom: 30px;
        }

        .store-info {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .store-info p {
            margin-bottom: 10px;
            font-size: 16px;
            color: #555;
        }

        .store-info strong {
            color: #333;
            font-weight: 600;
        }

        .no-store {
            text-align: center;
            color: #6c757d;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            background-color: #7494ec;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
        }

        .back-link:hover {
            background-color: #5a7bd4;
            transform: translateY(-2px);
        }

        .welcome-message {
            font-size: 18px;
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="left-panel">
            <div class="left-panel-content">
                <h2>Your Store</h2>
                <p>View your assigned store details</p>
            </div>
        </div>

        <div class="right-panel">
            <h1><i class="fas fa-store"></i> Store Details</h1>
            <p class="welcome-message">Hello, <?php echo isset($_SESSION['employee_name']) ? htmlspecialchars($_SESSION['employee_name']) : 'Employee'; ?>!</p>
            
            <?php if ($store): ?>
                <div class="store-info">
                    <p><strong>Store Name:</strong> <?php echo htmlspecialchars($store['store_name']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($store['location']); ?></p>
                </div>
            <?php else: ?>
                <div class="no-store">
                    <p>No store assigned to you yet.</p>
                </div>
            <?php endif; ?>
            
            <a href="employee_dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>