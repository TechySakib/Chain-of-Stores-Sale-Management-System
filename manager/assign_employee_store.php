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
$employee_id = '';
$store_id = '';
$errors = [];
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input
    $employee_id = trim($_POST['employee_id']);
    $store_id = trim($_POST['store_id']);

    // Validation
    if (empty($employee_id)) {
        $errors[] = "Employee selection is required.";
    }

    if (empty($store_id)) {
        $errors[] = "Store selection is required.";
    }

    // Check if assignment already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT assignment_id FROM employee_store_assignments WHERE employee_id = ? AND store_id = ?");
        $stmt->bind_param("ii", $employee_id, $store_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "This employee is already assigned to this store.";
        }
        $stmt->close();
    }

    // Insert into database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO employee_store_assignments (employee_id, store_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $employee_id, $store_id);

        if ($stmt->execute()) {
            $success = "Employee assigned to store successfully!";
            $employee_id = $store_id = ""; // Clear form
        } else {
            $errors[] = "Error assigning employee to store. Please try again.";
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
    <title>Assign Employee to Store</title>
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
            padding-top: 169px;
            padding-left: 20px;
            padding-right: 20px;

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding-top: 140px;
            padding-left: 40px;
            padding-right: 40px;
            padding-bottom: 100px;
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

        select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

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
        <h1><i class="fas fa-user-tag"></i> Assign Employee to Store</h1>
        
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
                <label for="employee_id">Employee:</label>
                <select name="employee_id" id="employee_id" required>
                    <option value="">Select an employee</option>
                    <?php
                    $employees = $conn->query("SELECT employee_id, name FROM employees");
                    while ($employee = $employees->fetch_assoc()) {
                        $selected = ($employee['employee_id'] == $employee_id) ? 'selected' : '';
                        echo "<option value='{$employee['employee_id']}' $selected>{$employee['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="store_id">Store:</label>
                <select name="store_id" id="store_id" required>
                    <option value="">Select a store</option>
                    <?php
                    $stores = $conn->query("SELECT store_id, store_name FROM stores");
                    while ($store = $stores->fetch_assoc()) {
                        $selected = ($store['store_id'] == $store_id) ? 'selected' : '';
                        echo "<option value='{$store['store_id']}' $selected>{$store['store_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Assign Employee</button>
            </div>
        </form>
        
        <a href="manager_dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>