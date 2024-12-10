<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php'); // Redirect to login if not logged in
    exit();
}


include('header.php');
include('../config/database.php');


$admin_name = "Admin"; 

// Fetch total employees
$query = "SELECT COUNT(*) AS total_employees FROM employees";
$stmt = $conn->prepare($query);
$stmt->execute();
$totalEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['total_employees'];

// Fetch present employees
$query = "SELECT COUNT(*) AS present_employees FROM attendance WHERE status = 'Present' AND date = CURDATE()";
$stmt = $conn->prepare($query);
$stmt->execute();
$presentEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['present_employees'];

// Fetch WFH employees
$query = "SELECT COUNT(*) AS wfh_employees FROM attendance WHERE status = 'WFH' AND date = CURDATE()";
$stmt = $conn->prepare($query);
$stmt->execute();
$wfhEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['wfh_employees'];

// Fetch leaves today
$query = "SELECT COUNT(*) AS leaves_today FROM attendance WHERE status = 'Leave' AND date = CURDATE()";
$stmt = $conn->prepare($query);
$stmt->execute();
$leavesToday = $stmt->fetch(PDO::FETCH_ASSOC)['leaves_today'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/ritzyy.png" type="image/x-icon">
    <title>Ritzy Attendance Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background-color: #212529; color: white;">
<div class="container mt-5">
    <!-- Dashboard Cards -->
    <div class="row mt-4">
        <!-- Total Employees Card -->
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Employees</h5>
                    <p class="card-text"><?= $totalEmployees ?></p>
                </div>
            </div>
        </div>

        <!-- Present Employees Card -->
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Present Employees</h5>
                    <p class="card-text"><?= $presentEmployees ?></p>
                </div>
            </div>
        </div>

        <!-- WFH Employees Card -->
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">WFH Employees</h5>
                    <p class="card-text"><?= $wfhEmployees ?></p>
                </div>
            </div>
        </div>

        <!-- Leaves Today Card -->
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Leaves Today</h5>
                    <p class="card-text"><?= $leavesToday ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
    <div class="row mb-3">
        <div class="col-md-12 text-center">
            <h5 class="card-title">Filter Statistics</h5><br>
            <div class="btn-group">
                <button id="filter-month-selection" class="btn custom-btn">Select Month</button>
                <button id="filter-month-range" class="btn custom-btn">Select Range</button>
                <button id="filter-days-selection" class="btn custom-btn">Select Days</button>
            </div>
        </div>
    </div>

    <div id="filter-options" class="row mb-3" style="display: none;">
        <!-- Filter Options (hidden initially, displayed dynamically) -->
        <div id="month-selection" class="col-md-12 text-center">
            <label for="month-select">Select Month:</label>
            <input type="month" id="month-select" class="form-control" style="display: inline-block; width: auto;" />
        </div>
        <div id="month-range" class="col-md-12 text-center" style="display: none;">
            <label for="start-month">Start Month:</label>
            <input type="month" id="start-month" class="form-control d-inline" style="width: auto;">
            <label for="end-month">End Month:</label>
            <input type="month" id="end-month" class="form-control d-inline" style="width: auto;">
        </div>
        <div id="days-selection" class="col-md-12 text-center" style="display: none;">
            <label for="start-date">Start Date:</label>
            <input type="date" id="start-date" class="form-control d-inline" style="width: auto;">
            <label for="end-date">End Date:</label>
            <input type="date" id="end-date" class="form-control d-inline" style="width: auto;">
        </div>
    </div>
</div>

    
    <!-- First Row: Pie Chart and Bar Chart -->
    <div class="row mt-4 justify-content-center">
        <!-- Pie Chart -->
        <div class="col-md-4 col-sm-12 mb-1 text-center">
            <div class="chart-box">
                <canvas id="employeePieChart" width="200" height="200"></canvas>
            </div>
        </div>

        <!-- Bar Chart (Pillar Chart) -->
        <div class="col-md-4 col-sm-12 mb-1 text-center">
            <div class="chart-box">
                <canvas id="employeeBarChart" width="200" height="200"></canvas>
            </div>
        </div>
    </div>


    <!-- Second Row: Attendance Trend, WFH Trend, and Leave Trend Mountain Graphs -->
    <div class="row mt-5 mb-5">
        <!-- Attendance Trend Mountain Graph -->
        <div class="col-md-4 mb-1">
            <div class="chart-box">
                <canvas id="attendanceMountainGraph" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- WFH Trend Mountain Graph -->
        <div class="col-md-4 mb-1">
            <div class="chart-box">
                <canvas id="wfhMountainGraph" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Leave Trend Mountain Graph -->
        <div class="col-md-4 mb-1">
            <div class="chart-box">
                <canvas id="leaveMountainGraph" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
<script>

document.addEventListener('DOMContentLoaded', function () {
    // Filter buttons
    const filterButtons = document.querySelectorAll('.btn-group button');
    const filterOptions = document.getElementById('filter-options');
    const monthSelection = document.getElementById('month-selection');
    const monthRange = document.getElementById('month-range');
    const daysSelection = document.getElementById('days-selection');

    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            filterOptions.style.display = 'block';
            monthSelection.style.display = 'none';
            monthRange.style.display = 'none';
            daysSelection.style.display = 'none';

            switch (this.id) {
                case 'filter-every-month':
                    updateCharts({ filter: 'everyMonth' });
                    filterOptions.style.display = 'none'; // No input required
                    break;
                case 'filter-month-selection':
                    monthSelection.style.display = 'block';
                    break;
                case 'filter-month-range':
                    monthRange.style.display = 'block';
                    break;
                case 'filter-days-selection':
                    daysSelection.style.display = 'block';
                    break;
            }
        });
    });

    // Fetch and update charts on user input
    document.getElementById('month-select').addEventListener('change', function () {
        const month = this.value;
        updateCharts({ filter: 'month', month });
    });

    document.getElementById('start-month').addEventListener('change', function () {
        const startMonth = document.getElementById('start-month').value;
        const endMonth = document.getElementById('end-month').value;
        if (startMonth && endMonth) {
            updateCharts({ filter: 'range', startMonth, endMonth });
        }
    });

    document.getElementById('start-date').addEventListener('change', function () {
        const startDate = document.getElementById('start-date').value;
        const endDate = document.getElementById('end-date').value;
        if (startDate && endDate) {
            updateCharts({ filter: 'days', startDate, endDate });
        }
    });

    // Function to update charts
    function updateCharts(filterParams) {
        // Send AJAX request to fetch data based on filters
        fetch('fetch_filtered_data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(filterParams)
        })
            .then(response => response.json())
            .then(data => {
                // Update charts with new data
                employeePieChart.data.datasets[0].data = data.pieData;
                employeePieChart.update();

                employeeBarChart.data.datasets[0].data = data.barData;
                employeeBarChart.update();

                attendanceMountainGraph.data.datasets[0].data = data.attendanceTrend;
                attendanceMountainGraph.update();

                wfhMountainGraph.data.datasets[0].data = data.wfhTrend;
                wfhMountainGraph.update();

                leaveMountainGraph.data.datasets[0].data = data.leaveTrend;
                leaveMountainGraph.update();
            })
            .catch(error => console.error('Error updating charts:', error));
    }
});

    // Pie Chart
    var ctxPie = document.getElementById('employeePieChart').getContext('2d');
    var employeePieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: ['Present Employees', 'WFH Employees', 'Leaves Today'],
            datasets: [{
                data: [<?= $presentEmployees ?>, <?= $wfhEmployees ?>, <?= $leavesToday ?>],
                backgroundColor: ['#3F8755', '#F2C010', '#DC3545'], // Green, Yellow, Red
                borderColor: ['#3F8755', '#F2C010', '#DC3545'], // Green, Yellow, Red
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });

    // Bar Chart (Pillar Chart)
    var ctxBar = document.getElementById('employeeBarChart').getContext('2d');
    var employeeBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Total Employees', 'Present Employees', 'WFH Employees', 'Leaves Today'],
            datasets: [{
                label: 'Employee Statistics',
                data: [<?= $totalEmployees ?>, <?= $presentEmployees ?>, <?= $wfhEmployees ?>, <?= $leavesToday ?>],
                backgroundColor: ['#3C6EFD', '#3F8755', '#F2C010', '#DC3545'], // Blue, Green, Yellow, Red
                borderColor: ['#3C6EFD', '#3F8755', '#F2C010', '#DC3545'], // Blue, Green, Yellow, Red
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });

    // Mountain Graph (Attendance Trend)
    var ctxAttendance = document.getElementById('attendanceMountainGraph').getContext('2d');
    var attendanceMountainGraph = new Chart(ctxAttendance, {
        type: 'line',
        data: {
            labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5'],
            datasets: [{
                label: 'Attendance Trend',
                data: [100, 120, 140, 110, 130],  // Sample data, replace with actual attendance data
                borderColor: '#3F8755', // Light Blue
                borderWidth: 2,
                fill: true,
                backgroundColor: 'rgba(63, 135, 85, 0.2)', // Light Blue with transparency
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });

    // Mountain Graph (WFH Trend)
    var ctxWfh = document.getElementById('wfhMountainGraph').getContext('2d');
    var wfhMountainGraph = new Chart(ctxWfh, {
        type: 'line',
        data: {
            labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5'],
            datasets: [{
                label: 'WFH Trend',
                data: [10, 15, 20, 25, 30],  // Sample data, replace with actual WFH data
                borderColor: '#F2C010', // Light Yellow
                borderWidth: 2,
                fill: true,
                backgroundColor: 'rgba(242, 192, 16, 0.2)', // Light Yellow with transparency
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });

    // Mountain Graph (Leave Trend)
    var ctxLeave = document.getElementById('leaveMountainGraph').getContext('2d');
    var leaveMountainGraph = new Chart(ctxLeave, {
        type: 'line',
        data: {
            labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5'],
            datasets: [{
                label: 'Leave Trend',
                data: [5, 7, 3, 4, 6],  // Sample data, replace with actual leave data
                borderColor: '#DC3545', // Light Red
                borderWidth: 2,
                fill: true,
                backgroundColor: 'rgba(240, 128, 128, 0.2)', // Light Red with transparency
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });
</script>


<style>
    .chart-box {
        border: 2px solid #ddd;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>

<?php include('footer.php'); ?>
