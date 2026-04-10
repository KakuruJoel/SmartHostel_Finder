<?php
require_once 'config.php';
require_once 'auth_check.php';


// 1. Protection: Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$error = "";
$success = "";

// 2. Fetch Current Data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 3. Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name  = clean($_POST['full_name']);
    $phone = clean($_POST['phone']);
    $new_pass = $_POST['new_password'];

    $update = $conn->prepare("UPDATE users SET full_name = ?, phone = ? WHERE user_id = ?");
    $update->bind_param("ssi", $name, $phone, $uid);

    if ($update->execute()) {
        $_SESSION['name'] = $name; // Update session name immediately
        $success = "Profile updated successfully.";

        if (!empty($new_pass)) {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $pw_upd = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $pw_upd->bind_param("si", $hashed, $uid);
            $pw_upd->execute();
            $success .= " Password changed.";
        }
    } else {
        $error = "Update failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile | SmartHostel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .profile-card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        }

        .avatar-lg {
            width: 100px;
            height: 100px;
            font-size: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            background: #dc3545;
            color: white;
            border-radius: 50%;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #eee;
            background: #fdfdfd;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>


    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">

                <?php if ($success): ?>
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4"><i class="fas fa-check-circle me-2"></i> <?= $success ?></div>
                <?php endif; ?>

                <div class="card profile-card p-4 p-md-5 bg-white">
                    <div class="text-center mb-4">
                        <div class="avatar-lg mb-3 shadow-sm"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></div>
                        <h3 class="fw-800 mb-0"><?= $user['full_name'] ?></h3>
                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2 mt-2"><?= strtoupper($user['role']) ?></span>
                    </div>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?= $user['full_name'] ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase">Email (Non-editable)</label>
                            <input type="email" class="form-control bg-light" value="<?= $user['email'] ?>" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="<?= $user['phone'] ?>" required>
                        </div>

                        <hr class="my-4 opacity-25">

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-danger">Change Password</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current">
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-danger btn-lg w-100 rounded-pill fw-bold shadow mt-3">
                            Save Profile Changes
                        </button>
                    </form>
                </div>

                <div class="text-center mt-4">
                    <a href="dashboard.php" class="text-muted text-decoration-none small"><i class="fas fa-arrow-left me-1"></i> Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>