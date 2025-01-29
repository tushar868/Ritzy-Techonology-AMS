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
            SELECT date, check_in, check_out, status
            FROM attendance 
            WHERE employee_id = :employee_id 
              AND MONTH(date) = :month 
              AND YEAR(date) = :year
            ORDER BY date;
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();
        $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate total days in the selected month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Calculate weekends for saturday and sunday
        // $weekends = 0;
        // $weekendDays = [];
        // for ($day = 1; $day <= $daysInMonth; $day++) {
        //     $date = strtotime("$year-$month-$day");
        //     $dayOfWeek = date('N', $date); 
        //     if ($dayOfWeek == 6 || $dayOfWeek == 7) { 
        //         $weekends++;
        //         $weekendDays[] = date('Y-m-d', $date);
    }
}


        // Calculate weekends (only Sunday)
        $weekends = 0;
        $weekendDays = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = strtotime("$year-$month-$day");
            $dayOfWeek = date('N', $date); // Get the day of the week (1 = Monday, 7 = Sunday)
            if ($dayOfWeek == 7) { // Only count Sundays as weekends
                $weekends++;
                $weekendDays[] = date('Y-m-d', $date);
            }
        }


        // Fetch holidays
        $query = "
            SELECT date
            FROM holidays
            WHERE MONTH(date) = :month AND YEAR(date) = :year;
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();
        $holidaysData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $holidays = array_map(function($holiday) {
            return $holiday['date'];
        }, $holidaysData);

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

        // Categories for Attendance
        $present = 0;
        $workFromHome = 0;
        $leaves = 0;

        foreach ($attendanceRecords as $record) {
            if ($record['status'] == 'Present') {
                $present++;
            } elseif ($record['status'] == 'WFH') {
                $workFromHome++;
            } elseif ($record['status'] == 'Leave') {
                $leaves++;
            }
        }

        // Additional Attendance Table for Categories
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 10, 'Category', 1);
        $pdf->Cell(50, 10, 'Count', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, 'Present', 1);
        $pdf->Cell(50, 10, $present, 1);
        $pdf->Ln();

        $pdf->Cell(50, 10, 'WFH', 1);
        $pdf->Cell(50, 10, $workFromHome, 1);
        $pdf->Ln();

        $pdf->Cell(50, 10, 'Leaves', 1);
        $pdf->Cell(50, 10, $leaves, 1);
        $pdf->Ln();

        $pdf->Cell(50, 10, 'Holidays', 1);
        $pdf->Cell(50, 10, count($holidays), 1);
        $pdf->Ln();

        $pdf->Cell(50, 10, 'Weekends', 1);
        $pdf->Cell(50, 10, $weekends, 1);
        $pdf->Ln();

        // Total Days
        $pdf->SetFont('Arial', 'B', 12);
        $totalDays = count($attendanceRecords) + count($holidays) + $weekends;
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'Total Days: ' . $totalDays, 0, 1);

        // Main Attendance Table
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 10, 'Date', 1);
        $pdf->Cell(40, 10, 'Check-In', 1);
        $pdf->Cell(40, 10, 'Check-Out', 1);
        $pdf->Cell(40, 10, 'Status', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12);
        
        // Loop through all days of the month, including weekends and holidays
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            $status = '';
            $check_in = '-';
            $check_out = '-';

            // Check if the date exists in attendance data
            $found = false;
            foreach ($attendanceRecords as $record) {
                if ($record['date'] == $date) {
                    $status = $record['status'];
                    $check_in = $record['check_in'] ?: '-';
                    $check_out = $record['check_out'] ?: '-';
                    $found = true;
                    break;
                }
            }

            // If the date was not found, determine if it's a weekend or holiday
            if (!$found) {
                if (in_array($date, $weekendDays)) {
                    $status = 'Weekend';
                } elseif (in_array($date, $holidays)) {
                    $status = 'Holiday';
                } else {
                    $status = '-'; // If not present, leave, or WFH
                }
            }

            $pdf->Cell(40, 10, $date, 1);
            $pdf->Cell(40, 10, $check_in, 1);
            $pdf->Cell(40, 10, $check_out, 1);
            $pdf->Cell(40, 10, $status, 1);
            $pdf->Ln();
        }

        // Output PDF
        $pdf->Output('I', 'Attendance_Report_' . $employee['name'] . '.pdf');
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    die("Invalid or missing Employee ID or Month/Year.");
}
?>
