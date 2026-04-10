<?php
require_once 'config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";
$error = "";

// --- REGISTRATION LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $fullname = clean($_POST['fullname']);
    $email    = clean($_POST['email']);
    $phone    = clean($_POST['phone']);
    $role     = clean($_POST['role']); // 'tenant' or 'landlord'
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // 1. Basic Validation
    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        // 2. Check if email exists
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
            // 3. Hash Password & Insert
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $fullname, $email, $phone, $hashed_pass, $role);

            if ($stmt->execute()) {
                header("Location: login.php?msg=registered");
                exit();
            } else {
                $error = "Registration failed. Please try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | SmartHostel FINDER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 40px 0;
        }

        .register-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            background: white;
            overflow: hidden;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px 15px;
            background-color: #f8f9fa;
            border: 1px solid #eee;
            font-size: 0.9rem;
        }

        .btn-register {
            background: #dc3545;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: #bb2d3b;
            transform: translateY(-2px);
        }

        .logo-img {
            height: 70px;
            width: 70px;
            object-fit: cover;
            border: 4px solid white;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">

                <div class="text-center mb-4">
                    <a href="index.php">
                        <img src="project_logo.png" alt="SmartHostel Logo" class="logo-img rounded-circle shadow-sm">
                    </a>
                    <h4 class="fw-800 mt-3 mb-0">Join SmartHostel</h4>
                    <p class="text-muted small">The premier hostel finder for Mbarara students.</p>
                </div>

                <div class="card register-card">
                    <div class="p-4 p-md-5">
                        <?php if ($error): ?>
                            <div class="alert alert-danger border-0 small py-2 text-center mb-4 rounded-3">
                                <i class="fas fa-exclamation-triangle me-1"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form action="register.php" method="POST">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Full Name</label>
                                    <input type="text" name="fullname" class="form-control shadow-none" placeholder="Enter your full name" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Email Address</label>
                                    <input type="email" name="email" class="form-control shadow-none" placeholder="student@example.com" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Phone Number</label>
                                    <input type="text" name="phone" class="form-control shadow-none" placeholder="07..." required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">I am a...</label>
                                    <select name="role" class="form-select shadow-none" required>
                                        <option value="tenant">Student (Tenant)</option>
                                        <option value="landlord">Landlord / Manager</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Password</label>
                                    <input type="password" name="password" class="form-control shadow-none" placeholder="••••••••" required>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label small fw-bold text-uppercase">Confirm</label>
                                    <input type="password" name="confirm_password" class="form-control shadow-none" placeholder="••••••••" required>
                                </div>
                            </div>

                            <button type="submit" name="register" class="btn btn-register btn-primary w-100 shadow-sm text-white">
                                Create My Account
                            </button>
                        </form>
                    </div>
                    <div class="bg-light p-3 text-center border-top">
                        <p class="mb-0 small text-muted">Already have an account? <a href="login.php" class="text-danger fw-bold text-decoration-none">Login Here</a></p>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-muted text-decoration-none small"><i class="fas fa-arrow-left me-1"></i> Back to Home</a>
                </div>

            </div>
        </div>
    </div>
    <!-- <?php include 'footer.php'; ?> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>