<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}
include('header.php');

$config_path = '../admin/config.php';
$config = include $config_path;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate the current password
    if (!password_verify($current_password, $config['password'])) {
        $error_message = 'Current password is incorrect!';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'New password and confirm password do not match!';
    } else {
        // Update the password in config.php
        $new_config = [
            'username' => $config['username'],
            'password' => password_hash($new_password, PASSWORD_DEFAULT)
        ];

        // Save updated credentials
        if (file_put_contents($config_path, "<?php\nreturn " . var_export($new_config, true) . ";")) {
            $success_message = 'Password changed successfully!';
        } else {
            $error_message = 'Failed to update the password. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/ritzyy.png" type="image/x-icon">
    <title>Ritzy Attendance Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
       
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body style="background-color: #212529; color: white;">
    <div class="container mt-5">
        <h2 class="text-center">Admin Profile</h2>
        <br>
        <h3>Change Password</h3>
        <br>
        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php } ?>

        <?php if (isset($success_message)) { ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php } ?>

        <form action="profile.php" method="POST">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" class="form-control" name="current_password" required>
            </div>

            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" class="form-control" name="new_password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>

            <button type="submit" class="btn custom-btn">Change Password</button>
            <br>
            <br>
        </form>
    </div>
</body>
</html>

<?php 
// Include the footer file
include('footer.php');
?>