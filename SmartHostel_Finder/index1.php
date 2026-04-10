<?php
require_once 'config.php';
require_once 'auth_check.php';

// Fetch Search Query if it exists
$search = isset($_GET['search']) ? clean($_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Home | <?php echo $system_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #eee;
        }

        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1555854817-5b2247a8175f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            height: 500px;
            display: flex;
            align-items: center;
            border-radius: 0 0 40px 40px;
        }

        .search-box {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            margin-top: -60px;
        }

        .hostel-card {
            border: none;
            border-radius: 25px;
            transition: 0.4s;
            overflow: hidden;
            background: white;
        }

        .hostel-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .badge-available {
            background: #d1e7dd;
            color: #0f5132;
            font-weight: 700;
            font-size: 0.7rem;
        }

        .star-rating {
            color: #ffc107;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>
    <!-- 
    <nav class="navbar navbar-expand-lg sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-800 d-flex align-items-center" href="index.php">
                <img src="project_logo.png" height="60" class="me-2 rounded-circle">
                <span class="text-dark">SmartHostel <span class="text-danger">FINDER</span></span>
            </a>
            <div class="ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">My Dashboard</a>
                <?php else: ?>
                    <a href="login.php" class="text-dark text-decoration-none me-3 fw-bold">Login</a>
                    <a href="register.php" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </nav> -->

    <header class="hero-section text-center text-white">
        <div class="container">
            <img src="project_logo.png" height="200" class="me-2 mt-0">
            <h1 class="display-3 fw-800 mb-3">Live Better. Study Harder.</h1>
            <p class="fs-5 opacity-75 mb-0">Discover the best-rated student accommodation in Mbarara.</p>
        </div>
    </header>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 search-box">
                <form action="index.php" method="GET" class="row g-3">
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Search by name or location (e.g. Kihumuro, Mbarara Town)..." value="<?= $search ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 fw-bold">Search Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <section class="container my-5 pb-5">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-800 mb-1">Featured Hostels</h2>
                <p class="text-muted mb-0">Approved and verified for student safety.</p>
            </div>
            <a href="#" class="text-danger fw-bold text-decoration-none small">View Map <i class="fas fa-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4">
            <?php
            // Query logic: Join with reviews to get average rating in real-time
            $query = "SELECT h.*, AVG(r.rating) as avg_rating, COUNT(r.review_id) as total_rev 
                  FROM hostels h 
                  LEFT JOIN reviews r ON h.hostel_id = r.hostel_id 
                  WHERE h.status = 'approved' AND h.is_available = 1";

            if ($search) {
                $query .= " AND (h.name LIKE '%$search%' OR h.location LIKE '%$search%')";
            }

            $query .= " GROUP BY h.hostel_id ORDER BY avg_rating DESC";
            $results = $conn->query($query);

            if ($results->num_rows > 0):
                while ($h = $results->fetch_assoc()):
                    $rating = round($h['avg_rating'], 1) ?: 'New';
            ?>
                    <div class="col-md-4">
                        <div class="card hostel-card shadow-sm h-100">
                            <div class="position-relative">
                                <img src="https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="card-img-top" style="height: 220px; object-fit: cover;">
                                <span class="badge badge-available position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill shadow-sm">
                                    <i class="fas fa-check-circle me-1"></i> AVAILABLE
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="fw-bold text-dark mb-0"><?= $h['name'] ?></h5>
                                    <div class="star-rating fw-bold">
                                        <i class="fas fa-star me-1"></i><?= $rating ?>
                                    </div>
                                </div>
                                <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt me-1 text-danger"></i><?= $h['location'] ?></p>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div>
                                        <span class="text-primary fw-800 fs-5"><?= formatMoney($h['price']) ?></span>
                                        <small class="text-muted d-block" style="font-size: 0.65rem;">PER SEMESTER</small>
                                    </div>
                                    <a href="details.php?id=<?= $h['hostel_id'] ?>" class="btn btn-outline-dark rounded-pill px-4 fw-bold btn-sm">Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-3x text-light mb-3"></i>
                    <h4 class="text-muted">No hostels found matching your search.</h4>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <!-- 
    <footer class="bg-dark text-white py-5">
        <div class="container text-center">
            <img src="<?= $logo_path ?>" height="80" color="primary" class="mb-3 rounded-circle border bg-white">
            <p class="mb-0 small opacity-50">&copy; 2026 SmartHostel FINDER. Designed for Mbarara Students.</p>
        </div>
    </footer> -->
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>