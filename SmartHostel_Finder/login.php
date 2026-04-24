<?php
// session_start(); 
require_once 'config.php';
require_once 'auth_check.php';

$message = "";
$error = "";

// Capture Cross-Page Messages (e.g., from register.php?msg=registered)
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'registered') {
        $message = "<div class='alert alert-success border-0 shadow-sm small'><i class='fas fa-check-circle me-2'></i> Registration successful! Please login.</div>";
    } elseif ($_GET['msg'] == 'login_required') {
        $message = "<div class='alert alert-warning border-0 shadow-sm small'><i class='fas fa-exclamation-triangle me-2'></i> Please login to access that page.</div>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

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

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | SmartHostel FINDER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css" />
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f7f6;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-container {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .login-sidebar {
            background: linear-gradient(135deg, #04d7f3 0%, #0abae6 100%);
            color: #000;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            font-weight: 600;
            font-size: 20px;
        }

        /* Updated Logo Integration */
        .sidebar-logo {
            max-width: 100%;
            height: auto;
            margin-bottom: 25px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 12px;
            font-weight: bold;
            border-radius: 10px;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .form-control {
            padding: 12px;
            border-radius: 10px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-right: none;
            color: #6c757d;
            border-radius: 10px 0 0 10px;
        }

        .form-control {
            border-radius: 0 10px 10px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9 login-container">
                <div class="row">
                    <div class="col-md-5 login-sidebar d-none d-md-flex">
                        <img src="project_logo.png" alt="SmartHostel FINDER Logo" class="sidebar-logo">

                        <h2 class="fw-bold fs-3 text-white">Welcome Back!</h2>
                        <p class="opacity-75 small mt-2">Find, manage, and secure your properties with Mbarara's smartest hostel network.</p>
                    </div>

                    <div class="col-md-7 p-4 p-md-5">
                        <div class="mb-4">
                            <h3 class="fw-bold text-dark">Sign In</h3>
                            <p class="text-muted small">Access your account by entering your details below.</p>
                        </div>

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
                                <label class="form-label small fw-bold text-secondary">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="name@example.com" required />
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="••••••••" required />
                                </div>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-primary shadow-sm">
                                    Login to My Account
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="text-muted small">
                                    New to our network?
                                    <a href="register.php" class="text-decoration-none fw-bold text-primary">Create an Account</a>
                                </p>
                                <hr class="my-4">
                                <a href="index.php" class="text-muted text-decoration-none small">
                                    <i class="fas fa-arrow-left me-1"></i> Return to Homepage
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>