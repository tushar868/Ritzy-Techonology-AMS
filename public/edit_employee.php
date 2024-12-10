<?php
session_start();

// Start output buffering to avoid header errors
ob_start();

// Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

include('header.php');
include('../config/database.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch employee details
    $query = "SELECT * FROM employees WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        die("Employee not found.");
    }
} else {
    header("Location: employees.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $date_of_joining = $_POST['date_of_joining'];
    $status = $_POST['status'];

    $query = "UPDATE employees SET name = :name, email = :email, position = :position, date_of_joining = :date_of_joining, status = :status WHERE id = :id";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':position', $position);
    $stmt->bindParam(':date_of_joining', $date_of_joining);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        // Set the success message and redirect to employees.php
        $_SESSION['success_message'] = "Employee updated successfully!";
        header("Location: employees.php");
        exit();
    } else {
        $errorMessage = "Failed to update employee.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/ritzyy.png" type="image/x-icon">
    <title>Ritzy Attendance Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #212529;
            color: white;
        }
        .alert {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Employee</h2><br>
        
        <!-- Display success or error message if set -->
        <?php if (isset($successMessage)) { ?>
            <div class="alert alert-success text-center" style="max-width: 500px; margin: 0 auto;">
                <?php echo $successMessage; ?>
            </div>
        <?php } elseif (isset($errorMessage)) { ?>
            <div class="alert alert-danger text-center" style="max-width: 500px; margin: 0 auto;">
                <?php echo $errorMessage; ?>
            </div>
        <?php } ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $employee['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $employee['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="position" class="form-label">Position</label>
                <input type="text" class="form-control" id="position" name="position" value="<?php echo $employee['position']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="date_of_joining" class="form-label">Date of Joining</label>
                <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" value="<?php echo $employee['date_of_joining']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="Active" <?php echo $employee['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $employee['status'] == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn custom-btn">Update Employee</button>
            <br><br>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php 
// Include the footer file
include('footer.php');

// End output buffering
ob_end_flush();
?>
