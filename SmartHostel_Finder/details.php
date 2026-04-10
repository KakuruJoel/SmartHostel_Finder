<?php
require_once 'config.php';
require_once 'auth_check.php';

// 1. Fetch Hostel ID from URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$hid = intval($_GET['id']);
$uid = $_SESSION['user_id'] ?? null;

// 2. Fetch Hostel & Landlord Details
$query = "SELECT h.*, u.full_name as landlord_name, u.phone as landlord_phone 
          FROM hostels h 
          JOIN users u ON h.landlord_id = u.user_id 
          WHERE h.hostel_id = $hid AND h.status = 'approved'";
$res = $conn->query($query);

if ($res->num_rows == 0) {
    echo "<div class='text-center py-5'><h3>Hostel not found or pending approval.</h3><a href='index.php'>Go Back</a></div>";
    exit();
}

$h = $res->fetch_assoc();

// 3. Handle Review Submission
if (isset($_POST['submit_review']) && $uid) {
    $rating = intval($_POST['rating']);
    $comment = clean($_POST['comment']);

    // Check if user already reviewed to prevent spam
    $check = $conn->query("SELECT review_id FROM reviews WHERE hostel_id = $hid AND tenant_id = $uid");
    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO reviews (hostel_id, tenant_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $hid, $uid, $rating, $comment);
        $stmt->execute();
        header("Location: details.php?id=$hid&msg=review_added");
        exit();
    }
}

// 4. Calculate Rating Stats
$stats = $conn->query("SELECT AVG(rating) as avg, COUNT(*) as total FROM reviews WHERE hostel_id = $hid")->fetch_assoc();
$avg_score = round($stats['avg'], 1) ?: "No ratings";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $h['name'] ?> | SmartHostel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .details-container {
            background: white;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        }

        .sidebar-card {
            border: none;
            border-radius: 20px;
            position: sticky;
            top: 100px;
        }

        .review-item {
            border-bottom: 1px solid #eee;
            padding: 20px 0;
        }

        .star-active {
            color: #ffc107;
        }

        .star-inactive {
            color: #e0e0e0;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <!-- <nav class="navbar navbar-light bg-white border-bottom py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <img src="project_logo.png" height="40" class="me-2 rounded-circle border">
                <span>SmartHostel <span class="text-danger">FINDER</span></span>
            </a>
        </div>
    </nav> -->

    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="details-container p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="fw-800 mb-1"><?= $h['name'] ?></h1>
                            <p class="text-muted fs-5"><i class="fas fa-map-marker-alt text-danger me-2"></i><?= $h['location'] ?></p>
                        </div>
                        <div class="text-end">
                            <div class="h4 fw-bold mb-0 text-warning"><i class="fas fa-star me-1"></i><?= $avg_score ?></div>
                            <small class="text-muted"><?= $stats['total'] ?> Reviews</small>
                        </div>
                    </div>

                    <hr class="my-4 opacity-50">

                    <h5 class="fw-bold mb-3">About this Hostel</h5>
                    <p class="text-secondary mb-5" style="line-height: 1.8;"><?= nl2br($h['description']) ?></p>

                    <h5 class="fw-bold mb-4">Student Reviews</h5>
                    <div class="review-list">
                        <?php
                        $reviews = $conn->query("SELECT r.*, u.full_name FROM reviews r JOIN users u ON r.tenant_id = u.user_id WHERE r.hostel_id = $hid ORDER BY r.created_at DESC");
                        if ($reviews->num_rows > 0):
                            while ($rev = $reviews->fetch_assoc()):
                        ?>
                                <div class="review-item">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-bold small"><?= $rev['full_name'] ?></span>
                                        <div>
                                            <?php for ($i = 1; $i <= 5; $i++) echo ($i <= $rev['rating']) ? '<i class="fas fa-star star-active"></i>' : '<i class="far fa-star star-inactive"></i>'; ?>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-0">"<?= htmlspecialchars($rev['comment']) ?>"</p>
                                </div>
                            <?php endwhile;
                        else: ?>
                            <p class="text-muted italic small">No reviews yet for this hostel.</p>
                        <?php endif; ?>
                    </div>

                    <?php if ($uid && $_SESSION['role'] == 'tenant'): ?>
                        <div class="mt-5 p-4 bg-light rounded-4">
                            <h6 class="fw-bold">Leave a Review</h6>
                            <form method="POST">
                                <div class="mb-3">
                                    <select name="rating" class="form-select form-select-sm border-0 shadow-sm" required>
                                        <option value="5">5 Stars - Excellent</option>
                                        <option value="4">4 Stars - Very Good</option>
                                        <option value="3">3 Stars - Average</option>
                                        <option value="2">2 Stars - Poor</option>
                                        <option value="1">1 Star - Terrible</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <textarea name="comment" class="form-control border-0 shadow-sm" rows="3" placeholder="How was your stay? Security? Water? WiFi?" required></textarea>
                                </div>
                                <button type="submit" name="submit_review" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold">Post Review</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card sidebar-card shadow p-4">
                    <h6 class="text-uppercase small fw-bold text-muted mb-2">Price Per Semester</h6>
                    <h2 class="fw-800 text-primary mb-4"><?= formatMoney($h['price']) ?></h2>

                    <div class="mb-4">
                        <small class="text-muted d-block mb-1">Managed By</small>
                        <div class="d-flex align-items-center">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; font-weight: bold;">
                                <?= substr($h['landlord_name'], 0, 1) ?>
                            </div>
                            <span class="fw-bold small"><?= $h['landlord_name'] ?></span>
                        </div>
                    </div>

                    <?php if ($h['is_available'] == 1): ?>
                        <div class="alert alert-success border-0 small py-2 mb-4">
                            <i class="fas fa-check-circle me-2"></i> Currently Accepting Bookings
                        </div>
                        <a href="pay_deposit.php?id=<?= $hid ?>" class="btn btn-danger btn-lg w-100 rounded-pill fw-bold py-3 shadow">
                            Book Room Now
                        </a>
                    <?php else: ?>
                        <div class="alert alert-danger border-0 small py-2 mb-4">
                            <i class="fas fa-ban me-2"></i> This hostel is currently full.
                        </div>
                        <button class="btn btn-secondary btn-lg w-100 rounded-pill disabled py-3" disabled>Fully Booked</button>
                    <?php endif; ?>

                    <p class="text-center text-muted small mt-4 mb-0">
                        <i class="fas fa-shield-alt me-1"></i> Verified SmartHostel Listing
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>