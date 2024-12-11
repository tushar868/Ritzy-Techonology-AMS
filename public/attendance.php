<?php
ob_start(); // Start output buffering
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

include('header.php');
include('../config/database.php');

// Fetch employees
$query = "SELECT * FROM employees";
$stmt = $conn->prepare($query);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];

    // Check if the date is a holiday
    $holidayQuery = "SELECT COUNT(*) FROM holidays WHERE date = :date";
    $holidayStmt = $conn->prepare($holidayQuery);
    $holidayStmt->bindParam(':date', $date);
    $holidayStmt->execute();
    $isHoliday = $holidayStmt->fetchColumn() > 0;

    // Check if the date is a weekend
    $isWeekend = (date('N', strtotime($date)) >= 6); // 6 = Saturday, 7 = Sunday

    if ($isHoliday || $isWeekend) {
        header('Location: attendance.php?error=Cannot mark attendance on a holiday or weekend!');
        exit();
    }

    foreach ($_POST['attendance'] as $employee_id => $data) {
        $status = $data['status'];

        // Set check_in and check_out to null for Leave or WFH
        $check_in = ($status === 'Leave' || $status === 'WFH') ? null : $data['check_in'];
        $check_out = ($status === 'Leave' || $status === 'WFH') ? null : $data['check_out'];

        // Ensure data is entered only once per employee
        $existingQuery = "SELECT COUNT(*) FROM attendance WHERE employee_id = :employee_id AND date = :date";
        $existingStmt = $conn->prepare($existingQuery);
        $existingStmt->bindParam(':employee_id', $employee_id);
        $existingStmt->bindParam(':date', $date);
        $existingStmt->execute();
        $alreadyExists = $existingStmt->fetchColumn() > 0;

        if (!$alreadyExists) {
            // Insert or update attendance record
            $query = "INSERT INTO attendance (employee_id, date, check_in, check_out, status)
                      VALUES (:employee_id, :date, :check_in, :check_out, :status)
                      ON DUPLICATE KEY UPDATE check_in = :check_in, check_out = :check_out, status = :status";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':check_in', $check_in);
            $stmt->bindParam(':check_out', $check_out);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
        }
    }

    header('Location: attendance.php?success=Attendance saved successfully!');
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/images/ritzyy.png" type="image/x-icon">
    <title>Ritzy Attendance Management</title>
    <style>
        body {
            background-color: #212529;
            color: white;
        }

        table {
            background-color: #F2F2F2;
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            color: black;
        }

        th {
            background-color: #007bff;
            color: black;
        }

        .custom-btn {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .custom-btn:hover {
            background-color: #0056b3;
        }

        @media (max-width: 576px) {
            .custom-btn {
                width: 100%;
                margin-bottom: 10px;
            }

            .form-label {
                display: block;
                text-align: left;
            }

            #date {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Employee Attendance</h2>
    <br>

    <div class="row mb-3">
        <div class="col text-end">
            <a href="view_attendance.php" class="btn custom-btn">All Attendance Records</a>
        </div>
    </div>

    <?php if (isset($_GET['success'])) { echo "<div class='alert alert-success'>{$_GET['success']}</div>"; } ?>
    <?php if (isset($_GET['error'])) { echo "<div class='alert alert-danger'>{$_GET['error']}</div>"; } ?>

    <form method="POST" action="">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($employee['name']); ?></td>
                        <td>
                            <input type="time" name="attendance[<?php echo $employee['id']; ?>][check_in]" class="form-control">
                        </td>
                        <td>
                            <input type="time" name="attendance[<?php echo $employee['id']; ?>][check_out]" class="form-control">
                        </td>
                        <td>
                            <select name="attendance[<?php echo $employee['id']; ?>][status]" class="form-control">
                                <option value="Present">Present</option>
                                <option value="Leave">Leave</option>
                                <option value="WFH">WFH</option>
                            </select>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <button type="submit" class="btn custom-btn mt-3">Save Attendance</button>
        <br>
        <br>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('select[name^="attendance"]').forEach(function (select) {
        select.addEventListener('change', function () {
            const row = this.closest('tr');
            const status = this.value;

            // Clear check-in and check-out fields for "Leave" or "WFH"
            if (status === 'Leave' || status === 'WFH') {
                row.querySelector('input[type="time"][name*="[check_in]"]').value = '';
                row.querySelector('input[type="time"][name*="[check_out]"]').value = '';
            }
        });
    });
});

</script>

</body>
</html>



<?php 
// Include the footer file
include('footer.php');
?>
