<?php
include('config.php');
include('auth_check.php');

// Security: Ensure only admins can access
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// 1. Fetch Key Metrics
$total_revenue = $conn->query("SELECT SUM(amount) as total FROM bookings WHERE booking_status = 'confirmed'")->fetch_assoc()['total'];
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'pending'")->fetch_assoc()['count'];
$total_hostels = $conn->query("SELECT COUNT(*) as count FROM hostels")->fetch_assoc()['count'];
$occupied_rooms = $conn->query("SELECT COUNT(*) as count FROM rooms WHERE availability_status = 'occupied'")->fetch_assoc()['count'];

// 2. Fetch Detailed Booking Report
$sql = "SELECT b.booking_id, u.full_name as tenant_name, h.name as hostel_name, 
               r.room_number, b.amount, b.booking_status, b.booking_date 
        FROM bookings b
        JOIN users u ON b.tenant_id = u.user_id
        JOIN rooms r ON b.room_id = r.room_id
        JOIN hostels h ON r.hostel_id = h.hostel_id
        ORDER BY b.booking_date DESC";
$report_res = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Reports | SmartHostel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
</head>

<body class="bg-light">
    <?php include('navbar.php'); ?>

    <div class="container py-5">
        <h2 class="fw-bold mb-4">System Reports & Analytics</h2>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-primary text-white">
                    <small>Total Revenue</small>
                    <h3 class="fw-bold">UGX <?= number_format($total_revenue) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-warning text-dark">
                    <small>Pending Bookings</small>
                    <h3 class="fw-bold"><?= $pending_bookings ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-success text-white">
                    <small>Occupancy</small>
                    <h3 class="fw-bold"><?= $occupied_rooms ?> Rooms</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-dark text-white">
                    <small>Total Hostels</small>
                    <h3 class="fw-bold"><?= $total_hostels ?></h3>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Recent Bookings Detailed Report</h5>
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Tenant</th>
                            <th>Hostel (Room)</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $report_res->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($row['booking_date'])) ?></td>
                                <td><?= $row['tenant_name'] ?></td>
                                <td><?= $row['hostel_name'] ?> (<?= $row['room_number'] ?>)</td>
                                <td>UGX <?= number_format($row['amount']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $row['booking_status'] == 'confirmed' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($row['booking_status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>