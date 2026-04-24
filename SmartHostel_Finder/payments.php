<?php
include('config.php');
include('auth_check.php');

// Security: Only landlords allowed
if ($_SESSION['role'] !== 'landlord') {
    header("Location: index.php");
    exit();
}

$landlord_id = $_SESSION['user_id'];

// Fetch Totals
$stats_sql = "SELECT SUM(amount) as total_revenue, COUNT(payment_id) as total_tx 
              FROM payments p 
              JOIN hostels h ON p.booking_id IN (SELECT booking_id FROM bookings WHERE hostel_id = h.hostel_id)
              WHERE h.landlord_id = $landlord_id";
$stats_res = $conn->query($stats_sql);
$stats = $stats_res->fetch_assoc();

// Fetch Transactions
$list_sql = "SELECT p.*, h.name as hostel_name, r.room_number 
             FROM payments p
             JOIN bookings b ON p.booking_id = b.booking_id
             JOIN rooms r ON b.room_id = r.room_id
             JOIN hostels h ON b.hostel_id = h.hostel_id
             WHERE h.landlord_id = $landlord_id
             ORDER BY p.created_at DESC";
$list_res = $conn->query($list_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Revenue Dashboard | SmartHostel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
</head>

<body class="bg-light">
    <?php include('navbar.php'); ?>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Financial Overview</h2>
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-download me-2"></i>Export Report
            </button>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-primary text-white">
                    <small class="text-uppercase opacity-75">Total Revenue</small>
                    <h2 class="fw-bold mb-0">UGX <?= number_format($stats['total_revenue'] ?? 0) ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <small class="text-uppercase text-muted">Total Transactions</small>
                    <h2 class="fw-bold mb-0"><?= $stats['total_tx'] ?></h2>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Hostel / Room</th>
                                <th>Transaction ID</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th class="pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $list_res->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4"><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <span class="fw-bold"><?= htmlspecialchars($row['hostel_name']) ?></span><br>
                                        <small class="text-muted">Room <?= htmlspecialchars($row['room_number']) ?></small>
                                    </td>
                                    <td><code><?= $row['transaction_id'] ?></code></td>
                                    <td class="fw-bold text-success">UGX <?= number_format($row['amount']) ?></td>
                                    <td><?= ucfirst($row['method']) ?></td>
                                    <td class="pe-4">
                                        <span class="badge bg-success-soft text-success border border-success">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>

</html>