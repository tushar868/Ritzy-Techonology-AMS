<?php
require('../fpdf/fpdf.php'); // Include FPDF library
require('../config/database.php'); // Include database connection

if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['month_year'])) {
    $employee_id = intval($_GET['id']); // Sanitize input
    $month_year = $_GET['month_year']; // Get selected month and year

    // Extract the year and month from the selected value (YYYY-MM format)
    $year = substr($month_year, 0, 4);
    $month = substr($month_year, 5, 2);

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

        // Fetch attendance data for the selected month and year
        $query = "
            SELECT 
                SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present,
                SUM(CASE WHEN status = 'WFH' THEN 1 ELSE 0 END) AS wfh,
                SUM(CASE WHEN status = 'Leave' THEN 1 ELSE 0 END) AS leave_days
            FROM attendance 
            WHERE employee_id = :employee_id 
              AND MONTH(date) = :month 
              AND YEAR(date) = :year;
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();
        $attendanceData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calculate total days in the selected month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Calculate weekends
        $weekends = 0;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = strtotime("$year-$month-$day");
            $dayOfWeek = date('N', $date);
            if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                $weekends++;
            }
        }

        // Fetch holidays
        $query = "
            SELECT COUNT(*) AS holidays
            FROM holidays
            WHERE MONTH(date) = :month AND YEAR(date) = :year;
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();
        $holidaysData = $stmt->fetch(PDO::FETCH_ASSOC);
        $holidays = $holidaysData['holidays'];

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
        $pdf->Cell(0, 10, 'Month: ' . date('F Y', strtotime("$year-$month-01")), 0, 1);
        $pdf->Ln(10);

        // Attendance Table
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(60, 10, 'Status', 1);
        $pdf->Cell(60, 10, 'Days', 1);
        $pdf->Ln();

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

        $pdf->Cell(60, 10, 'Holidays', 1);
        $pdf->Cell(60, 10, $holidays, 1);
        $pdf->Ln();

        $pdf->Cell(60, 10, 'Weekends', 1);
        $pdf->Cell(60, 10, $weekends, 1);
        $pdf->Ln();

        // Total Days
        $pdf->SetFont('Arial', 'B', 12);
        $totalDays = $attendanceData['present'] + $attendanceData['wfh'] + $attendanceData['leave_days'] + $holidays + $weekends;
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'Total Days: ' . $totalDays, 0, 1);

        // Output PDF
        $pdf->Output('I', 'Attendance_Report_' . $employee['name'] . '.pdf');
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    die("Invalid or missing Employee ID or Month/Year.");
}
