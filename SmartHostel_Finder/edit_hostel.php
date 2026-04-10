<?php
include('config.php');     // Database connection
include('auth_check.php'); // Security logic

// 1. Admin Security Check
protect_page(['admin']);

$message = "";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. Handle Admin Update (Approval/Rejection)
if (isset($_POST['update_hostel'])) {
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $admin_note = mysqli_real_escape_string($conn, $_POST['admin_note']);

    $update_sql = "UPDATE hostels SET status = '$status', description = CONCAT(description, '\n\nAdmin Note: $admin_note') WHERE hostel_id = $id";

    if ($conn->query($update_sql)) {
        $message = "<div class='alert alert-success shadow-sm'>Hostel status updated to <strong>$status</strong>.</div>";
    }
}

// 3. Fetch Hostel Data with Landlord Info
$res = $conn->query("SELECT h.*, u.full_name, u.phone FROM hostels h JOIN users u ON h.landlord_id = u.user_id WHERE h.hostel_id = $id");
$h = $res->fetch_assoc();

if (!$h) {
    die("Hostel not found.");
}

// 4. Fetch Linked Image from 'images' table
$img_res = $conn->query("SELECT image_path FROM images WHERE hostel_id = $id AND is_thumbnail = 1 LIMIT 1");
$img = $img_res->fetch_assoc();
$display_img = ($img) ? $img['image_path'] : 'img/default.jpg';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Review Hostel | Admin Panel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
</head>

<body class="bg-light">
    <?php include('navbar.php'); ?>


    <div class="container py-5">
        <a href="dashboard.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="fas fa-arrow-left"></i> Back to List</a>

        <?= $message ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <img src="<?= $display_img ?>" class="img-fluid" style="height: 350px; width: 100%; object-fit: cover;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <h2 class="fw-bold"><?= htmlspecialchars($h['name']) ?></h2>
                            <span class="badge bg-<?= ($h['status'] == 'approved' ? 'success' : ($h['status'] == 'pending' ? 'warning' : 'danger')) ?> p-2 px-3">
                                <?= strtoupper($h['status']) ?>
                            </span>
                        </div>
                        <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($h['location']) ?></p>
                        <hr>

                        <h6 class="fw-bold">Landlord Information</h6>
                        <p class="mb-1 text-secondary">Name: <strong><?= $h['full_name'] ?></strong></p>
                        <p class="text-secondary">Contact: <strong><?= $h['phone'] ?></strong></p>

                        <hr>
                        <h6 class="fw-bold">Description Provided</h6>
                        <p class="text-muted small"><?= nl2br(htmlspecialchars($h['description'])) ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 sticky-top" style="top: 20px;">
                    <h5 class="fw-bold mb-4 text-center">Moderation Action</h5>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold mb-2">Change Status</label>
                            <select name="status" class="form-select border-0 bg-light py-2">
                                <option value="pending" <?= ($h['status'] == 'pending' ? 'selected' : '') ?>>Pending Verification</option>
                                <option value="approved" <?= ($h['status'] == 'approved' ? 'selected' : '') ?>>Approve Listing</option>
                                <option value="rejected" <?= ($h['status'] == 'rejected' ? 'selected' : '') ?>>Reject Listing</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="small fw-bold mb-2">Internal Notes / Reason</label>
                            <textarea name="admin_note" class="form-control border-0 bg-light" rows="4" placeholder="e.g., Photos are clear, verified location."></textarea>
                            <div class="form-text mt-2 small text-danger">Notes will be visible to the Landlord.</div>
                        </div>

                        <button type="submit" name="update_hostel" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include('footer.php'); ?>
    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>