<?php
require_once 'config.php';

// 1. Access Control: Students Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php?msg=unauthorized");
    exit();
}

$uid = $_SESSION['user_id'];
$error = "";
$success = "";

// 2. Auto-detect the Student's Hostel
$check_booking = $conn->query("SELECT h.hostel_id, h.name FROM bookings b JOIN hostels h ON b.hostel_id = h.hostel_id WHERE b.tenant_id = $uid AND b.booking_status = 'confirmed' LIMIT 1");

if ($check_booking->num_rows == 0) {
    $error = "You don't have an active confirmed booking. You can only report issues for hostels you are officially staying in.";
} else {
    $my_hostel = $check_booking->fetch_assoc();
}

// 3. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report_issue']) && isset($my_hostel)) {
    $issue_type = clean($_POST['issue_type']);
    $description = clean($_POST['description']);
    $hid = $my_hostel['hostel_id'];

    if (empty($description)) {
        $error = "Please provide details about the problem.";
    } else {
        $stmt = $conn->prepare("INSERT INTO maintenance (hostel_id, tenant_id, issue_type, description, booking_status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iiss", $hid, $uid, $issue_type, $description);

        if ($stmt->execute()) {
            $success = "Your report has been sent to the landlord. They will attend to it soon.";
        } else {
            $error = "System error. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Report an Issue | SmartHostel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .report-card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px;
            background: #f8f9fa;
            border: 1px solid #eee;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-center mb-4">
                    <h2 class="fw-800">Maintenance Request</h2>
                    <p class="text-muted">Is something broken? Let us help you get it fixed.</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 rounded-4 mb-4"><?= $error ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success border-0 rounded-4 mb-4">
                        <i class="fas fa-check-circle me-2"></i> <?= $success ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($my_hostel)): ?>
                    <div class="card report-card p-4 p-md-5 bg-white">
                        <form action="report_issue.php" method="POST">
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-uppercase text-danger">Hostel</label>
                                <input type="text" class="form-control fw-bold" value="<?= $my_hostel['name'] ?>" readonly>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-uppercase">Issue Type</label>
                                <select name="issue_type" class="form-select shadow-none" required>
                                    <option value="Plumbing">Plumbing (Leaking, No Water)</option>
                                    <option value="Electrical">Electrical (Lights, Sockets)</option>
                                    <option value="Furniture">Furniture (Bed, Desk, Door)</option>
                                    <option value="Security">Security (Locks, Gates)</option>
                                    <option value="Internet">Internet / WiFi</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-uppercase">Description</label>
                                <textarea name="description" class="form-control shadow-none" rows="4" placeholder="Be specific: e.g. Room 4B tap is leaking heavily..." required></textarea>
                            </div>

                            <button type="submit" name="report_issue" class="btn btn-danger btn-lg w-100 rounded-pill fw-bold shadow">
                                Send Report
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>