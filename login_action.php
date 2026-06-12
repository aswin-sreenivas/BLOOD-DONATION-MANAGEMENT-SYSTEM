<?php
// login_action.php
session_start();
require_once 'config/database.php';
require_once 'config/constants.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($email) || empty($password) || empty($role)) {
        header("Location: login.php?error=invalid_credentials");
        exit();
    }

    $table = "";
    $id_field = "";
    $name_field = "Name";

    // Determine table and primary key based on role
    switch ($role) {
        case 'Admin':
            $table = '`ADMIN`';
            $id_field = 'Admin_ID';
            break;
        case 'Donor':
            $table = 'DONOR';
            $id_field = 'Donor_ID';
            break;
        case 'Recipient':
            $table = 'RECIPIENT';
            $id_field = 'Recipient_ID';
            break;
        case 'Hospital':
            $table = 'HOSPITAL';
            $id_field = 'Hospital_ID';
            $name_field = 'Hospital_Name';
            break;
        case 'Staff':
            $table = 'Staff';
            $id_field = 'Staff_ID';
            break;
        default:
            header("Location: login.php?error=invalid_credentials");
            exit();
    }

    try {
        // Prepare statement (SQL Injection Prevention)
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE Email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['Password'])) {

            // Checking approval status for roles that require it
            if (in_array($role, ['Donor', 'Recipient', 'Hospital'])) {
                if ($user['Status'] === 'Pending') {
                    header("Location: login.php?error=pending_approval");
                    exit();
                } else if ($user['Status'] === 'Rejected') {
                    header("Location: login.php?error=inactive");
                    exit();
                }
            } else if ($role === 'Staff' && $user['Status'] === 'Inactive') {
                header("Location: login.php?error=inactive");
                exit();
            }

            // Set session variables
            $_SESSION['user_id'] = $user[$id_field];
            $_SESSION['role'] = $role;
            $_SESSION['user_name'] = $user[$name_field];
            $_SESSION['email'] = $user['Email'];

            // Save Hospital ID context for staff
            if ($role === 'Staff') {
                $_SESSION['hospital_id'] = $user['Hospital_ID'];
            }

            // Redirect to respective dashboard
            $dashboard_url = strtolower($role) . "/dashboard.php";
            header("Location: " . $dashboard_url);
            exit();

        } else {
            // Invalid credentials
            header("Location: login.php?error=invalid_credentials");
            exit();
        }

    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        header("Location: login.php?error=system_error");
        exit();
    }
} else {
    // If accessed directly without POST
    header("Location: index.php");
    exit();
}
?>