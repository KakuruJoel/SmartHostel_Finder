<?php
include('config.php');
include('auth_check.php');

// 1. Security Check
if (!isset($_SESSION['user_id']) || !isset($_GET['booking_id'])) {
    header("Location: auth.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);
$student_id = $_SESSION['user_id'];

// 2. Fetch Booking and Room Details
$sql = "SELECT b.*, r.room_number, h.name as hostel_name 
        FROM bookings b
        JOIN rooms r ON b.room_id = r.room_id
        JOIN hostels h ON r.hostel_id = h.hostel_id
        WHERE b.booking_id = $booking_id AND b.tenant_id = $student_id";
$res = $conn->query($sql);
$booking = $res->fetch_assoc();

if (!$booking) {
    die("Invalid Booking Access.");
}

// 3. Handle Simulated Payment Logic
if (isset($_POST['pay_now'])) {
    $phone = mysqli_real_escape_string($conn, $_POST['momo_number']);
    $network = $_POST['network'];

    // In a real app, you'd call an API here. 
    // For the project, we simulate success after a "processing" delay.

    $transaction_id = "SH-" . strtoupper(uniqid());
    $amount = $booking['amount'];

    // Update Booking Status
    $update_booking = "UPDATE bookings SET booking_status = 'paid' WHERE booking_id = $booking_id";

    // Update Room Status to 'occupied'
    $room_id = $booking['room_id'];
    $update_room = "UPDATE rooms SET availability_status = 'occupied' WHERE room_id = $room_id";

    // Insert into a hypothetical payments table (if you have one)
    $pay_sql = "INSERT INTO payments (booking_id, transaction_id, amount, method, status) 
                VALUES ($booking_id, '$transaction_id', '$amount', '$network', 'completed')";

    if ($conn->query($update_booking) && $conn->query($update_room) && $conn->query($pay_sql)) {
        header("Location: payment_success.php?tid=" . $transaction_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Secure Payment | SmartHostel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        .momo-card {
            border: 2px solid #eee;
            cursor: pointer;
            transition: 0.3s;
        }

        .momo-card:hover {
            border-color: #ffcc00;
        }

        input[type="radio"]:checked+.momo-card {
            border-color: #ffcc00;
            background: #fffdf0;
        }

        .network-logo {
            height: 40px;
        }
    </style>
</head>

<body class="bg-light">
    <?php include('navbar.php'); ?>


    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Checkout</h2>
                    <p class="text-muted">Booking Reference: #<?= $booking_id ?></p>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><?= $booking['hostel_name'] ?> (Room <?= $booking['room_number'] ?>)</span>
                        <span class="fw-bold">UGX <?= number_format($booking['amount']) ?></span>
                    </div>
                    <hr>

                    <form action="" method="POST" id="paymentForm">
                        <label class="fw-bold small mb-3">Select Mobile Network</label>
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <input type="radio" name="network" value="MTN" id="mtn" class="btn-check" required>
                                <label class="card momo-card p-3 text-center rounded-3 w-100" for="mtn">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/af/MTN_Logo.svg" class="network-logo mb-2" alt="MTN">
                                    <div class="small fw-bold">MTN MoMo</div>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" name="network" value="Airtel" id="airtel" class="btn-check">
                                <label class="card momo-card p-3 text-center rounded-3 w-100" for="airtel">
                                    <img src="Airtel_logo.png" class="network-logo mb-2" alt="Airtel">
                                    <div class="small fw-bold">Airtel Money</div>
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold small mb-2">Mobile Money Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">+256</span>
                                <input type="text" name="momo_number" class="form-control" placeholder="771234567" required maxlength="9">
                            </div>
                            <small class="text-muted">Enter the number to receive the PIN prompt.</small>
                        </div>

                        <button type="submit" name="pay_now" class="btn btn-warning w-100 py-3 fw-bold rounded-pill" id="payBtn">
                            Pay UGX <?= number_format($booking['amount']) ?>
                        </button>
                    </form>
                </div>

                <div class="text-center">
                    <p class="small text-muted"><i class="fas fa-lock me-1"></i> Secured by SmartHostel Encryption</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add a loading effect to the button when clicked
        document.getElementById('paymentForm').onsubmit = function() {
            let btn = document.getElementById('payBtn');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            btn.classList.add('disabled');
        };
    </script>
    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>