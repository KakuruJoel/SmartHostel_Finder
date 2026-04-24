<?php
include('config.php');
include('auth_check.php');

// 1. Security: Student must be logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php?action=login");
    exit();
}

// 2. Validate Room ID from URL
if (!isset($_GET['room_id'])) {
    header("Location: find_hostel.php");
    exit();
}

$room_id = intval($_GET['room_id']);
$student_id = $_SESSION['user_id'];
$message = "";

// 3. Fetch Room and Hostel Data for display
$sql = "SELECT r.*, h.name as hostel_name, h.location, u.full_name as landlord_name 
        FROM rooms r 
        JOIN hostels h ON r.hostel_id = h.hostel_id 
        JOIN users u ON h.landlord_id = u.user_id
        WHERE r.room_id = $room_id";
$res = $conn->query($sql);
$data = $res->fetch_assoc();

// Check if room exists and is available
if (!$data || $data['availability_status'] !== 'available') {
    echo "<div class='container mt-5 alert alert-warning'>This room is no longer available. <a href='find_hostel.php'>Back to search</a></div>";
    exit();
}

// 4. Handle Booking Submission
if (isset($_POST['confirm_booking'])) {
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $total_price = floatval($_POST['total_price']);
    $deposit = floatval($_POST['deposit_amount']); // Capture the user's input
    $hid = mysqli_real_escape_string($conn, $_POST['hostel_id']);
    $rid = mysqli_real_escape_string($conn, $_POST['room_id']);
    $sid = $_SESSION['user_id'];

    // Validation: Ensure deposit is at least a minimum amount and not more than total
    if ($deposit <= 0) {
        $message = "<div class='alert alert-danger'>Please enter a valid deposit amount.</div>";
    } elseif ($deposit > $total_price) {
        $message = "<div class='alert alert-danger'>Deposit cannot be greater than the total price (UGX " . number_format($total_price) . ").</div>";
    } else {
        // Insert into database - Note we use $deposit for the amount field
        $book_sql = "INSERT INTO bookings (room_id, tenant_id, booking_status, booking_date, amount, hostel_id) 
                     VALUES ('$rid', '$sid', 'pending', NOW(), '$deposit', '$hid')";

        if ($conn->query($book_sql)) {
            $booking_id = $conn->insert_id;
            header("Location: process_payment.php?booking_id=" . $booking_id);
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Booking failed: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Confirm Booking | SmartHostel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        .booking-summary {
            border-left: 5px solid #0d6efd;
            background: #f8fafc;
        }

        .price-tag {
            font-size: 1.5rem;
            color: #198754;
            font-weight: 800;
        }
    </style>
</head>

<body class="bg-light">

    <?php include('navbar.php'); ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h3 class="fw-bold mb-4">Complete Your Booking</h3>

                    <?= $message ?>

                    <div class="booking-summary p-3 mb-4 rounded-3">
                        <h6 class="text-primary fw-bold text-uppercase small mb-2">Reservation Summary</h6>
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($data['hostel_name']) ?></h5>
                        <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($data['location']) ?></p>

                        <div class="d-flex justify-content-between border-top pt-2">
                            <span>Room Number:</span>
                            <span class="fw-bold"><?= htmlspecialchars($data['room_number']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Room Type:</span>
                            <span class="fw-bold"><?= htmlspecialchars($data['room_type']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Landlord:</span>
                            <span class="fw-bold"><?= htmlspecialchars($data['landlord_name']) ?></span>
                        </div>
                    </div>

                    <form action="" method="POST">
                        <input type="hidden" name="room_id" value="<?= $data['room_id'] ?>">
                        <input type="hidden" name="hostel_id" value="<?= $data['hostel_id'] ?>">
                        <input type="hidden" name="total_price" value="<?= $data['price'] ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Expected Move-in Date</label>
                            <input type="date" name="start_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-primary">Amount to Pay Now (Deposit)</label>
                            <div class="input-group">
                                <span class="input-group-text">UGX</span>
                                <input type="number" name="deposit_amount" class="form-control form-control-lg fw-bold"
                                    placeholder="Enter amount" min="100" max="<?= $data['price'] ?>"
                                    value="<?= $data['price'] ?>" required>
                            </div>
                            <div class="form-text">Total semester price: <strong>UGX <?= number_format($data['price']) ?></strong>. You can pay a portion now to secure the room.</div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agree" required>
                            <label class="form-check-label small" for="agree">
                                I confirm that I have read and agree to the <a href="safety.php">safety rules</a> and hostel policies.
                            </label>
                        </div>

                        <button type="submit" name="confirm_booking" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">
                            Confirm & Proceed to Payment
                        </button>
                    </form>
                </div>

                <div class="text-center mt-3">
                    <a href="details.php?id=<?= $data['hostel_id'] ?>" class="text-muted text-decoration-none small">
                        <i class="fas fa-arrow-left me-1"></i> Cancel and go back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>