<?php
include('config.php');
include('auth_check.php');
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic Validation
    if ($password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>Passwords do not match!</div>";
    } else {
        // Check if email exists
        $check = $conn->query("SELECT email FROM users WHERE email='$email'");
        if ($check->num_rows > 0) {
            $message = "<div class='alert alert-danger'>Email already registered!</div>";
        } else {
            $hashed_pass = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users (full_name, email, password_hash, role, phone) 
                    VALUES ('$full_name', '$email', '$hashed_pass', '$role', '$phone')";

            if ($conn->query($sql)) {
                header("Location: login.php?msg=registered");
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Join SmartHostel | Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        body {
            background-color: #f4f7f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .reg-container {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .reg-sidebar {
            background: linear-gradient(135deg, #04d7f3 0%, #0abae6 100%);
            color: #000;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ced4da;
        }

        .btn-primary {
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 reg-container">
                <div class="row">
                    <div class="col-md-5 reg-sidebar d-none d-md-flex text-center">
                        <img src="project_logo.png" alt="SmartHostel FINDER Logo" class="sidebar-logo">
                        <h2 class="fw-bold">Create Account</h2>
                        <p class="lead">Join thousands of students and landlords in Mbarara's smartest hostel network.</p>
                        <ul class="list-unstyled text-start mx-auto mt-3">
                            <li><i class="fas fa-check-circle me-2"></i> Quick Booking</li>
                            <li><i class="fas fa-check-circle me-2"></i> Verified Listings</li>
                            <li><i class="fas fa-check-circle me-2"></i> Direct Contact</li>
                        </ul>
                    </div>

                    <div class="col-md-7 p-5">
                        <h3 class="fw-bold mb-4">Sign Up</h3>
                        <?= $message ?>

                        <form action="register.php" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" placeholder="0700000000" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">I am a...</label>
                                <select name="role" class="form-select" required>
                                    <option value="tenant">Tenant (Looking for a room)</option>
                                    <option value="landlord">Landlord (I own a property)</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">Create Account</button>

                            <div class="text-center">
                                <p class="text-muted">Already have an account? <a href="login.php" class="text-decoration-none fw-bold">Login</a></p>
                                <a href="index.php" class="text-muted small"><i class="fas fa-arrow-left"></i> Return to Homepage</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>