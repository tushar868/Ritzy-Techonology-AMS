<?php

include('../config/database.php');
$data = json_decode(file_get_contents('php://input'), true);
$filter = $data['filter'];
$response = [];

// Fetch data based on filter
if ($filter === 'everyMonth') {
    // Fetch data for every month
} elseif ($filter === 'month') {
    $month = $data['month'];
    // Fetch data for selected month
} elseif ($filter === 'range') {
    $startMonth = $data['startMonth'];
    $endMonth = $data['endMonth'];
    // Fetch data for range
} elseif ($filter === 'days') {
    $startDate = $data['startDate'];
    $endDate = $data['endDate'];
    // Fetch data for days
}

// Return JSON response
echo json_encode($response);
