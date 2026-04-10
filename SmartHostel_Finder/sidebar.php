<?php
// sidebar.php - Included in all dashboard pages
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? 'tenant';
$name = $_SESSION['name'] ?? 'User';
?>

<style>
    #sidebar {
        min-width: 260px;
        max-width: 260px;
        background: #1e293b;
        /* Dark slate blue */
        color: #f8fafc;
        min-height: 100vh;
        transition: all 0.3s;
        box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar-header {
        padding: 25px 20px;
        background: #0f172a;
        border-bottom: 1px solid #334155;
    }

    .nav-list {
        padding: 20px 0;
        list-style: none;
    }

    .nav-item-link {
        padding: 12px 25px;
        display: flex;
        align-items: center;
        color: #94a3b8;
        text-decoration: none;
        transition: 0.2s;
        border-left: 4px solid transparent;
    }

    .nav-item-link:hover {
        background: #334155;
        color: #fff;
    }

    .nav-item-link.active {
        background: #334155;
        color: #3b82f6;
        /* Bright blue */
        border-left-color: #3b82f6;
        font-weight: 600;
    }

    .nav-item-link i {
        margin-right: 15px;
        width: 20px;
        text-align: center;
    }

    .role-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 10px 25px;
        color: #64748b;
        font-weight: 700;
    }
</style>

<nav id="sidebar">
    <div class="sidebar-header">
        <h4 class="fw-bold mb-0"><i class="fas fa-home me-2"></i>SmartHostel</h4>
    </div>

    <ul class="nav-list">
        <li>
            <a href="dashboard.php" class="nav-item-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </li>

        <?php if ($role == 'admin'): ?>
            <div class="role-label">System Admin</div>
            <li><a href="manage_users.php" class="nav-item-link <?= ($current_page == 'manage_users.php') ? 'active' : '' ?>"><i class="fas fa-users-cog"></i> Users</a></li>
            <li><a href="approve_hostels.php" class="nav-item-link <?= ($current_page == 'approve_hostels.php') ? 'active' : '' ?>"><i class="fas fa-check-double"></i> Approvals</a></li>
            <li><a href="reports.php" class="nav-item-link <?= ($current_page == 'reports.php') ? 'active' : '' ?>"><i class="fas fa-file-invoice-dollar"></i> System Revenue</a></li>

        <?php elseif ($role == 'landlord'): ?>
            <div class="role-label">Landlord Portal</div>
            <li><a href="add_hostel.php" class="nav-item-link <?= ($current_page == 'add_hostel.php') ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i> Add Hostel</a></li>
            <li><a href="manage_hostels.php" class="nav-item-link <?= ($current_page == 'manage_hostels.php') ? 'active' : '' ?>"><i class="fas fa-building"></i> My Properties</a></li>
            <li><a href="tenant_requests.php" class="nav-item-link <?= ($current_page == 'tenant_requests.php') ? 'active' : '' ?>"><i class="fas fa-tools"></i> Tenant Requests</a></li>
            <li><a href="payments.php" class="nav-item-link <?= ($current_page == 'payments.php') ? 'active' : '' ?>"><i class="fas fa-wallet"></i> My Earnings</a></li>

        <?php else: ?>
            <div class="role-label">Student Portal</div>
            <li><a href="find_hostel.php" class="nav-item-link <?= ($current_page == 'find_hostel.php') ? 'active' : '' ?>"><i class="fas fa-search"></i> Search Hostels</a></li>
            <li><a href="my_bookings.php" class="nav-item-link <?= ($current_page == 'my_bookings.php') ? 'active' : '' ?>"><i class="fas fa-bookmark"></i> My Bookings</a></li>
            <li><a href="report_maintenance.php" class="nav-item-link <?= ($current_page == 'report_maintenance.php') ? 'active' : '' ?>"><i class="fas fa-exclamation-triangle"></i> Report Issue</a></li>
        <?php endif; ?>

        <hr class="mx-3 border-secondary opacity-25">

        <li><a href="profile.php" class="nav-item-link <?= ($current_page == 'profile.php') ? 'active' : '' ?>"><i class="fas fa-user-circle"></i> Profile</a></li>
        <li><a href="support.php" class="nav-item-link <?= ($current_page == 'support.php') ? 'active' : '' ?>"><i class="fas fa-question-circle"></i> Support</a></li>
        <li><a href="logout.php" class="nav-item-link text-danger"><i class="fas fa-power-off"></i> Logout</a></li>
    </ul>
</nav>