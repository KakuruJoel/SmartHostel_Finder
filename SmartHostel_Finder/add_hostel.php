<?php
include('config.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$landlord_id = $_SESSION['user_id'];
$msg = "";

if (isset($_POST['save_hostel'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $price = mysqli_real_escape_string($conn, $_POST['price_range']);
    $rooms = intval($_POST['num_rooms']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    // 1. Insert into 'hostels' table
    $sql = "INSERT INTO hostels (landlord_id, name, location, price_range, num_rooms, description, status) 
            VALUES ('$landlord_id', '$name', '$loc', '$price', '$rooms', '$desc', 'pending')";

    if ($conn->query($sql)) {
        $hostel_id = $conn->insert_id; // Get the ID of the newly created hostel

        // 2. Insert selected facilities into 'hostel_facilities' junction table
        if (!empty($_POST['fac_ids'])) {
            foreach ($_POST['fac_ids'] as $f_id) {
                $f_id = intval($f_id);
                $conn->query("INSERT INTO hostel_facilities (hostel_id, facility_id) VALUES ($hostel_id, $f_id)");
            }
        }
        if (move_uploaded_file($_FILES["hostel_image"]["tmp_name"], $target_file)) {
            // Insert into images table with hostel_id and mark as thumbnail
            $conn->query("INSERT INTO images (hostel_id, room_id, image_path, is_thumbnail) 
                  VALUES ($hostel_id, NULL, '$target_file', 1)");
        }
        $msg = "<div class='alert alert-success'>Hostel listed! Waiting for Admin approval.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Hostel | SmartHostel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body class="bg-light d-flex">
    <?php include('sidebar.php'); ?>
    <div class="container py-5">
        <?= $msg ?>
        <form action="" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm border-0">
            <h4 class="fw-bold mb-4">Register New Hostel</h4>
            <div class="row mb-3">
                <div class="col-md-6"><label>Hostel Name</label><input type="text" name="name" class="form-control" required></div>
                <div class="col-md-6"><label>Location</label><input type="text" name="location" class="form-control" required></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><label>Price Range (UGX)</label><input type="text" name="price_range" class="form-control" required></div>
                <div class="col-md-6"><label>Total Rooms</label><input type="number" name="num_rooms" class="form-control" required></div>
            </div>

            <label class="fw-bold small mb-2">Select Facilities Available</label>
            <div class="d-flex flex-wrap gap-3 mb-3 border p-3 rounded bg-white">
                <?php
                $facs = $conn->query("SELECT * FROM facilities");
                while ($f = $facs->fetch_assoc()): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="fac_ids[]" value="<?= $f['facility_id'] ?>">
                        <label class="form-check-label"><?= $f['name'] ?></label>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="mb-3">
                <label class="fw-bold small">Hostel Main Image</label>
                <input type="file" name="hostel_image" class="form-control" accept="image/*" required>
                <div class="form-text">Upload a clear photo of the hostel front or a room.</div>
            </div>

            <div class="mb-3"><label>Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
            <button type="submit" name="save_hostel" class="btn btn-primary px-5">Submit for Approval</button>
        </form>
    </div>
</body>

</html>