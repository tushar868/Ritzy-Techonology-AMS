<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

// Include the header file
include('header.php');

// Include the database connection
include('../config/database.php');

// Check if there's a success message in the URL
if (isset($_GET['success'])) {
    echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['success']) . "</div>";
}

// Fetch employees data
$query = "SELECT * FROM employees";
$stmt = $conn->prepare($query);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/ritzyy.png" type="image/x-icon">
    <title>Ritzy Attendance Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #212529;
            color: white;
        }

        table {
            background-color: #F2F2F2;
        }

        th, td {
            color: black;
            vertical-align: middle;
        }

        .modal-content {
            background-color: #212529;
            color: white;
        }

        .btn.custom-btn {
            background-color: #D3A645;
            color: black;
        }

        .btn.custom-btn:hover {
            background-color: #b5893a;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Employee List</h2>
        
        <!-- Row for the "Add Employee" button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="m-0"></p>
            <button class="btn btn custom-btn" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">Add Employee</button>
        </div>
        
        <!-- Table to display all employees -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th>Date of Joining</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td><?= htmlspecialchars($employee['name']); ?></td>
                        <td><?= htmlspecialchars($employee['email']); ?></td>
                        <td><?= htmlspecialchars($employee['position']); ?></td>
                        <td><?= htmlspecialchars($employee['date_of_joining']); ?></td>
                        <td><?= htmlspecialchars($employee['status']); ?></td>
                        <td>
                            <div class="d-flex flex-wrap justify-content-center gap-1">
                                <a href="edit_employee.php?id=<?= $employee['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_employee.php?id=<?= $employee['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</a>
                                <form action="generate_report.php" method="get">
                                    <label for="month_year_<?php echo $employee['id']; ?>"></label>
                                    <input type="month" id="month_year_<?php echo $employee['id']; ?>" name="month_year" required>
                                    <input type="hidden" name="id" value="<?php echo $employee['id']; ?>"> <!-- Employee ID -->
                                    <button class="btn btn-success btn-sm" type="submit">Generate Report</button>
                                </form>                                           
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for adding a new employee -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true" >
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header" style="background-color: #212529; color: white;">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Add New Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #212529; color: white;">
                    <!-- Form to add a new employee -->
                    <form method="POST" action="add_employee.php">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" class="form-control" id="position" name="position" required>
                        </div>
                        <div class="mb-3">
                            <label for="date_of_joining" class="form-label">Date of Joining</label>
                            <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn custom-btn w-100">Add Employee</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php 
// Include the footer file
include('footer.php');
?>
