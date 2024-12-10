<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

include('header.php');
include('../config/database.php');

// Fetch holidays
$query = "SELECT * FROM holidays ORDER BY date";
$stmt = $conn->prepare($query);
$stmt->execute();
$holidays = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle add holiday
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_holiday'])) {
    $date = $_POST['date'];
    $description = $_POST['description'];

    $query = "INSERT INTO holidays (date, description) VALUES (:date, :description)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':description', $description);
    $stmt->execute();
    header('Location: holidays.php?success=Holiday added successfully!');
    exit();
}

// Handle delete holiday
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM holidays WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    header('Location: holidays.php?success=Holiday deleted successfully!');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/ritzyy.png" type="image/x-icon">
    <title>Ritzy Attendance Management</title>
    <!-- Add Bootstrap CSS link here -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <h2 class="text-center">Holiday Management</h2><br>
    <?php if (isset($_GET['success'])) { echo "<div class='alert alert-success'>{$_GET['success']}</div>"; } ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" class="form-control" id="description" name="description" placeholder="Holiday Description" required>
        </div>
        <button type="submit" name="add_holiday" class="btn btn custom-btn">Add Holiday</button>
    </form>

    <h3 class="mt-5">Existing Holidays</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($holidays as $holiday) { ?>
            <tr>
                <td><?php echo htmlspecialchars($holiday['date']); ?></td>
                <td><?php echo htmlspecialchars($holiday['description']); ?></td>
                <td>
                    <a href="holidays.php?delete=<?php echo $holiday['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
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