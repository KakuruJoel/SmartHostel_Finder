<?php
include('config.php');

// 1. Security Check: Only allow Admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$user_name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Live Preview | SmartHostel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7f6;
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
        }

        #wrapper {
            display: flex;
            width: 100%;
            height: 100%;
        }

        /* Sidebar Styling */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #1a1d20;
            color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            transition: 0.3s;
        }

        .sidebar-header {
            padding: 30px 20px;
            background: #141619;
            text-align: center;
            border-bottom: 1px solid #2d3238;
        }

        .sidebar-menu {
            padding: 20px 0;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-menu a {
            padding: 12px 25px;
            display: block;
            color: #adb5bd;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar-menu a:hover {
            color: #fff;
            background: #2d3238;
        }

        .sidebar-menu a.active {
            color: #fff;
            background: #dc3545;
            border-radius: 0 30px 30px 0;
            margin-right: 15px;
        }

        /* Content Container for iFrame */
        #content-viewer {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .view-header {
            padding: 15px 30px;
            background: white;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .site-frame {
            flex-grow: 1;
            border: none;
            width: 100%;
            background: #fff;
        }
    </style>
</head>

<body>

    <div id="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <h4 class="fw-bold text-danger mb-0">SmartHostel</h4>
                <small class="text-light">ADMIN CONTROL</small>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php"><i class="fas fa-th-large me-3"></i>Main Dashboard</a>
                <a href="admin.php"><i class="fas fa-check-circle me-3"></i>Hostel Approvals</a>
                <a href="manage_users.php"><i class="fas fa-users-cog me-3"></i>Manage Users</a>
                <a href="view_site.php" class="active"><i class="fas fa-eye me-3"></i>View Site</a>
                <div class="px-4 mt-4">
                    <hr class="bg-secondary">
                </div>
                <a href="logout.php" class="text-danger"><i class="fas fa-power-off me-3"></i>Logout</a>
            </div>
        </nav>

        <div id="content-viewer">
            <header class="view-header">
                <div>
                    <span class="badge bg-danger rounded-pill px-3 me-2">Live Preview Mode</span>
                    <span class="text-muted small">Viewing the public site from the admin panel.</span>
                </div>
                <div>
                    <button onclick="document.getElementById('siteFrame').contentWindow.location.reload();" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <a href="index.php" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> Open Full Window
                    </a>
                </div>
            </header>

            <iframe id="siteFrame" src="index.php" class="site-frame shadow-inner"></iframe>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>