<?php
include('config.php');
include('auth_check.php');

// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'landlord') {
    header("Location: auth.php");
    exit();
}

$landlord_id = $_SESSION['user_id'];
$message = "";

// 2. Handle DELETE Room
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    // Extra security: Ensure this room belongs to a hostel owned by this landlord
    $check = $conn->query("SELECT r.room_id FROM rooms r JOIN hostels h ON r.hostel_id = h.hostel_id WHERE r.room_id = $id AND h.landlord_id = $landlord_id");

    if ($check->num_rows > 0) {
        // Delete associated images first to keep database clean
        $conn->query("DELETE FROM images WHERE room_id = $id");
        $conn->query("DELETE FROM rooms WHERE room_id = $id");
        $message = "<div class='alert alert-success'>Room and associated data deleted successfully.</div>";
    }
}

// 3. Handle ADD Room
if (isset($_POST['add_room'])) {
    $hid = intval($_POST['hostel_id']);
    $r_no = mysqli_real_escape_string($conn, $_POST['room_number']); // Fixed key name
    $type = mysqli_real_escape_string($conn, $_POST['room_type']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $cap = intval($_POST['capacity']);
    $faci = isset($_POST['room_faci']) ? implode(', ', $_POST['room_faci']) : '';

    $sql = "INSERT INTO rooms (hostel_id, room_number, room_type, price, capacity, status, room_facilities) 
            VALUES ($hid, '$r_no', '$type', '$price', $cap, 'available', '$faci')";

    if ($conn->query($sql)) {
        $room_id = $conn->insert_id; // Get the ID for the new room
        $message = "<div class='alert alert-primary'>New room added to your inventory!</div>";

        // 3b. Handle Image Upload
        if (!empty($_FILES['room_image']['name'])) {
            $target_dir = "uploads/rooms/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_name = time() . "_room_" . basename($_FILES["room_image"]["name"]);
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($_FILES["room_image"]["tmp_name"], $target_file)) {
                // Insert into images table using the new room_id
                $conn->query("INSERT INTO images (room_id, hostel_id, image_path, is_thumbnail) 
                              VALUES ($room_id, NULL, '$target_file', 0)");
            }
        }
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// 4. Fetch Hostels for Dropdown (Only Approved ones for THIS landlord)
$hostel_options = $conn->query("SELECT hostel_id, name FROM hostels WHERE landlord_id = $landlord_id AND status = 'approved'");

// 5. Fetch ALL Rooms for the table
$rooms_sql = "SELECT r.*, h.name as hostel_name 
              FROM rooms r 
              JOIN hostels h ON r.hostel_id = h.hostel_id 
              WHERE h.landlord_id = $landlord_id
              ORDER BY h.name ASC, r.room_number ASC";
$rooms_res = $conn->query($rooms_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Room Management | SmartHostel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        .room-table-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .table thead {
            background-color: #f8fafc;
        }

        .status-pill {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 50px;
        }
    </style>
</head>

<body class="bg-light">

    <div class="d-flex">
        <?php include('sidebar.php'); ?>

        <div class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0">Manage Rooms</h2>
                    <p class="text-muted small">Update pricing, availability, and types for your approved hostels.</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                    <i class="fas fa-plus me-2"></i> Add New Room
                </button>
            </div>

            <?= $message ?>

            <div class="card room-table-card overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Hostel Name</th>
                                <th>Room No.</th>
                                <th>Room Type</th>
                                <th>Price (UGX)</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($rooms_res->num_rows > 0): ?>
                                <?php while ($room = $rooms_res->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($room['hostel_name']) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= $room['room_number'] ?></span></td>
                                        <td><?= $room['room_type'] ?></td>
                                        <td class="fw-bold text-success"><?= number_format($room['price']) ?></td>
                                        <td><?= $room['capacity'] ?> Stud.</td>
                                        <td>
                                            <?php if ($room['status'] == 'available'): ?>
                                                <span class="status-pill bg-success-subtle text-success">Available</span>
                                            <?php else: ?>
                                                <span class="status-pill bg-danger-subtle text-danger">Occupied</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm rounded">
                                                <a href="manage_rooms.php?delete_id=<?= $room['room_id'] ?>"
                                                    class="btn btn-sm btn-white border"
                                                    onclick="return confirm('Permanently remove this room?')" title="Delete">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="opacity-25 mb-3"><i class="fas fa-door-closed fa-3x"></i></div>
                                        <p class="text-muted">No rooms found. Get started by adding one!</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addRoomModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="manage_rooms.php" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
                <div class="modal-header border-0">
                    <h5 class="fw-bold">Add Room to Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Target Hostel</label>
                        <select name="hostel_id" class="form-select bg-light" required>
                            <option value="" disabled selected>-- Select Approved Hostel --</option>
                            <?php
                            // Re-fetching options to ensure dropdown is populated correctly
                            $hostel_options->data_seek(0);
                            while ($h = $hostel_options->fetch_assoc()): ?>
                                <option value="<?= $h['hostel_id'] ?>"><?= $h['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold mb-2">Room Number/Name</label>
                            <input type="text" name="room_number" class="form-control" placeholder="e.g. G04" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold mb-2">Room Type</label>
                            <select name="room_type" class="form-select">
                                <option>Single</option>
                                <option>Double (Standard)</option>
                                <option>Triple</option>
                                <option>Self-Contained</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold mb-2">Price per Semester (UGX)</label>
                            <input type="number" name="price" class="form-control" placeholder="800000" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold mb-2">Capacity (Persons)</label>
                            <input type="number" name="capacity" class="form-control" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Room Features:</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="room_faci[]" value="Bathroom" id="f1">
                            <label class="form-check-label" for="f1">Bathroom</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="room_faci[]" value="Tiles" id="f2">
                            <label class="form-check-label" for="f2">Tiles</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="room_faci[]" value="Kitchen" id="f3">
                            <label class="form-check-label" for="f3">Kitchen</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small mb-2">Room Photo</label>
                        <input type="file" name="room_image" class="form-control" accept="image/*">
                        <div class="form-text">Upload a clear photo of the room interior.</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_room" class="btn btn-primary rounded-pill px-4">Save Room</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>