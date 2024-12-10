<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

include('header.php');
include('../config/database.php');

// Initialize the base query
$query = "SELECT a.date, a.check_in, a.check_out, a.status, e.name
          FROM attendance a
          JOIN employees e ON a.employee_id = e.id";

// Apply filter by Date
if (isset($_GET['filter_date']) && !empty($_GET['filter_date'])) {
    $filterDate = $_GET['filter_date'];
    $query .= " WHERE a.date = :filter_date";
}

// Apply filter by Employee
if (isset($_GET['employee_id']) && !empty($_GET['employee_id']) && $_GET['employee_id'] != "") {
    $employeeId = $_GET['employee_id'];
    if (strpos($query, 'WHERE') !== false) {
        $query .= " AND a.employee_id = :employee_id";
    } else {
        $query .= " WHERE a.employee_id = :employee_id";
    }
}

$query .= " ORDER BY a.date DESC";

$stmt = $conn->prepare($query);

// Bind parameters
if (isset($filterDate)) {
    $stmt->bindParam(':filter_date', $filterDate);
}
if (isset($employeeId) && !empty($employeeId)) {
    $stmt->bindParam(':employee_id', $employeeId);
}

$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/images/ritzyy.png" type="image/x-icon">
    <title>Ritzy Attendance Management</title>
</head>

<style>
    body {
        background-color: #212529; 
        color: white;  
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
        background-color: #F2F2F2; 
    }

    th, td {
        color: black; 
        border: 2px solid Black; 
        padding: 8px;
        
    }

    tr {
        background-color: #F2F2F2;  
    }
</style>

<body>
<div class="container mt-5">
    <h2 class="text-center">Attendance Records</h2><br><br>

    <!-- Filters Form -->
    <form method="GET" action="">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="filter_date" class="form-label">Filter by Date</label>
                <input type="date" class="form-control" id="filter_date" name="filter_date" value="<?php echo isset($_GET['filter_date']) ? $_GET['filter_date'] : ''; ?>">
            </div>

            <div class="col-md-4">
                <label for="employee_id" class="form-label">Filter by Employee</label>
                <select class="form-select" name="employee_id" id="employee_id">
                    <option value="">All Employees</option> <!-- Option for all employees -->
                    <?php
                    // Fetch employees for dropdown
                    $employeeQuery = "SELECT id, name FROM employees";
                    $employeeStmt = $conn->prepare($employeeQuery);
                    $employeeStmt->execute();
                    $employees = $employeeStmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($employees as $employee) {
                        $selected = (isset($_GET['employee_id']) && $_GET['employee_id'] == $employee['id']) ? 'selected' : '';
                        echo "<option value='{$employee['id']}' {$selected}>{$employee['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-4 text-end">
                <button type="submit" class="btn btn custom-btn mt-4">Apply Filters</button>
                <!-- Reset Button - clears all filters -->
                <a href="view_attendance.php" class="btn btn-secondary mt-4">Reset</a>
            </div>
        </div>
    </form>

    <!-- Attendance Records Table -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Employee</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $record) { ?>
            <tr>
                <td><?php echo $record['date']; ?></td>
                <td><?php echo $record['name']; ?></td>
                <td><?php echo $record['check_in'] ?: '-'; ?></td>
                <td><?php echo $record['check_out'] ?: '-'; ?></td>
                <td><?php echo $record['status']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php 
// Include the footer file
include('footer.php');
?>