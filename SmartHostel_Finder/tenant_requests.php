<?php
include('config.php');

// Security: Landlord Only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'landlord') {
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// --- Handle Status Update (Mark as Resolved) ---
if (isset($_GET['resolve_id'])) {
    $res_id = intval($_GET['resolve_id']);
    // Ensure the request belongs to a hostel owned by this landlord
    $update = "UPDATE maintenance_requests SET status = 'resolved' WHERE request_id = $res_id";
    if ($conn->query($update)) {
        $msg = "<div class='alert alert-success border-0 shadow-sm'>Request marked as resolved!</div>";
    }
}

// --- Fetch Requests ---
// Joins maintenance_requests with rooms and hostels to filter by landlord_id
$sql = "SELECT m.*, r.room_id, h.name as hostel_name, u.full_name as tenant_name 
        FROM maintenance_issues m
        JOIN rooms r ON m.room_id = r.room_id
        JOIN hostels h ON r.hostel_id = h.hostel_id
        JOIN users u ON m.tenant_id = u.user_id
        WHERE h.landlord_id = $user_id 
        ORDER BY m.status DESC, m.created_at DESC";
$requests = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tenant Requests | SmartHostel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        .request-card {
            border: none;
            border-radius: 12px;
            transition: 0.3s;
        }

        .request-card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 5px 12px;
            border-radius: 50px;
        }

        .sidebar-link.active {
            background: #0d6efd;
            color: white;
        }
    </style>
</head>

<body class="bg-light">

    <div class="d-flex">
        <?php include('sidebar.php'); ?>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-0">Maintenance Requests</h3>
                    <p class="text-muted small">Manage and resolve issues reported by your tenants.</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary rounded-pill p-2 px-3">
                        <?= $requests->num_rows ?> Total Requests
                    </span>
                </div>
            </div>

            <?= $msg ?>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Tenant & Room</th>
                                <th>Hostel</th>
                                <th>Issue Description</th>
                                <th>Date Reported</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($requests->num_rows > 0): ?>
                                <?php while ($row = $requests->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold"><?= $row['tenant_name'] ?></div>
                                            <small class="text-muted">Room: <?= $row['room_number'] ?></small>
                                        </td>
                                        <td><?= $row['hostel_name'] ?></td>
                                        <td>
                                            <p class="mb-0 text-truncate" style="max-width: 200px;" title="<?= $row['description'] ?>">
                                                <?= $row['description'] ?>
                                            </p>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                        <td>
                                            <?php if ($row['status'] == 'pending'): ?>
                                                <span class="badge bg-warning-subtle text-warning status-badge">Pending</span>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success status-badge">Resolved</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <?php if ($row['status'] == 'pending'): ?>
                                                <a href="tenant_requests.php?resolve_id=<?= $row['request_id'] ?>"
                                                    class="btn btn-sm btn-success rounded-pill px-3"
                                                    onclick="return confirm('Mark this issue as resolved?')">
                                                    Mark Resolved
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-light rounded-pill px-3" disabled>
                                                    <i class="fas fa-check"></i> Done
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-tasks fa-3x mb-3 opacity-25"></i>
                                        <p>No maintenance requests found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>