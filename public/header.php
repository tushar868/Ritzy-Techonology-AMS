<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/ritzyy.png" type="image/x-icon">
    <title>Ritzy Attendance Management</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Additional Styling for Mobile Screens */
        @media (max-width: 576px) {
            h3 {
                font-size: 1.25rem;
            }

            .nav-link {
                font-size: 0.9rem;
            }

            .btn {
                font-size: 0.8rem;
                padding: 0.5rem 0.8rem;
            }

            #current-date-time {
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <header class="py-3" style="background-color: #000; color: white;">
        <div class="container d-flex justify-content-between align-items-center flex-wrap">
            <!-- Company Logo and Name -->
            <div class="d-flex align-items-center mb-3 mb-sm-0">
                <img src="../assets/images/ritzyy.png" alt="Ritzy Technology Logo" width="35"> <!-- Replace with actual path -->
                <h3 class="m-0 ms-2">Ritzy Technology</h3>
            </div>

            <!-- Date, Time, Profile & Logout -->
            <div id="date-time" class="d-flex flex-column flex-md-row align-items-center text-center">
                <span id="current-date-time" class="mb-2 mb-sm-0"></span>
                <div class="d-flex flex-wrap justify-content-center">
                    <a href="profile.php" class="btn btn-secondary ms-2 mb-2">Profile</a>
                    <a href="logout.php" class="btn btn-danger ms-2 mb-2">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Navbar Section -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #000;">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item mx-3">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item mx-3">
                        <a class="nav-link" href="attendance.php">Attendance</a>
                    </li>
                    <li class="nav-item mx-3">
                        <a class="nav-link" href="employees.php">Employees</a>
                    </li>
                    <li class="nav-item mx-3">
                        <a class="nav-link" href="holidays.php">Holiday Management</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript to Show Current Date and Time -->
    
    <script>
        function updateDateTime() {
                const currentDate = new Date();

                // Get the day of the week (e.g., Monday, Tuesday, etc.)
                const weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                const dayOfWeek = weekdays[currentDate.getDay()];

                // Extract the date parts
                const day = currentDate.getDate().toString().padStart(2, '0');
                const month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
                const year = currentDate.getFullYear().toString().slice(-2);

                // Extract the time parts
                const hours = currentDate.getHours().toString().padStart(2, '0');
                const minutes = currentDate.getMinutes().toString().padStart(2, '0');

                // Format the date and time (with day and without seconds)
                const formattedDate = `${dayOfWeek}, ${day}/${month}/${year}`;
                const formattedTime = `${hours}:${minutes}`;

                // Display the formatted date and time
                document.getElementById('current-date-time').textContent = `${formattedDate} ${formattedTime}`;
            }

// Update the date and time every minute
setInterval(updateDateTime, 60000); // Update every 60,000 milliseconds (1 minute)
updateDateTime(); // Initial call to display immediately

    </script>

</body>

</html>
