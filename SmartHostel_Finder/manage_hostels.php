<?php
require_once 'config.php';
require_once 'auth_check.php';

// 1. Access Control: Admins Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?msg=unauthorized");
    exit();
}

// --- 2. HANDLE DELETE/booking_status ACTIONS ---
if (isset($_GET['delete_id'])) {
    $did = intval($_GET['delete_id']);
    $conn->query("DELETE FROM hostels WHERE hostel_id = $did");
    header("Location: manage_hostels.php?msg=hostel_deleted");
    exit();
}

// --- 3. FETCH & FILTER LOGIC ---
$filter_booking_status = isset($_GET['booking_status']) ? clean($_GET['booking_status']) : '';
$search = isset($_GET['search']) ? clean($_GET['search']) : '';

$query = "SELECT h.*, u.full_name as landlord_name 
          FROM hostels h 
          JOIN users u ON h.landlord_id = u.user_id WHERE 1=1";

if ($filter_booking_status) $query .= " AND h.status = '$filter_booking_status'";
if ($search) $query .= " AND (h.name LIKE '%$search%' OR h.location LIKE '%$search%')";

$query .= " ORDER BY h.hostel_id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Hostel Directory | <?= $system_name ?></title>
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

        .admin-card {
            border: none;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .filter-pill {
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid #eee;
            background: white;
            color: #666;
            text-decoration: none;
            transition: 0.3s;
        }

        .filter-pill.active {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        .booking_status-badge {
            font-size: 0.7rem;
            padding: 5px 12px;
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
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h2 class="fw-800 mb-1">Hostel Directory</h2>
                <p class="text-muted mb-0">Total Listings: <?= $result->num_rows ?></p>
            </div>
            <div class="d-flex gap-2">
                <a href="manage_hostels.php" class="filter-pill <?= !$filter_booking_status ? 'active' : '' ?>">All</a>
                <a href="manage_hostels.php?booking_status=pending" class="filter-pill <?= $filter_booking_status == 'pending' ? 'active' : '' ?>">Pending</a>
                <a href="manage_hostels.php?booking_status=approved" class="filter-pill <?= $filter_booking_status == 'approved' ? 'active' : '' ?>">Approved</a>
            </div>
        </div>

        <div class="card admin-card p-3 mb-4">
            <form action="manage_hostels.php" method="GET" class="row g-2">
                <input type="hidden" name="booking_status" value="<?= $filter_booking_status ?>">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control border-0 bg-light rounded-3" placeholder="Search by hostel name or location..." value="<?= $search ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 rounded-3">Filter</button>
                </div>
            </form>
        </div>

        <div class="card admin-card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 px-3">Hostel</th>
                            <th class="border-0">Landlord</th>
                            <th class="border-0">Location</th>
                            <th class="border-0">Price Range</th>
                            <th class="border-0">status</th>
                            <th class="border-0 text-end px-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($h = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="px-3">
                                    <div class="fw-bold text-dark"><?= $h['name'] ?></div>
                                    <div class="text-muted small">ID: #HST-<?= $h['hostel_id'] ?></div>
                                </td>
                                <td><span class="small"><?= $h['landlord_name'] ?></span></td>
                                <td><span class="small"><?= $h['location'] ?></span></td>
                                <td><span class="fw-bold text-primary small"><?= ($h['price_range']) ?></span></td>
                                <td>
                                    <?php
                                    $st = $h['status'];
                                    $color = ($st == 'approved') ? 'success' : ($st == 'pending' ? 'warning' : 'danger');
                                    ?>
                                    <span class="status-badge bg-<?= $color ?>-subtle text-<?= $color ?>">
                                        <?= strtoupper($st) ?>
                                    </span>
                                </td>
                                <td class="text-end px-3">
                                    <a href="details.php?id=<?= $h['hostel_id'] ?>" class="btn btn-sm btn-light rounded-pill me-1" title="View Detail"><i class="fas fa-eye"></i></a>
                                    <a href="edit_hostel.php?id=<?= $h['hostel_id'] ?>" class="btn btn-sm btn-light rounded-pill me-1"><i class="fas fa-pen"></i></a>
                                    <button onclick="confirmDelete(<?= $h['hostel_id'] ?>)" class="btn btn-sm btn-light text-danger rounded-pill"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure? Deleting this hostel will also remove all associated reviews and bookings.')) {
                window.location.href = 'manage_hostels.php?delete_id=' + id;
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>