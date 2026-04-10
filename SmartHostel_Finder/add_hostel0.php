<?php
require_once 'config.php';

// 1. Access Control: Only Landlords can add hostels
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: login.php?msg=unauthorized");
    exit();
}

$error = "";
$success = "";

// --- 2. FORM PROCESSING ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_hostel'])) {
    $h_name = clean($_POST['name']);
    $h_loc  = clean($_POST['location']);
    $h_price = floatval($_POST['price_range']);
    $h_desc  = clean($_POST['description']);
    $l_id    = $_SESSION['user_id'];

    // Validation
    if (empty($h_name) || empty($h_loc) || $h_price <= 0) {
        $error = "Please fill all required fields correctly.";
    } else {
        // Insert query - Defaulting booking_status to 'pending' and is_available to 1
        $stmt = $conn->prepare("INSERT INTO hostels (landlord_id, name, location, price, description, booking_status, is_available) VALUES (?, ?, ?, ?, ?, 'pending', 1)");
        $stmt->bind_param("issds", $l_id, $h_name, $h_loc, $h_price, $h_desc);

        if ($stmt->execute()) {
            $success = "Hostel submitted successfully! It is now awaiting Admin approval.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add New Hostel | SmartHostel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .form-card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #eee;
            background: #f8f9fa;
        }

        .btn-submit {
            background: #dc3545;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #bb2d3b;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <!-- <nav class="navbar navbar-light bg-white border-bottom py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
                <img src="project_logo.png" height="40" class="me-2 rounded-circle border">
                <span class="small text-muted">Back to Dashboard</span>
            </a>
        </div>
    </nav> -->

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">

                <div class="text-center mb-4">
                    <h2 class="fw-800">List Your Hostel</h2>
                    <p class="text-muted">Fill in the details below to reach thousands of students in Mbarara.</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 rounded-4 small mb-4"><?= $error ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success border-0 rounded-4 mb-4">
                        <i class="fas fa-check-circle me-2"></i> <?= $success ?>
                        <br><a href="dashboard.php" class="alert-link small">Return to Dashboard</a>
                    </div>
                <?php endif; ?>

                <div class="card form-card">
                    <div class="card-body p-4 p-md-5">
                        <form action="add_hostel.php" method="POST">

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-uppercase">Hostel Name</label>
                                <input type="text" name="name" class="form-control shadow-none" placeholder="e.g. Riverside Plaza" required>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-uppercase">Location / Area</label>
                                    <select name="location" class="form-select shadow-none" required>
                                        <option value="">Select Location...</option>
                                        <option value="Kihumuro">Kihumuro</option>
                                        <option value="Town Centre">Town Centre</option>
                                        <option value="Kakyeka">Kakyeka</option>
                                        <option value="Rwebikoona">Rwebikoona</option>
                                        <option value="Taso Village">Taso Village</option>
                                        <option value="Taso Village">Kasengenge</option>
                                        <option value="Taso Village">Rwemigina</option>
                                        <option value="Taso Village">Nyakiinzi</option>
                                        <option value="Taso Village">Rwentondo</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3 mt-md-0">
                                    <label class="form-label small fw-bold text-uppercase">Price per Semester (UGX)</label>
                                    <input type="text" name="price" class="form-control shadow-none" placeholder="e.g. 800000" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-uppercase">Description & Facilities</label>
                                <textarea name="description" class="form-control shadow-none" rows="5" placeholder="Mention Wi-Fi, Security, Water, Distance to Campus..."></textarea>
                            </div>

                            <div class="bg-light p-3 rounded-4 mb-4 border border-warning-subtle">
                                <p class="mb-0 small text-muted">
                                    <i class="fas fa-info-circle text-warning me-2"></i>
                                    Your listing will be reviewed by the admin before appearing on the public search results.
                                </p>
                            </div>

                            <button type="submit" name="submit_hostel" class="btn btn-submit btn-primary w-100 text-white">
                                Submit for Approval
                            </button>

                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted small">Need help? <a href="mailto:<?= $admin_email ?>" class="text-danger">Contact Support</a></p>
                </div>

            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>