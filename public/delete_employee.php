<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

include('header.php');
include('../config/database.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM employees WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        header("Location: employees.php?success=Employee deleted successfully!");
    } else {
        header("Location: employees.php?error=Failed to delete employee.");
    }
} else {
    header("Location: employees.php");
}
?>
