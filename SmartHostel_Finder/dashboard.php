<?php
require_once 'config.php';
require_once 'auth_check.php';

// 1. Protection: If not logged in, kick to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=login_required");
    exit();
}

$uid = $_SESSION['user_id'];
$role = $_SESSION['role'];
$name = $_SESSION['name'];

// --- 2. LANDLORD LOGIC: Handle Availability Toggle ---
if ($role == 'landlord' && isset($_POST['toggle_availability'])) {
    $hid = intval($_POST['hostel_id']);
    $booking_status = intval($_POST['current_booking_status']);
    $new_booking_status = ($booking_status == 1) ? 0 : 1;

    $stmt = $conn->prepare("UPDATE hostels SET is_available = ? WHERE hostel_id = ? AND landlord_id = ?");
    $stmt->bind_param("iii", $new_booking_status, $hid, $uid);
    $stmt->execute();
    header("Location: dashboard.php?msg=updated");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard | <?= $system_name ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <style>
        :root {
            --sidebar-width: 250px;
        }

        body {
            background: #f4f7f6;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #fff;
            border-right: 1px solid #eee;
            padding: 20px;
            z-index: 1000;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #555;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 5px;
            transition: 0.3s;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: #dc3545;
            color: white !important;
        }

        .sidebar-link i {
            width: 25px;
            font-size: 1.1rem;
        }

        /* Original Styles Kept */
        .card-custom {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: white;
        }

        .btn-round {
            border-radius: 50px;
            font-weight: 600;
        }

        .booking_status-pill {
            font-size: 0.75rem;
            padding: 5px 15px;
            border-radius: 50px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 10px;
            }

            .sidebar span,
            .sidebar .brand-text {
                display: none;
            }

            .main-content {
                margin-left: 70px;
            }
        }
    </style>
</head>

