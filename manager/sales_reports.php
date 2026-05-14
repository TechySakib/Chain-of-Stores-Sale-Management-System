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

// Fetch sales reports
$reports = $conn->query("
    SELECT sr.report_id, s.store_name, sr.report_month, sr.total_sales, sr.total_revenue 
    FROM sales_reports sr
    JOIN stores s ON sr.store_id = s.store_id
    ORDER BY sr.report_month DESC, s.store_name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports</title>
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
            padding-top: 20px;
            padding-left: 20px;
            padding-right: 20px;
            display: flex;
            flex-direction: column;
        }

        .container {
            min-width: 1000px;
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            flex: 1;
            margin-bottom: 40px; /* Added space between container and footer */
        }

        h1 {
            color: #7494ec;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #7494ec;
            color: white;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
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

        .currency {
            text-align: right;
        }

        .month {
            white-space: nowrap;
        }

        /* Footer spacing */
        body > footer {
            margin-top: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-chart-line"></i> Monthly Sales Reports</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Store</th>
                    <th class="month">Month</th>
                    <th class="currency">Total Sales</th>
                    <th class="currency">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($report = $reports->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['store_name']); ?></td>
                        <td class="month"><?php echo date('F Y', strtotime($report['report_month'])); ?></td>
                        <td class="currency"><?php echo number_format($report['total_sales']); ?></td>
                        <td class="currency">$<?php echo number_format($report['total_revenue'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <a href="manager_dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>