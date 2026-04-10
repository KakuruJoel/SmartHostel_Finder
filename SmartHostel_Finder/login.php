<?php
require_once 'config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') header("Location: admin.php");
    else header("Location: dashboard.php");
    exit();
}

$error = "";

// --- LOGIN LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = clean($_POST['email']);
    $password = $_POST['password'];

    // 1. Fetch User
    $stmt = $conn->prepare("SELECT user_id, full_name, password_hash, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $u = $result->fetch_assoc();

        // 2. Verify Password
        if (password_verify($password, $u['password_hash'])) {

            // --- SECURITY: Regenerate ID to prevent fixation ---
            session_regenerate_id(true);

            // 3. Set Session Variables
            $_SESSION['user_id'] = $u['user_id'];
            $_SESSION['role']    = $u['role'];
            $_SESSION['name']    = $u['full_name'];
            $_SESSION['last_activity'] = time();

            // 4. Role-Based Redirect
            if ($u['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "No account found with that email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SmartHostel FINDER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            background: white;
        }

        .login-header {
            background: #dc3545;
            padding: 40px;
            text-align: center;
            color: white;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            background-color: #f8f9fa;
            border: 1px solid #eee;
        }

        .btn-login {
            background: #dc3545;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #bb2d3b;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">

                <div class="text-center mb-4">
                    <a href="index.php"><img src="<?= $logo_path ?>" alt="Logo" height="70" class="rounded-circle shadow-sm border border-white border-4"></a>
                </div>

                <div class="card login-card">
                    <div class="p-4 p-md-5">
                        <h3 class="fw-800 text-center mb-2">Welcome Back</h3>
                        <p class="text-muted text-center mb-4 small">Securely access your SmartHostel account.</p>

                        <?php if ($error): ?>
                            <div class="alert alert-danger border-0 small py-2 text-center rounded-3">
                                <i class="fas fa-exclamation-circle me-1"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
                            <div class="alert alert-success border-0 small py-2 text-center rounded-3">
                                Registration successful! Please login.
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold text-uppercase">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input id="email" type="email" name="email" class="form-control shadow-none border-0" placeholder="name@example.com" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label small fw-bold text-uppercase">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
                                    <input id="password" type="password" name="password" class="form-control shadow-none border-0" placeholder="••••••••" required>
                                </div>
                            </div>

                            <button type="submit" name="login" class="btn btn-login btn-primary w-100 shadow-sm">
                                Sign In
                            </button>
                        </form>
                    </div>
                    <div class="bg-light p-3 text-center border-top">
                        <p class="mb-0 small text-muted">Don't have an account? <a href="register.php" class="text-danger fw-bold text-decoration-none">Sign Up</a></p>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-muted text-decoration-none small"><i class="fas fa-arrow-left me-1"></i> Back to Home</a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>