<body>

    <div class="main-content">

        <?php include 'navbar.php'; ?>

        <div class="sidebar">
            <div class="mb-4 px-2">
                <h5 class="fw-bold text-danger brand-text"><i class="fas fa-house-user me-2"></i>SmartHostel</h5>
            </div>

            <nav class="nav flex-column">
                <a href="dashboard.php" class="sidebar-link active">
                    <i class="fas fa-th-large"></i> <span>Dashboard</span>
                </a>

                <?php if ($role == 'admin'): ?>
                    <a href="manage_users.php" class="sidebar-link"><i class="fas fa-users"></i> <span>Manage Users</span></a>
                    <a href="manage_hostels.php" class="sidebar-link"><i class="fas fa-building"></i> <span>All Hostels</span></a>
                    <a href="reports.php" class="sidebar-link"><i class="fas fa-chart-line"></i> <span>Reports</span></a>

                <?php elseif ($role == 'landlord'): ?>
                    <a href="add_hostel.php" class="sidebar-link"><i class="fas fa-plus-circle"></i> <span>Add Hostel</span></a>
                    <a href="my_hostels.php" class="sidebar-link"><i class="fas fa-home"></i> <span>My Properties</span></a>
                    <a href="tenant_requests.php" class="sidebar-link"><i class="fas fa-envelope-open-text"></i> <span>Bookings</span></a>
                    <a href="manage_rooms.php" class="sidebar-link"><i class="fas fa-envelope-open-text"></i> <span> Manage Rooms</span></a>

                <?php else: ?>
                    <a href="find_hostels.php" class="sidebar-link"><i class="fas fa-search"></i> <span>Browse Hostels</span></a>
                    <a href="bookings.php" class="sidebar-link"><i class="fas fa-ticket-alt"></i> <span>My Bookings</span></a>
                    <a href="report_issue.php" class="sidebar-link"><i class="fas fa-tools"></i> <span>Maintenance</span></a>
                <?php endif; ?>

                <hr>
                <a href="profile.php" class="sidebar-link"><i class="fas fa-user-cog"></i> <span>Profile</span></a>
                <a href="logout.php" class="sidebar-link text-danger"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
            </nav>
        </div>

        <div class="container-fluid py-4">

            <div class="mb-5">
                <h2 class="fw-bold">Hello, <?= explode(' ', $name)[0] ?>!</h2>
                <p class="text-muted">Manage your <?= ($role == 'landlord') ? 'properties and bookings' : ($role == 'admin' ? 'entire system' : 'hostel stays and requests') ?> here.</p>
            </div>

            <?php if ($role == 'landlord'): ?>
                <div class="row g-4">
                    <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                        <h4 class="fw-bold m-0">My Hostels</h4>
                        <a href="add_hostel.php" class="btn btn-danger btn-round px-4 shadow-sm"><i class="fas fa-plus me-2"></i>New Listing</a>
                    </div>

                    <?php
                    $hostels = $conn->query("SELECT * FROM hostels WHERE landlord_id = $uid");
                    while ($h = $hostels->fetch_assoc()):
                        $bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE hostel_id = " . $h['hostel_id'])->fetch_assoc()['count'];
                    ?>
                        <div class="col-md-6 col-xxl-4">
                            <div class="card card-custom p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="fw-bold mb-0"><?= $h['name'] ?></h5>
                                    <span class="badge <?= ($h['booking_status'] == 'approved') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' ?> booking_status-pill">
                                        <?= strtoupper($h['booking_status'] ?? 'PENDING') ?>
                                    </span>
                                </div>
                                <p class="text-muted small mb-4"><i class="fas fa-map-marker-alt me-1"></i><?= $h['location'] ?></p>

                                <div class="bg-light p-3 rounded-4 mb-4 d-flex justify-content-around text-center">
                                    <div><small class="d-block text-muted">Price</small><b><?= ($h['price_range']) ?></b></div>
                                    <div><small class="d-block text-muted">Bookings</small><b><?= $bookings ?></b></div>
                                </div>

                                <form method="POST" class="d-grid">
                                    <input type="hidden" name="hostel_id" value="<?= $h['hostel_id'] ?>">
                                    <input type="hidden" name="current_booking_status" value="<?= $h['is_available'] ?>">
                                    <button type="submit" name="toggle_availability" class="btn btn-round <?= $h['is_available'] ? 'btn-outline-danger' : 'btn-outline-success' ?> btn-sm">
                                        <i class="fas fa-power-off me-2"></i> Mark as <?= $h['is_available'] ? 'Full' : 'Available' ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

            <?php elseif ($role == 'admin'): ?>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card card-custom p-4 text-center">
                            <h2 class="fw-bold">0</h2>
                            <p class="text-muted mb-0">Total Users</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-custom p-4 text-center">
                            <h2 class="fw-bold">0</h2>
                            <p class="text-muted mb-0">Total Hostels</p>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="row g-4">
                    <div class="col-lg-8">
                        <h4 class="fw-bold mb-4">My Bookings</h4>
                        <?php
                        $my_bookings = $conn->query("SELECT b.*, h.name, h.location FROM bookings b JOIN hostels h ON b.hostel_id = h.hostel_id WHERE b.tenant_id = $uid");
                        if ($my_bookings->num_rows > 0):
                            while ($b = $my_bookings->fetch_assoc()):
                        ?>
                                <div class="card card-custom p-3 mb-3 d-flex flex-row align-items-center">
                                    <div class="flex-grow-1 ps-3">
                                        <h6 class="fw-bold mb-1"><?= $b['name'] ?></h6>
                                        <small class="text-muted"><i class="fas fa-receipt me-1"></i> ID: <?= $b['transaction_id'] ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-<?= ($b['booking_status'] == 'confirmed') ? 'success' : 'warning' ?> booking_status-pill d-block mb-1">
                                            <?= strtoupper($b['booking_status']) ?>
                                        </span>
                                        <small class="text-muted"><?= date('d M Y', strtotime($b['created_at'])) ?></small>
                                    </div>
                                </div>
                            <?php endwhile;
                        else: ?>
                            <div class="card card-custom p-5 text-center">
                                <p class="text-muted">You haven't made any bookings yet.</p>
                                <a href="index.php" class="btn btn-danger btn-round px-4 d-inline-block">Find a Hostel</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-4">
                        <h4 class="fw-bold mb-4">Quick Actions</h4>
                        <div class="card card-custom p-4 mb-4 bg-dark text-white">
                            <h6>Report a Maintenance Issue</h6>
                            <p class="small opacity-75">Broken light? Leaking tap? Let your landlord know.</p>
                            <a href="report_issue.php" class="btn btn-light btn-round w-100 btn-sm fw-bold">Report Now</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        <?php include 'footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>