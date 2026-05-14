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

// Initialize variables
$store_name = '';
$location = '';
$manager_id = '';
$errors = [];
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input
    $store_name = trim($_POST['store_name']);
    $location = trim($_POST['location']);
    $manager_id = trim($_POST['manager_id']);

    // Validation
    if (empty($store_name)) {
        $errors[] = "Store name is required.";
    }

    if (empty($location)) {
        $errors[] = "Location is required.";
    }

    if (empty($manager_id)) {
        $errors[] = "Manager selection is required.";
    }

    // Insert into database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO stores (store_name, location, manager_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $store_name, $location, $manager_id);

        if ($stmt->execute()) {
            $success = "Store added successfully!";
            $store_name = $location = ""; // Clear form
        } else {
            $errors[] = "Error adding store. Please try again.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Store</title>
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
            padding-top: 235px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .container {
            max-width: 800px;
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

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: #7494ec;
            outline: none;
            box-shadow: 0 0 0 3px rgba(116, 148, 236, 0.2);
        }

        .btn {
            background-color: #7494ec;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
        }

        .btn:hover {
            background-color: #5a7bd4;
            transform: translateY(-2px);
        }

        .error {
            color: #d9534f;
            background-color: #f2dede;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .success {
            color: #3c763d;
            background-color: #dff0d8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
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
        <h1><i class="fas fa-store"></i> Add New Store</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="store_name">Store Name:</label>
                <input type="text" id="store_name" name="store_name" value="<?php echo htmlspecialchars($store_name); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="manager_id">Store Manager:</label>
                <select name="manager_id" id="manager_id" required>
                    <option value="">Select a manager</option>
                    <?php
                    $managers = $conn->query("SELECT employee_id, name FROM employees WHERE role = 'manager'");
                    while ($manager = $managers->fetch_assoc()) {
                        $selected = ($manager['employee_id'] == $manager_id) ? 'selected' : '';
                        echo "<option value='{$manager['employee_id']}' $selected>{$manager['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Add Store</button>
            </div>
        </form>
        
        <a href="manager_dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>