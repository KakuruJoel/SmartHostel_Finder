<?php
include('config.php');
include('auth_check.php');

// 1. Get the Hostel ID from the URL
$hostel_id = isset($_GET['hostel_id']) ? intval($_GET['hostel_id']) : 0;

// 2. Fetch Hostel Name for the Header
$hostel_res = $conn->query("SELECT name FROM hostels WHERE hostel_id = $hostel_id");
$hostel = $hostel_res->fetch_assoc();

if (!$hostel) {
    die("Hostel not found.");
}

// 3. Fetch all rooms for this specific hostel
$rooms_res = $conn->query("SELECT * FROM rooms WHERE hostel_id = $hostel_id AND availability_status = 'available'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Available Rooms | <?= htmlspecialchars($hostel['name']) ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">SmartHostel <span class="text-danger">FINDER</span></a>
            <div class="ms-auto">
                <a href="details.php?id=<?= $hostel_id ?>" class="text-decoration-none text-muted small"><i class="fas fa-arrow-left"></i> Back to Hostel</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Available Rooms at <?= htmlspecialchars($hostel['name']) ?></h2>
            <p class="text-muted">Select a room to proceed with your booking.</p>
        </div>

        <div class="row g-4">
            <?php if ($rooms_res->num_rows > 0): ?>
                <?php while ($r = $rooms_res->fetch_assoc()):
                    // Fetch individual room image
                    $rid = $r['room_id'];
                    $img_res = $conn->query("SELECT image_path FROM images WHERE room_id = $rid LIMIT 1");
                    $img = $img_res->fetch_assoc();
                    $room_img = ($img) ? $img['image_path'] : 'img/default-room.jpg';
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                            <img src="<?= $room_img ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="fw-bold mb-0">Room <?= $r['room_number'] ?></h5>
                                    <span class="badge bg-primary-subtle text-primary"><?= $r['room_type'] ?></span>
                                </div>
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-users me-1"></i> Capacity: <?= $r['capacity'] ?> Person(s)<br>
                                    <i class="fas fa-info-circle me-1"></i> <?= $r['room_facilities'] ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <h5 class="text-success fw-bold mb-0">UGX <?= number_format($r['price']) ?></h5>
                                    <a href="book_now.php?room_id=<?= $r['room_id'] ?>" class="btn btn-dark rounded-pill px-4">
                                        Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-door-closed fa-4x text-muted mb-3"></i>
                    <h4>No rooms available currently.</h4>
                    <p class="text-muted">Please check back later or contact the landlord.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>