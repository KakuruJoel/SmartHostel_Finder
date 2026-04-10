<?php
require_once 'config.php';
require_once 'auth_check.php';

// 1. Access Control: Only Landlords
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: login.php?msg=unauthorized");
    exit();
}

$uid = $_SESSION['user_id'];

// --- 2. HANDLE DELETE ACTION ---
if (isset($_GET['delete_id'])) {
    $did = intval($_GET['delete_id']);
    // Security: Ensure the landlord deleting it actually owns it
    $stmt = $conn->prepare("DELETE FROM hostels WHERE hostel_id = ? AND landlord_id = ?");
    $stmt->bind_param("ii", $did, $uid);
    if ($stmt->execute()) {
        header("Location: my_hostels.php?msg=deleted");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Properties | <?= $system_name ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #eee;
        }

        .hostel-item {
            border: none;
            border-radius: 20px;
            transition: 0.3s;
            background: white;
        }

        .hostel-item:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .btn-action {
            border-radius: 10px;
            padding: 8px 15px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .booking_status-dot {
            height: 10px;
            width: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <!-- <nav class="navbar sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
                <img src="project_logo.png" height="40" class="me-2 rounded-circle border">
                <span class="text-dark">SmartHostel </span>
            </a>
            <a href="add_hostel.php" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                <i class="fas fa-plus me-2"></i> Add New
            </a>
        </div>
    </nav> -->

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-800 mb-0">My Listed Hostels</h2>
            <span class="badge bg-dark rounded-pill px-3 py-2">Total:
                <?php echo $conn->query("SELECT COUNT(*) FROM hostels WHERE landlord_id = $uid")->fetch_row()[0]; ?>
            </span>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-success border-0 rounded-4 py-2 small mb-4">
                <i class="fas fa-check-circle me-2"></i> Hostel listing removed successfully.
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php
            $res = $conn->query("SELECT * FROM hostels WHERE landlord_id = $uid ORDER BY hostel_id DESC");
            if ($res->num_rows > 0):
                while ($h = $res->fetch_assoc()):
                    // Logic for booking_status styling
                    $booking_status_class = ($h['status'] == 'approved') ? 'text-success' : (($h['status'] == 'rejected') ? 'text-danger' : 'text-warning');
                    $booking_status_bg = ($h['status'] == 'approved') ? 'bg-success' : (($h['status'] == 'rejected') ? 'bg-danger' : 'bg-warning');
            ?>
                    <div class="col-12">
                        <div class="card hostel-item p-4 shadow-sm border-0">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <h5 class="fw-bold mb-1 text-dark"><?= $h['name'] ?></h5>
                                    <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt me-1 text-danger"></i> <?= $h['location'] ?></p>
                                    <div class="mt-2">
                                        <span class="booking_status-dot <?= $booking_status_bg ?>"></span>
                                        <small class="fw-bold <?= $booking_status_class ?>"><?= strtoupper($h['status']) ?></small>
                                    </div>
                                </div>

                                <div class="col-md-3 mt-3 mt-md-0">
                                    <div class="small text-muted mb-1">Price / Availability</div>
                                    <div class="fw-bold text-primary"><?= formatMoney($h['price_range']) ?></div>
                                    <span class="badge <?= $h['is_available'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> rounded-pill mt-1" style="font-size: 0.65rem;">
                                        <?= $h['is_available'] ? 'OPEN FOR BOOKING' : 'FULLY BOOKED' ?>
                                    </span>
                                </div>

                                <div class="col-md-2 mt-3 mt-md-0 text-md-center">
                                    <?php
                                    $rev_avg = $conn->query("SELECT AVG(rating) FROM reviews WHERE hostel_id = " . $h['hostel_id'])->fetch_row()[0];
                                    ?>
                                    <div class="small text-muted mb-1">Rating</div>
                                    <div class="fw-bold text-dark"><i class="fas fa-star text-warning me-1"></i><?= round($rev_avg, 1) ?: 'N/A' ?></div>
                                </div>

                                <div class="col-md-3 mt-3 mt-md-0 text-md-end">
                                    <a href="edit_hostel.php?id=<?= $h['hostel_id'] ?>" class="btn btn-light btn-action text-dark me-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="details.php?id=<?= $h['hostel_id'] ?>" class="btn btn-light btn-action text-dark me-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="confirmDelete(<?= $h['hostel_id'] ?>)" class="btn btn-outline-danger btn-action">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            else: ?>
                <div class="col-12 text-center py-5">
                    <div class="card card-custom p-5 border-0 shadow-sm rounded-4">
                        <i class="fas fa-hotel fa-3x text-light mb-3"></i>
                        <h4 class="text-muted">You haven't listed any hostels yet.</h4>
                        <a href="add_hostel.php" class="btn btn-danger btn-round px-4 mt-3 shadow-sm">Post Your First Hostel</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to permanently delete this listing? This action cannot be undone.')) {
                window.location.href = 'my_hostels.php?delete_id=' + id;
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>