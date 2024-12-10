<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}
include('../config/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input to prevent XSS
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $position = htmlspecialchars(trim($_POST['position']));
    $date_of_joining = htmlspecialchars(trim($_POST['date_of_joining']));
    $status = htmlspecialchars(trim($_POST['status']));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // If email is invalid, redirect with an error
        header('Location: employees.php?error=Invalid email address');
        exit();
    }

    // Prepare the SQL query to insert employee data
    $query = "INSERT INTO employees (name, email, position, date_of_joining, status) 
              VALUES (:name, :email, :position, :date_of_joining, :status)";
    $stmt = $conn->prepare($query);
    
    // Bind parameters to avoid SQL injection
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':position', $position);
    $stmt->bindParam(':date_of_joining', $date_of_joining);
    $stmt->bindParam(':status', $status);

    // Execute the query
    if ($stmt->execute()) {
        // If successful, redirect to employee list with success message
        header('Location: employees.php?success=Employee added successfully');
    } else {
        // If query fails, redirect with error message
        header('Location: employees.php?error=Failed to add employee');
    }
    exit();
}
?>
