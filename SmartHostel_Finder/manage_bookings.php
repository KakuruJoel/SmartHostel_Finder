<?php
require_once 'config.php';
require_once 'auth_check.php';

// 1. Access Control: Admins Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?msg=unauthorized");
    exit();
}

// --- 2. HANDLE BOOKING ACTIONS ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $bid = intval($_GET['id']);
    $booking_status = ($_GET['action'] == 'confirm') ? 'confirmed' : 'cancelled';

    $stmt = $conn->prepare("UPDATE bookings SET booking_status = ? WHERE booking_id = ?");
    $stmt->bind_param("si", $booking_status, $bid);

    if ($stmt->execute()) {
        // If confirmed, we could optionally send an email/notification here
        header("Location: manage_bookings.php?msg=booking_" . $booking_status);
    }
    exit();
}

// --- 3. DATA FETCHING ---
$query = "SELECT b.*, u.full_name as tenant_name, u.phone as tenant_phone, h.name 
          FROM bookings b 
          JOIN users u ON b.tenant_id = u.user_id 
          JOIN hostels h ON b.hostel_id = h.hostel_id 
          ORDER BY b.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Bookings | <?= $system_name ?></title>
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

        .booking-card {
            border: none;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .trans-id {
            font-family: 'Courier New', Courier, monospace;
            background: #f8f9fa;
            padding: 2px 8px;
            border-radius: 5px;
            font-weight: bold;
            color: #dc3545;
        }

        .booking_status-badge {
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 700;
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
        <div class="mb-5">
            <h2 class="fw-800 mb-1">Financial Records</h2>
            <p class="text-muted">Verify transactions and manage student placements.</p>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success border-0 rounded-4 py-3 mb-4 shadow-sm">
                <i class="fas fa-check-circle me-2"></i> Action completed: <?= str_replace('_', ' ', $_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <div class="card booking-card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 px-3">Student / Tenant</th>
                            <th class="border-0">Hostel</th>
                            <th class="border-0">Transaction ID</th>
                            <th class="border-0">Amount</th>
                            <th class="border-0">booking_status</th>
                            <th class="border-0 text-end px-3">Verification</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($b = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-3">
                                        <div class="fw-bold"><?= $b['tenant_name'] ?></div>
                                        <div class="text-muted small"><?= $b['tenant_phone'] ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold"><?= $b['name'] ?></div>
                                        <div class="text-muted" style="font-size: 0.7rem;"><?= date('d M Y, H:i', strtotime($b['created_at'])) ?></div>
                                    </td>
                                    <td>
                                        <span class="trans-id"><?= $b['transaction_id'] ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= number_format($b['amount']) ?></div>
                                        <div class="text-muted small" style="font-size: 0.6rem;">UGX</div>
                                    </td>
                                    <td>
                                        <?php
                                        $s = $b['booking_status'];
                                        $class = ($s == 'confirmed') ? 'success' : ($s == 'pending' ? 'warning' : 'danger');
                                        ?>
                                        <span class="booking_status-badge bg-<?= $class ?>-subtle text-<?= $class ?>">
                                            <?= strtoupper($s) ?>
                                        </span>
                                    </td>
                                    <td class="text-end px-3">
                                        <?php if ($s == 'pending'): ?>
                                            <a href="manage_bookings.php?action=confirm&id=<?= $b['booking_id'] ?>" class="btn btn-sm btn-success rounded-pill px-3 me-1">Confirm</a>
                                            <a href="manage_bookings.php?action=cancel&id=<?= $b['booking_id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3">Cancel</a>
                                        <?php else: ?>
                                            <span class="text-muted small italic">Processed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted small">No booking records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4 ms-auto">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-dark text-white">
                    <small class="text-uppercase opacity-50 fw-bold">Total Confirmed Revenue</small>
                    <?php
                    $total = $conn->query("SELECT SUM(amount) FROM bookings WHERE booking_status='confirmed'")->fetch_row()[0];
                    ?>
                    <h2 class="fw-800 mb-0 mt-2"><?= formatMoney($total ?: 0) ?></h2>
                </div>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>