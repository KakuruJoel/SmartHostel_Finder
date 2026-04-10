<?php
require_once 'config.php';
require_once 'auth_check.php';

// 1. Access Control: Admins Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?msg=unauthorized");
    exit();
}

$error = "";
$success = "";

// 2. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $name  = clean($_POST['full_name']);
    $email = clean($_POST['email']);
    $phone = clean($_POST['phone']);
    $role  = clean($_POST['role']);
    $pass  = $_POST['password'];

    // Basic Validation
    $check_email = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();

    if ($check_email->get_result()->num_rows > 0) {
        $error = "A user with this email already exists.";
    } else {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $hashed_pass, $role);

        if ($stmt->execute()) {
            $success = "New user account created successfully.";
        } else {
            $error = "Database error: Unable to create user.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add New User | SmartHostel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .main-content {
            margin-left: 260px;
            padding: 40px;
        }

        .add-card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px;
            background: #f8f9fa;
            border: 1px solid #eee;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <?php include 'admin_sidebar.php'; ?>

    <main class="main-content">
        <div class="mb-4 d-flex align-items-center">
            <a href="manage_users.php" class="btn btn-light rounded-circle me-3"><i class="fas fa-arrow-left"></i></a>
            <div>
                <h2 class="fw-800 mb-0">Create User</h2>
                <p class="text-muted small">Add a new member to the SmartHostel ecosystem.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4"><?= $error ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4">
                        <i class="fas fa-check-circle me-2"></i> <?= $success ?>
                        <br><a href="manage_users.php" class="alert-link small">Back to User List</a>
                    </div>
                <?php endif; ?>

                <div class="card add-card p-4 p-md-5 bg-white">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-uppercase">Full Name</label>
                                <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-uppercase">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-uppercase">Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="07..." required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-uppercase">Assigned Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="tenant">Tenant (Student)</option>
                                    <option value="landlord">Landlord</option>
                                    <option value="admin">System Admin</option>
                                </select>
                            </div>
                            <div class="col-12 mb-4">
                                <label class="form-label small fw-bold text-uppercase">Temporary Password</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                                <div class="form-text small">Users should be encouraged to change this after their first login.</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="add_user" class="btn btn-danger rounded-pill px-5 fw-bold shadow-sm">Create Account</button>
                            <a href="manage_users.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card add-card p-4 bg-dark text-white shadow">
                    <h6 class="fw-bold mb-3 text-danger"><i class="fas fa-shield-alt me-2"></i>Security Note</h6>
                    <p class="small opacity-75 mb-0">Creating an account here bypasses the standard email verification process. Ensure the details provided are accurate to maintain system integrity.</p>
                </div>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>