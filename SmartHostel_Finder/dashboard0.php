<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=login_required");
    exit();
}

$uid  = $_SESSION['user_id'];
$role = $_SESSION['role']; // 'admin', 'landlord', or 'tenant'
$name = $_SESSION['name'];

// --- LANDLORD LOGIC: Handle Availability Toggle ---
if ($role == 'landlord' && isset($_POST['toggle_availability'])) {
    $hid = intval($_POST['hostel_id']);
    $status = intval($_POST['current_booking_status']);
    $new_status = ($status == 1) ? 0 : 1;

    $stmt = $conn->prepare("UPDATE hostels SET is_available = ? WHERE hostel_id = ? AND landlord_id = ?");
    $stmt->bind_param("iii", $new_status, $hid, $uid);
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
            --sidebar-width: 260px;
            --primary-red: #dc3545;
            --bg-light: #f8f9fa;
        }

        body {
            background: var(--bg-light);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        #wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            background: #fff;
            border-right: 1px solid #e0e0e0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            text-align: center;
        }

        .nav-link {
            padding: 12px 20px;
            color: #555;
            font-weight: 500;
            display: flex;
            align-items: center;
            border-radius: 10px;
            margin: 4px 15px;
            transition: all 0.3s;
        }

        .nav-link:hover {
            background: #fff5f5;
            color: var(--primary-red);
        }

        .nav-link.active {
            background: var(--primary-red);
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
        }

        .nav-link i {
            width: 25px;
            font-size: 1.1rem;
        }

        /* Content Styling */
        #main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 2rem;
        }

        .card-stats {
            border: none;
            border-radius: 15px;
            transition: transform 0.2s;
        }

        .card-stats:hover {
            transform: translateY(-5px);
        }

        .role-badge {
            font-size: 0.7rem;
            letter-spacing: 1px;
            padding: 4px 10px;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div id="wrapper">
        <!-- <nav id="sidebar">
            <div class="sidebar-header">
                <h4 class="fw-bold text-danger mb-0">SmartHostel</h4>
                <span class="badge bg-dark role-badge mt-2"><?= strtoupper($role) ?></span>
            </div>

            <div class="nav flex-column">
                <a href="dashboard.php" class="nav-link active"><i class="fas fa-home"></i> Overview</a>

                <?php if ($role == 'admin'): ?>
                    <div class="px-4 py-2 small text-muted text-uppercase fw-bold">Management</div>
                    <a href="manage_users.php" class="nav-link"><i class="fas fa-users"></i> All Users</a>
                    <a href="manage_hostels.php" class="nav-link"><i class="fas fa-hotel"></i> All Hostels</a>
                    <a href="pending_approvals.php" class="nav-link"><i class="fas fa-check-circle"></i> Approvals</a>
                    <a href="system_settings.php" class="nav-link"><i class="fas fa-cogs"></i> Settings</a>

                <?php elseif ($role == 'landlord'): ?>
                    <div class="px-4 py-2 small text-muted text-uppercase fw-bold">Property</div>
                    <a href="my_hostels.php" class="nav-link"><i class="fas fa-building"></i> My Hostels</a>
                    <a href="bookings.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Bookings</a>
                    <a href="payments.php" class="nav-link"><i class="fas fa-wallet"></i> Earnings</a>

                <?php else: ?>
                    <div class="px-4 py-2 small text-muted text-uppercase fw-bold">Student Hub</div>
                    <a href="explore.php" class="nav-link"><i class="fas fa-search"></i> Find Hostels</a>
                    <a href="my_stay.php" class="nav-link"><i class="fas fa-key"></i> My Stay</a>
                    <a href="tickets.php" class="nav-link"><i class="fas fa-tools"></i> Maintenance</a>
                <?php endif; ?>

                <div class="mt-auto border-top pt-3">
                    <a href="profile.php" class="nav-link"><i class="fas fa-user-circle"></i> Profile</a>
                    <a href="logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </nav> -->

        <main id="main-content">
            <header class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="fw-bold mb-0">Welcome back, <?= explode(' ', $name)[0] ?>!</h2>
                    <p class="text-muted">Here is what's happening today.</p>
                </div>
                <div class="dropdown">
                    <button class="btn btn-white shadow-sm border-0 rounded-pill px-3 py-2 dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name=<?= $name ?>&background=dc3545&color=fff" class="rounded-circle me-2" width="30">
                        <span class="fw-semibold"><?= $name ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                        <li><a class="dropdown-item" href="profile.php">Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </header>

            <div class="row g-4">
                <?php if ($role == 'admin'): ?>
                    <div class="col-md-3">
                        <div class="card card-stats p-4 bg-white shadow-sm">
                            <small class="text-muted text-uppercase">Total Hostels</small>
                            <h3 class="fw-bold">42</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stats p-4 bg-white shadow-sm">
                            <small class="text-muted text-uppercase">Active Users</small>
                            <h3 class="fw-bold">156</h3>
                        </div>
                    </div>
                <?php elseif ($role == 'landlord'): ?>
                    <div class="col-12">
                        <h4 class="fw-bold mb-3">Your Properties</h4>
                    </div>

                <?php else: ?>
                    <div class="col-lg-8">
                        <h4 class="fw-bold mb-3">My Current Booking</h4>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>