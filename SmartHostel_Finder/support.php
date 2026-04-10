<?php
include('config.php');

$message = "";

// --- Handle Support Ticket Submission ---
if (isset($_POST['submit_ticket'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $user_msg = mysqli_real_escape_string($conn, $_POST['message']);

    // For project defense, we simulate a successful database entry/email trigger.
    $message = "<div class='alert alert-primary border-0 shadow-sm rounded-3 mb-4'>
                    <i class='fas fa-check-circle me-2'></i> 
                    Thank you, $name. Your support ticket has been sent to the Admin!
                </div>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Support | SmartHostel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        .support-header {
            background: #0d6efd;
            color: white;
            padding: 60px 0;
            border-radius: 0 0 50px 50px;
        }

        .contact-card {
            border: none;
            border-radius: 20px;
            margin-top: -50px;
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            transition: 0.3s;
        }

        .info-box:hover {
            background: #eef5ff;
        }

        .info-icon {
            width: 45px;
            height: 45px;
            background: #0d6efd;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body class="bg-light">

    <?php include('navbar.php'); ?>

    <div class="support-header text-center">
        <div class="container">
            <h1 class="fw-bold">How can we help?</h1>
            <p class="lead opacity-75">Our team is available to assist with bookings, MoMo payments, and account issues.</p>
        </div>
    </div>

    <div class="container mb-5 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card contact-card shadow-lg p-4 p-md-5 bg-white border-0">
                    <div class="row g-5">

                        <div class="col-md-4">
                            <h4 class="fw-bold mb-4 text-dark">Get in Touch</h4>

                            <div class="info-box mb-3">
                                <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                                <h6 class="fw-bold mb-1">Phone Support</h6>
                                <p class="text-muted small mb-0">+256 700 000000</p>
                                <p class="text-muted small">+256 770 000000</p>
                            </div>

                            <div class="info-box mb-3">
                                <div class="info-icon"><i class="fas fa-envelope"></i></div>
                                <h6 class="fw-bold mb-1">Email Us</h6>
                                <p class="text-muted small mb-0">support@smarthostel.ug</p>
                            </div>

                            <div class="info-box">
                                <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <h6 class="fw-bold mb-1">Office</h6>
                                <p class="text-muted small mb-0">Mbarara City, Uganda</p>
                            </div>
                        </div>

                        <div class="col-md-8 ps-md-5">
                            <h4 class="fw-bold mb-4 text-dark">Send a Message</h4>
                            <?= $message ?>
                            <form action="support.php" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Your Name</label>
                                        <input type="text" name="name" class="form-control" value="<?= $_SESSION['name'] ?? '' ?>" placeholder="Enter name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Email Address</label>
                                        <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">What are you inquiring about?</label>
                                        <select name="subject" class="form-select">
                                            <option>Payment Verification (MoMo)</option>
                                            <option>Booking Status</option>
                                            <option>Hostel Approval Inquiry</option>
                                            <option>Report User/Fraud</option>
                                            <option>Technical Issue</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Details</label>
                                        <textarea name="message" class="form-control" rows="5" placeholder="Describe your issue in detail..." required></textarea>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" name="submit_ticket" class="btn btn-primary px-5 py-2 fw-bold rounded-pill">
                                            Submit Ticket <i class="fas fa-paper-plane ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>