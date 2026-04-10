<?php
require_once 'config.php';

// 1. Access Control: Strictly Admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?msg=unauthorized");
    exit();
}

// --- 2. ANALYTICS QUERIES ---
// Total Revenue from confirmed bookings
$rev_query = $conn->query("SELECT SUM(amount) as total FROM bookings WHERE booking_status='confirmed'");
$total_revenue = $rev_query->fetch_assoc()['total'] ?? 0;

// Count Pending Hostels
$pend_query = $conn->query("SELECT COUNT(*) as count FROM hostels WHERE status='pending'");
$pending_count = $pend_query->fetch_assoc()['count'];

// Count Total Users
$user_query = $conn->query("SELECT COUNT(*) as count FROM users WHERE role != 'admin'");
$total_users = $user_query->fetch_assoc()['count'];

// --- 3. HANDLE HOSTEL APPROVAL/REJECTION ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = ($_GET['action'] == 'approve') ? 'approved' : 'rejected';

    $stmt = $conn->prepare("UPDATE hostels SET booking_status = ? WHERE hostel_id = ?");
    $stmt->bind_param("si", $action, $id);
    $stmt->execute();
    header("Location: admin.php?msg=booking_status_updated");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel | <?= $system_name ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <style>
        body {
            background: #f0f2f5;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .main-content {
            margin-left: 260px;
            padding: 40px;
            transition: 0.3s;
        }

        .stat-card {
            border: none;
            border-radius: 20px;
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .table-card {
            border: none;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .btn-approve {
            background: #198754;
            color: white;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            #sidebar {
                display: none;
            }

            /* Use a toggle for mobile in production */
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-800 mb-1">System Overview</h2>
                <p class="text-muted mb-0">Welcome back, Super Admin.</p>
            </div>
            <div class="text-end">
                <span class="badge bg-white text-dark shadow-sm py-2 px-3 rounded-pill border">
                    <i class="fas fa-calendar-alt me-2 text-danger"></i><?= date('D, d M Y') ?>
                </span>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card stat-card bg-primary text-white p-4 shadow-sm">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-uppercase opacity-75 fw-bold">Confirmed Revenue</small>
                            <h2 class="fw-800 mt-2"><?= formatMoney($total_revenue) ?></h2>
                        </div>
                        <i class="fas fa-wallet fa-2x opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-danger text-white p-4 shadow-sm">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-uppercase opacity-75 fw-bold">Pending Hostels</small>
                            <h2 class="fw-800 mt-2"><?= $pending_count ?> Listings</h2>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-dark text-white p-4 shadow-sm">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-uppercase opacity-75 fw-bold">Platform Users</small>
                            <h2 class="fw-800 mt-2"><?= $total_users ?> Members</h2>
                        </div>
                        <i class="fas fa-users fa-2x opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0"><i class="fas fa-clipboard-check me-2 text-danger"></i>Waitlist for Approval</h5>
                <a href="manage_hostels.php" class="text-muted small text-decoration-none">View All <i class="fas fa-chevron-right ms-1"></i></a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 rounded-start px-3">Hostel Name</th>
                            <th class="border-0">Landlord</th>
                            <th class="border-0">Location</th>
                            <th class="border-0">Price</th>
                            <th class="border-0 text-end rounded-end px-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT h.*, u.full_name FROM hostels h JOIN users u ON h.landlord_id = u.user_id WHERE h.status = 'pending'");
                        if ($res->num_rows > 0):
                            while ($h = $res->fetch_assoc()):
                        ?>
                                <tr>
                                    <td class="fw-bold px-3"><?= $h['name'] ?></td>
                                    <td><?= $h['full_name'] ?></td>
                                    <td><span class="badge bg-light text-dark fw-normal"><?= $h['location'] ?></span></td>
                                    <td class="text-primary fw-bold"><?= formatMoney($h['price_range']) ?></td>
                                    <td class="text-end px-3">
                                        <a href="admin.php?action=approve&id=<?= $h['hostel_id'] ?>" class="btn btn-approve btn-sm px-3 me-2">Approve</a>
                                        <a href="admin.php?action=reject&id=<?= $h['hostel_id'] ?>" class="btn btn-reject btn-sm px-3">Reject</a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted small italic">No pending hostels to review. You're all caught up!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>