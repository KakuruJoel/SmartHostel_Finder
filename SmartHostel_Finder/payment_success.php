<?php
include('config.php');
include('auth_check.php');

$tid = isset($_GET['tid']) ? mysqli_real_escape_string($conn, $_GET['tid']) : 'N/A';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Successful | SmartHostel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
</head>

<body class="bg-light">
    <?php include('navbar.php'); ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card border-0 shadow-sm p-5 rounded-4">
                    <i class="fas fa-check-circle text-success fa-5x mb-4"></i>
                    <h2 class="fw-bold">Payment Successful!</h2>
                    <p class="text-muted">Your booking has been confirmed and the room is now reserved for you.</p>

                    <div class="bg-light p-3 rounded-3 my-4">
                        <small class="text-uppercase fw-bold text-muted d-block">Transaction ID</small>
                        <span class="fw-bold h5 text-primary"><?= htmlspecialchars($tid) ?></span>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="dashboard.php" class="btn btn-primary rounded-pill py-2">Go to Dashboard</a>
                        <a href="index.php" class="btn btn-outline-secondary rounded-pill py-2">Back Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>

</html>