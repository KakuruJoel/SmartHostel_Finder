<?php
include('config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartHostel | Find Your Perfect Student Home</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1555854817-5b2260d50c63?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            height: 80vh;
            display: flex;
            align-items: center;
            color: white;
        }

        .search-box {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 20px;
        }

        .hostel-card {
            transition: transform 0.3s;
            border: none;
            border-radius: 15px;
        }

        .hostel-card:hover {
            transform: translateY(-10px);
        }
    </style>
</head>

<body class="bg-light">

    <?php include('navbar.php'); ?>

    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <h1 class="display-3 fw-bold mb-4">Find Your Home Away From Home.</h1>
                    <p class="lead mb-5">The smartest way to search, book, and manage your student accommodation in Uganda.</p>

                    <div class="search-box text-dark">
                        <form action="find_hostel.php" method="GET" class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small">Location or Hostel Name</label>
                                <input type="text" name="search" class="form-control" placeholder="e.g. Kihumuro, Mbarara City">
                            </div>
                            <div class="col-md-4 d-grid">
                                <label class="form-label invisible">Search</label>
                                <button type="submit" class="btn btn-primary fw-bold">Search Now</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Why Choose SmartHostel?</h2>
                <p class="text-muted">Built specifically for the modern Ugandan student.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <i class="fas fa-check-circle feature-icon"></i>
                    <h5 class="fw-bold">Verified Listings</h5>
                    <p class="text-muted">Every hostel is approved by our Admin team to ensure safety and quality.</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas fa-mobile-alt feature-icon"></i>
                    <h5 class="fw-bold">Easy Payments</h5>
                    <p class="text-muted">Pay your booking fees instantly using MTN or Airtel Mobile Money.</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas fa-tools feature-icon"></i>
                    <h5 class="fw-bold">Maintenance Support</h5>
                    <p class="text-muted">Report room issues directly to your landlord via your dashboard.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold">Available Hostels</h2>
                <p class="text-muted mb-0">Browse recently added accommodations.</p>
            </div>
            <a href="find_hostel.php" class="btn btn-outline-primary rounded-pill">View All</a>
        </div>

        <div class="row g-4">
            <?php
            $sql = "SELECT h.*, i.image_path FROM hostels h 
                LEFT JOIN images i ON h.hostel_id = i.hostel_id 
                WHERE h.status='approved' 
                GROUP BY h.hostel_id 
                LIMIT 3";
            $res = $conn->query($sql);
            if ($res->num_rows > 0):
                while ($row = $res->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card h-100 hostel-card shadow-sm">
                            <img src="<?= $row['image_path'] ?? 'https://via.placeholder.com/400x250' ?>" class="card-img-top rounded-top-4" alt="<?= $row['name'] ?>" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <h5 class="fw-bold mb-0"><?= $row['name'] ?></h5>
                                    <span class="badge bg-primary-subtle text-primary"><?= $row['price_range'] ?></span>
                                </div>
                                <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt me-1"></i> <?= $row['location'] ?></p>
                                <a href="details.php?id=<?= $row['hostel_id'] ?>" class="btn btn-light border w-100 fw-bold">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            else: ?>
                <div class="col-12 text-center text-muted py-5">
                    <p>No hostels currently available for booking.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="container mb-5">
        <div class="bg-dark text-white p-5 rounded-4 shadow">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold">Are you a Hostel Owner?</h3>
                    <p class="mb-0 opacity-75">Start managing your bookings and revenue more efficiently today.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="auth.php" class="btn btn-primary btn-lg px-5 rounded-pill">List Your Hostel</a>
                </div>
            </div>
        </div>
    </section>

    <?php include('footer.php'); ?>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>