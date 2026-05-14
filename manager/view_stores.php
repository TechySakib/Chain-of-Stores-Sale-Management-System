<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "chain_of_store");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch stores
$stores = $conn->query("
    SELECT s.store_id, s.store_name, s.location, e.name AS manager_name
    FROM stores s
    LEFT JOIN employees e ON s.manager_id = e.employee_id
    ORDER BY s.store_name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Stores</title>
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
            padding-top: 181px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        h1 {
            color: #7494ec;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .add-btn {
            display: inline-block;
            background-color: #7494ec;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .add-btn:hover {
            background-color: #5a7bd4;
            transform: translateY(-2px);
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

        .action-btn {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            margin-right: 5px;
            transition: all 0.2s;
        }

        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }

        .edit-btn:hover {
            background-color: #3e8e41;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
        }

        .delete-btn:hover {
            background-color: #d32f2f;
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
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-store"></i> Stores List</h1>
        
        <a href="add_store.php" class="add-btn"><i class="fas fa-plus"></i> Add New Store</a>
        
        <table>
            <thead>
                <tr>
                    <th>Store Name</th>
                    <th>Location</th>
                    <th>Manager</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($store = $stores->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($store['store_name']); ?></td>
                        <td><?php echo htmlspecialchars($store['location']); ?></td>
                        <td><?php echo $store['manager_name'] ? htmlspecialchars($store['manager_name']) : 'Not assigned'; ?></td>
                        <td>
                            <a href="edit_store.php?id=<?php echo $store['store_id']; ?>" class="action-btn edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="delete_store.php?id=<?php echo $store['store_id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this store?');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <a href="manager_dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>