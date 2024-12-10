<?php
session_start();

// Load admin credentials
$config = include 'admin/config.php';

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: public/dashboard.php'); // Redirect to dashboard
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate credentials
    if ($username === $config['username'] && password_verify($password, $config['password'])) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: public/dashboard.php'); // Redirect to dashboard
        exit();
    } else {
        $error_message = 'Invalid credentials!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/images/ritzyy.png" type="image/x-icon">
    <title>Ritzy Attendance Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            height: 100vh;
            margin: 0;
            background-color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background-color: #212529;
            color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .logo {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 80px;
            height: 80px;
        }
        .company-name {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #fff;
        }
        .form-control {
            background-color: #343a40;
            color: white;
            border: 1px solid #495057;
        }
        .form-control:focus {
            background-color: #343a40;
            color: white;
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .btn.custom-btn {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .btn.custom-btn:hover {
            background-color: #0056b3;
        }

        /* Media Query for Mobile Devices */
        @media (max-width: 576px) {
            .login-container {
                padding: 15px;
                margin: 0 10px; /* Add margin on both sides for mobile */
            }
            .company-name {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Logo -->
        <div class="logo">
            <img src="assets/images/ritzyy.png" alt="Company Logo">
        </div>

        <!-- Company Name -->
        <div class="company-name">Ritzy Technology</div>

        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php } ?>

        <form action="index.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn custom-btn w-100">Login</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
