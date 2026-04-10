<?php
require_once 'config.php';

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
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        body {
            background: #f4f7f6;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .nav-dash {
            background: white;
            border-bottom: 1px solid #eee;
        }

        .card-custom {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
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
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <!-- <nav class="navbar nav-dash sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <img src="project_logo.png" height="40" class="me-2 rounded-circle border">
                <span>SmartHostel </span>
            </a>
            <div class="dropdown">
                <button class="btn btn-light btn-round dropdown-toggle shadow-sm" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle me-1"></i> <?= $name ?> (<?= ucfirst($role) ?>)
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                    <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav> -->

    <div class="container py-5">

        <div class="mb-5">
            <h2 class="fw-bold">Hello, <?= explode(' ', $name)[0] ?>!</h2>
            <p class="text-muted">Manage your <?= ($role == 'landlord') ? 'properties and bookings' : 'hostel stays and requests' ?> here.</p>
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
                    // Get stats for this hostel
                    $bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE hostel_id = " . $h['hostel_id'])->fetch_assoc()['count'];
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-custom p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <h5 class="fw-bold mb-0"><?= $h['name'] ?></h5>
                                <span class="badge <?= ($h['booking_status'] == 'approved') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' ?> booking_status-pill">
                                    <?= strtoupper($h['booking_status']) ?>
                                </span>
                            </div>
                            <p class="text-muted small mb-4"><i class="fas fa-map-marker-alt me-1"></i><?= $h['location'] ?></p>

                            <div class="bg-light p-3 rounded-4 mb-4 d-flex justify-content-around text-center">
                                <div><small class="d-block text-muted">Price</small><b><?= number_format($h['price']) ?></b></div>
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

                    <div class="card card-custom p-4 bg-white">
                        <h6 class="fw-bold">Help & Support</h6>
                        <p class="small text-muted">Need help with your booking?</p>
                        <a href="mailto:<?= $admin_email ?>" class="btn btn-outline-dark btn-round w-100 btn-sm fw-bold">Contact Admin</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>