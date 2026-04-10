<?php
require_once 'config.php';
require_once 'auth_check.php';

// --- 1. SEARCH & FILTER LOGIC ---
$search = isset($_GET['search']) ? clean($_GET['search']) : '';
$location = isset($_GET['location']) ? clean($_GET['location']) : '';
$max_price = isset($_GET['max_price']) ? intval($_GET['max_price']) : 2000000;

// Base Query: Only show 'approved' and 'available' hostels
$query = "SELECT h.*, u.full_name as landlord 
          FROM hostels h 
          JOIN users u ON h.landlord_id = u.user_id 
          WHERE h.status = 'approved' AND h.is_available = 1";

if ($search) {
    $query .= " AND (h.name LIKE '%$search%' OR h.description LIKE '%$search%')";
}
if ($location) {
    $query .= " AND h.location = '$location'";
}
if ($max_price) {
    $query .= " AND h.price_range <= $max_price";
}

$query .= " ORDER BY h.price_range ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Find Hostels | <?= $system_name ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .filter-card {
            border: none;
            border-radius: 20px;
            position: sticky;
            top: 100px;
        }

        .hostel-card {
            border: none;
            border-radius: 20px;
            transition: 0.3s;
            overflow: hidden;
        }

        .hostel-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .price-tag {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 5px 15px;
            border-radius: 50px;
            font-weight: 800;
            color: #dc3545;
        }

        .img-placeholder {
            height: 200px;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <div class="row g-4">

            <div class="col-lg-3">
                <div class="card filter-card p-4 shadow-sm bg-white">
                    <h5 class="fw-bold mb-4">Filter Results</h5>
                    <form action="find_hostels.php" method="GET">

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase">Area</label>
                            <select name="location" class="form-select border-0 bg-light shadow-none">
                                <option value="">All Locations</option>
                                <option value="Kihumuro" <?= $location == 'Kihumuro' ? 'selected' : '' ?>>Kihumuro</option>
                                <option value="Town Centre" <?= $location == 'Town Centre' ? 'selected' : '' ?>>Town Centre</option>
                                <option value="Kakyeka" <?= $location == 'Kakyeka' ? 'selected' : '' ?>>Kakyeka</option>
                                <option value="Rwebikoona" <?= $location == 'Rwebikoona' ? 'selected' : '' ?>>Rwebikoona</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase">Max Price (UGX)</label>
                            <input type="range" name="max_price" class="form-range" min="300000" max="2000000" step="50000" value="<?= $max_price ?>" oninput="this.nextElementSibling.value = this.value">
                            <output class="fw-bold text-danger"><?= number_format($max_price) ?></output>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold">Apply Filters</button>
                        <a href="find_hostels.php" class="btn btn-link btn-sm w-100 text-muted mt-2 text-decoration-none">Clear All</a>
                    </form>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-800 mb-0">Available Hostels</h2>
                    <span class="text-muted small">Showing <?= $result->num_rows ?> results</span>
                </div>

                <div class="row g-4">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($h = $result->fetch_assoc()): ?>
                            <div class="col-md-6">
                                <div class="card hostel-card shadow-sm h-100 bg-white">
                                    <div class="position-relative">
                                        <div class="img-placeholder">
                                            <i class="fas fa-image fa-3x"></i>
                                        </div>
                                        <div class="price-tag shadow-sm">
                                            <?= formatMoney($h['price']) ?>
                                        </div>
                                    </div>

                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="fw-bold mb-0"><?= $h['name'] ?></h5>
                                            <span class="badge bg-success-subtle text-success rounded-pill">Verified</span>
                                        </div>
                                        <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?= $h['location'] ?></p>

                                        <p class="card-text small text-secondary mb-4">
                                            <?= substr($h['description'], 0, 100) ?>...
                                        </p>

                                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                            <div class="small">
                                                <i class="fas fa-user-tie me-1 text-muted"></i> <?= explode(' ', $h['landlord'])[0] ?>
                                            </div>
                                            <a href="details.php?id=<?= $h['hostel_id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <img src="project_logo.png" height="100" class="opacity-25 mb-3 rounded-circle grayscale">
                            <h4 class="text-muted">No hostels match your criteria.</h4>
                            <p class="small text-muted">Try adjusting your filters or price range.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>