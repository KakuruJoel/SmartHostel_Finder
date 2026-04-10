<?php
require_once 'config.php';

// 1. Access Control: Landlords Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: login.php?msg=unauthorized");
    exit();
}

$uid = $_SESSION['user_id'];

// 2. Handle "Mark as Resolved"
if (isset($_GET['resolve_id'])) {
    $rid = intval($_GET['resolve_id']);
    // Security: Ensure landlord owns the hostel before resolving
    $conn->query("UPDATE maintenance m JOIN hostels h ON m.hostel_id = h.hostel_id SET m.booking_status = 'resolved' WHERE m.maintenance_id = $rid AND h.landlord_id = $uid");
    header("Location: landlord_maintenance.php?msg=resolved");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Maintenance Manager | SmartHostel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <style>
        body {
            background: #f0f2f5;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .issue-card {
            border: none;
            border-radius: 20px;
            transition: 0.3s;
        }

        .issue-card:hover {
            transform: translateY(-5px);
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-resolved {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <div class="mb-5">
            <h2 class="fw-800 mb-1">Maintenance Requests</h2>
            <p class="text-muted">Track and resolve issues reported by your tenants.</p>
        </div>

        <div class="row g-4">
            <?php
            $query = "SELECT m.*, h.name, u.full_name as tenant_name 
                  FROM maintenance m 
                  JOIN hostels h ON m.hostel_id = h.hostel_id 
                  JOIN users u ON m.tenant_id = u.user_id 
                  WHERE h.landlord_id = $uid 
                  ORDER BY m.booking_status ASC, m.created_at DESC";
            $res = $conn->query($query);

            if ($res->num_rows > 0):
                while ($row = $res->fetch_assoc()):
            ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card issue-card h-100 shadow-sm p-4 bg-white">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="badge rounded-pill px-3 py-2 <?= ($row['booking_status'] == 'pending') ? 'badge-pending' : 'badge-resolved' ?>">
                                    <?= strtoupper($row['booking_status']) ?>
                                </span>
                                <small class="text-muted"><?= date('d M', strtotime($row['created_at'])) ?></small>
                            </div>

                            <h6 class="fw-bold mb-1"><?= $row['issue_type'] ?></h6>
                            <p class="text-muted small mb-3">Hostel: <b><?= $row['name'] ?></b></p>

                            <div class="bg-light p-3 rounded-4 mb-4 flex-grow-1">
                                <p class="small mb-0 text-dark">"<?= htmlspecialchars($row['description']) ?>"</p>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                <small class="text-muted">By: <?= $row['tenant_name'] ?></small>
                                <?php if ($row['booking_status'] == 'pending'): ?>
                                    <a href="landlord_maintenance.php?resolve_id=<?= $row['maintenance_id'] ?>" class="btn btn-success btn-sm rounded-pill px-3 fw-bold">Resolve</a>
                                <?php else: ?>
                                    <i class="fas fa-check-double text-success"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-tools fa-3x text-light mb-3"></i>
                    <h5 class="text-muted">Everything is looking good! No pending issues.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>