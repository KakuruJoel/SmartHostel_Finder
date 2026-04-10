<?php
include('config.php');

// 1. Security Check: Only Tenants allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$user_name = $_SESSION['name'];

// Logic: Cancel a pending booking
if (isset($_GET['cancel_id'])) {
    $bid = intval($_GET['cancel_id']);
    // Only allow cancellation if it's still pending
    $conn->query("DELETE FROM bookings WHERE booking_id=$bid AND tenant_id=$uid AND booking_status='pending'");
    header("Location: bookings.php?msg=cancelled");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Bookings | SmartHostel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        body {
            background-color: #f4f7f6;
            overflow-x: hidden;
            font-family: 'Segoe UI', sans-serif;
        }

        #wrapper {
            display: flex;
            width: 100%;
        }

        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #212529;
            color: #fff;
            min-height: 100vh;
        }

        .sidebar-header {
            padding: 30px 20px;
            background: #1a1d20;
            text-align: center;
        }

        .sidebar-menu a {
            padding: 12px 25px;
            display: block;
            color: #adb5bd;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar-menu a:hover {
            color: #fff;
            background: #343a40;
        }

        .sidebar-menu a.active {
            color: #fff;
            background: #0d6efd;
            border-radius: 0 30px 30px 0;
            margin-right: 15px;
        }

        #content {
            width: 100%;
            padding: 40px;
        }

        .booking-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            background: white;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
    </style>
</head>

<body>

    <div id="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <h4 class="fw-bold text-primary mb-0"><i class="fas fa-hotel me-2"></i>SmartHostel</h4>
                <small class="text-muted text-uppercase">Tenant Panel</small>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php"><i class="fas fa-th-large me-3"></i>Dashboard</a>
                <a href="find_hostels.php"><i class="fas fa-search me-3"></i>Find Hostels</a>
                <a href="bookings.php" class="active"><i class="fas fa-calendar-alt me-3"></i>My Bookings</a>
                <div class="px-4 mt-4">
                    <hr class="bg-secondary">
                </div>
                <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-3"></i>Logout</a>
            </div>
        </nav>

        <div id="content">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="fw-bold mb-0">My Booking History</h2>
                    <p class="text-muted">Track your applications and stay history.</p>
                </div>
                <div class="bg-white p-2 rounded shadow-sm px-3 border">
                    <span class="small text-muted">Active Requests: </span>
                    <span class="fw-bold text-primary">
                        <?php echo $conn->query("SELECT count(*) as c FROM bookings WHERE tenant_id=$uid AND booking_status='pending'")->fetch_assoc()['c']; ?>
                    </span>
                </div>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'cancelled'): ?>
                <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4">
                    Booking request has been cancelled.
                </div>
            <?php endif; ?>

            <div class="card booking-card overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Hostel & Room</th>
                                <th>Applied On</th>
                                <th>Scheduled Move-in</th>
                                <th>Status</th>
                                <th class="text-center">Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = $conn->query("SELECT b.*, h.name as hname, h.location, r.room_type, r.price 
                                             FROM bookings b 
                                             JOIN rooms r ON b.room_id = r.room_id 
                                             JOIN hostels h ON r.hostel_id = h.hostel_id 
                                             WHERE b.tenant_id = $uid 
                                             ORDER BY b.created_at DESC");

                            if ($res->num_rows > 0):
                                while ($b = $res->fetch_assoc()):
                                    $status = $b['booking_status'];
                                    // Status styling logic
                                    if ($status == 'approved') {
                                        $color = 'text-success';
                                        $bg = 'bg-success';
                                    } elseif ($status == 'rejected') {
                                        $color = 'text-danger';
                                        $bg = 'bg-danger';
                                    } else {
                                        $color = 'text-warning';
                                        $bg = 'bg-warning';
                                    }
                            ?>
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold text-dark"><?= $b['hname'] ?></div>
                                            <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> <?= $b['location'] ?></small>
                                            <div class="badge bg-light text-primary border mt-1" style="font-size: 0.7rem;"><?= $b['room_type'] ?></div>
                                        </td>
                                        <td><?= date('d M Y', strtotime($b['created_at'])) ?></td>
                                        <td class="fw-bold"><?= date('d M Y', strtotime($b['booking_date'])) ?></td>
                                        <td>
                                            <span class="d-flex align-items-center <?= $color ?> fw-bold">
                                                <span class="status-indicator <?= $bg ?>"></span>
                                                <?= ucfirst($status) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($status == 'pending'): ?>
                                                <a href="bookings.php?cancel_id=<?= $b['booking_id'] ?>"
                                                    class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                    onclick="return confirm('Are you sure you want to cancel this request?')">
                                                    Cancel
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">No actions</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">You haven't made any bookings yet. <a href="index.php">Search now</a>.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>