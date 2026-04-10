<?php
require_once 'config.php';

// 1. Access Control: Admins Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?msg=unauthorized");
    exit();
}

$error = "";
$success = "";

// 2. Fetch User Data
if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$target_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $target_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: manage_users.php?msg=user_not_found");
    exit();
}

// 3. Handle Update Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $name  = clean($_POST['full_name']);
    $email = clean($_POST['email']);
    $phone = clean($_POST['phone']);
    $role  = clean($_POST['role']);
    $pass  = $_POST['new_password'];

    // Update basic info
    $update_stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, role = ? WHERE user_id = ?");
    $update_stmt->bind_param("ssssi", $name, $email, $phone, $role, $target_id);

    if ($update_stmt->execute()) {
        $success = "User information updated successfully.";

        // Handle Password Change (Only if provided)
        if (!empty($pass)) {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $pw_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $pw_stmt->bind_param("si", $hashed, $target_id);
            $pw_stmt->execute();
            $success .= " Password was also reset.";
        }
    } else {
        $error = "Error updating record: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User | <?= $system_name ?></title>
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

        .edit-card {
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
                <h2 class="fw-800 mb-0">Edit User Account</h2>
                <p class="text-muted small">Modifying profile for: <b><?= $user['full_name'] ?></b></p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <?php if ($success): ?>
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4"><i class="fas fa-check-circle me-2"></i> <?= $success ?></div>
                <?php endif; ?>

                <div class="card edit-card p-4 p-md-5 bg-white">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-uppercase">Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?= $user['full_name'] ?>" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-uppercase">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= $user['email'] ?>" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-uppercase">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="<?= $user['phone'] ?>" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-uppercase">System Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="tenant" <?= ($user['role'] == 'tenant') ? 'selected' : '' ?>>Tenant (Student)</option>
                                    <option value="landlord" <?= ($user['role'] == 'landlord') ? 'selected' : '' ?>>Landlord</option>
                                    <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : '' ?>>System Admin</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-danger">Reset Password</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
                            <div class="form-text small">Only fill this if the user requested a password reset.</div>
                        </div>

                        <div class="d-flex gap-2 mt-5">
                            <button type="submit" name="update_user" class="btn btn-dark rounded-pill px-5 fw-bold">Save Changes</button>
                            <a href="manage_users.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card edit-card p-4 bg-white">
                    <h6 class="fw-bold mb-3">Account Meta</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2 text-muted">User ID: <span class="text-dark fw-bold">#<?= $user['user_id'] ?></span></li>
                        <li class="mb-2 text-muted">Created: <span class="text-dark fw-bold"><?= date('d M Y', strtotime($user['created_at'])) ?></span></li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>