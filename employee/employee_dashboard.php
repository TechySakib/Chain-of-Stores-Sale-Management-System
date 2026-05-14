<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
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

        .dashboard-menu {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .dashboard-btn {
            background-color: #7494ec;
            color: #fff;
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

        .dashboard-btn:hover {
            background-color: #5a7bd4;
            transform: translateY(-2px);
        }

        .logout-btn {
            margin-top: 30px;
            background-color: #f1f1f1;
            color: #7494ec;
        }

        .logout-btn:hover {
            background-color: #e1e1e1;
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
                <h2>Welcome Back!</h2>
                <p>Manage your work efficiently</p>
            </div>
        </div>

        <div class="right-panel">
            <h1>Employee Dashboard</h1>
            <p class="welcome-message">Hello, <?php echo isset($_SESSION['employee_name']) ? htmlspecialchars($_SESSION['employee_name']) : 'Employee'; ?>!</p>
            
            <div class="dashboard-menu">
                <a href="view_assigned_store.php" class="dashboard-btn">
                    <i class="fas fa-store"></i> View Assigned Stores
                </a>
                <a href="../manager/view_products.php" class="dashboard-btn">
                    <i class="fas fa-boxes"></i> View Products
                </a>
                <a href="record_sales.php" class="dashboard-btn">
                    <i class="fas fa-cash-register"></i> Record Sales
                </a>
                <a href="view_sales.php" class="dashboard-btn">
                    <i class="fas fa-history"></i> View Sales History
                </a>
                <a href="../logout.php" class="dashboard-btn logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>