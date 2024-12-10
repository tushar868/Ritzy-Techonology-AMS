<?php
require('../fpdf/fpdf.php'); // Include FPDF library
require('../config/database.php'); // Include database connection

if (isset($_GET['id'])) {
    $employee_id = $_GET['id'];

    try {
        // Fetch employee details
        $query = "SELECT name, position FROM employees WHERE id = :employee_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
        $stmt->execute();
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            die("Employee not found.");
        }

        // Fetch attendance data
        $query = "
            SELECT 
                SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present,
                SUM(CASE WHEN status = 'WFH' THEN 1 ELSE 0 END) AS wfh,
                SUM(CASE WHEN status = 'Leave' THEN 1 ELSE 0 END) AS leave_days
            FROM attendance 
            WHERE employee_id = :employee_id 
              AND MONTH(date) = MONTH(CURRENT_DATE()) 
              AND YEAR(date) = YEAR(CURRENT_DATE());
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
        $stmt->execute();
        $attendanceData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$attendanceData) {
            die("No attendance data found for this employee.");
        }

        // Generate PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Title
        $pdf->Cell(0, 10, 'Attendance Report', 0, 1, 'C');
        $pdf->Ln(10);

        // Employee Details
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Employee Name: ' . $employee['name'], 0, 1);
        $pdf->Cell(0, 10, 'Position: ' . $employee['position'], 0, 1);
        $pdf->Cell(0, 10, 'Month: ' . date('F Y'), 0, 1);
        $pdf->Ln(10);

        // Attendance Table Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(60, 10, 'Status', 1);
        $pdf->Cell(60, 10, 'Days', 1);
        $pdf->Ln();

        // Attendance Table Data
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(60, 10, 'Present', 1);
        $pdf->Cell(60, 10, $attendanceData['present'], 1);
        $pdf->Ln();

        $pdf->Cell(60, 10, 'Work From Home', 1);
        $pdf->Cell(60, 10, $attendanceData['wfh'], 1);
        $pdf->Ln();

        $pdf->Cell(60, 10, 'Leave', 1);
        $pdf->Cell(60, 10, $attendanceData['leave_days'], 1);
        $pdf->Ln();

        // Total Days
        $pdf->SetFont('Arial', 'B', 12);
        $totalDays = $attendanceData['present'] + $attendanceData['wfh'] + $attendanceData['leave_days'];
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'Total Days: ' . $totalDays, 0, 1);

        // Output PDF
        $pdf->Output('I', 'Attendance_Report_' . $employee['name'] . '.pdf');
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    die("Employee ID not provided.");
}
