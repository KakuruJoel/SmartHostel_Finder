<?php
require_once 'config.php';
require_once 'auth_check.php';

// 1. Access Control: Admins Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?msg=unauthorized");
    exit();
}

// --- 2. HANDLE DELETE ACTION ---
if (isset($_GET['delete_id'])) {
    $did = intval($_GET['delete_id']);

    // Security: Prevent Admin from deleting themselves
    if ($did == $_SESSION['user_id']) {
        header("Location: manage_users.php?msg=cannot_delete_self");
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $did);
        $stmt->execute();
        header("Location: manage_users.php?msg=user_deleted");
    }
    exit();
}

// --- 3. FETCH USERS ---
$search = isset($_GET['search']) ? clean($_GET['search']) : '';
$query = "SELECT * FROM users WHERE user_id != " . $_SESSION['user_id'];

if ($search) {
    $query .= " AND (full_name LIKE '%$search%' OR email LIKE '%$search%')";
}
$query .= " ORDER BY user_id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Management | <?= $system_name ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .main-content {
            margin-left: 260px;
            padding: 40px;
        }

        .user-card {
            border: none;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .search-bar {
            border-radius: 15px;
            border: 1px solid #eee;
            padding: 12px 20px;
            background: white;
        }

        .role-pill {
            font-size: 0.7rem;
            padding: 4px 12px;
            border-radius: 50px;
            font-weight: 700;
        }

        .btn-icon {
            width: 35px;
            height: 35px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <?php include 'admin_sidebar.php'; ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-800 mb-1">User Management</h2>
                <p class="text-muted mb-0">Monitor and manage all platform members.</p>
            </div>
            <a href="add_user.php" class="btn btn-danger rounded-pill px-4 fw-bold">
                <i class="fas fa-user-plus me-2"></i>Create New User
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <form action="manage_users.php" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control search-bar shadow-sm" placeholder="Search by name or email..." value="<?= $search ?>">
                    <button type="submit" class="btn btn-dark rounded-4 px-4">Search</button>
                </form>
            </div>
        </div>

        <div class="user-card p-4">
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-info border-0 rounded-4 py-2 small mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <?= str_replace('_', ' ', $_GET['msg']) ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 rounded-start px-3">User</th>
                            <th class="border-0">Contact Info</th>
                            <th class="border-0">Role</th>
                            <th class="border-0">Joined Date</th>
                            <th class="border-0 text-end rounded-end px-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($u = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold; color: #dc3545;">
                                                <?= strtoupper(substr($u['full_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= $u['full_name'] ?></div>
                                                <div class="text-muted small">UID: #<?= $u['user_id'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small"><i class="fas fa-envelope me-2 opacity-50"></i><?= $u['email'] ?></div>
                                        <div class="small mt-1"><i class="fas fa-phone me-2 opacity-50"></i><?= $u['phone'] ?></div>
                                    </td>
                                    <td>
                                        <span class="role-pill <?= ($u['role'] == 'landlord') ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success' ?>">
                                            <?= strtoupper($u['role']) ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted">
                                        <?= date('M d, Y', strtotime($u['created_at'])) ?>
                                    </td>
                                    <td class="text-end px-3">
                                        <a href="edit_user.php?id=<?= $u['user_id'] ?>" class="btn btn-light btn-icon text-primary me-1" title="Edit User">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <button onclick="confirmDelete(<?= $u['user_id'] ?>)" class="btn btn-light btn-icon text-danger" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to permanently delete this user? All their data (hostels or bookings) may be affected.')) {
                window.location.href = 'manage_users.php?delete_id=' + id;
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>