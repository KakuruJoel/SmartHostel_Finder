<aside id="sidebar" class="bg-white border-end position-fixed h-100 shadow-sm" style="width: 260px; z-index: 1000;">
    <div class="p-4 text-center">
        <img src="project_logo.png" height="75" class="rounded-circle border border-4 border-white shadow-sm mb-3">
        <h6 class="fw-800 mb-0">SmartHostel Panel</h6>
        <p class="text-danger small fw-bold text-uppercase" style="letter-spacing: 1px;">Administrator</p>
    </div>

    <div class="list-group list-group-flush px-3 mt-4">
        <a href="admin.php" class="list-group-item list-group-item-action border-0 rounded-4 py-3 mb-2 <?= (basename($_SERVER['PHP_SELF']) == 'admin.php') ? 'bg-danger text-white active shadow' : 'text-muted' ?>">
            <i class="fas fa-th-large me-3"></i>Dashboard
        </a>
        <a href="manage_users.php" class="list-group-item list-group-item-action border-0 rounded-4 py-3 mb-2 <?= (basename($_SERVER['PHP_SELF']) == 'manage_users.php') ? 'bg-danger text-white active shadow' : 'text-muted' ?>">
            <i class="fas fa-users me-3"></i>Manage Users
        </a>
        <a href="manage_hostels.php" class="list-group-item list-group-item-action border-0 rounded-4 py-3 mb-2 <?= (basename($_SERVER['PHP_SELF']) == 'manage_hostels.php') ? 'bg-danger text-white active shadow' : 'text-muted' ?>">
            <i class="fas fa-hotel me-3"></i>Hostel Directory
        </a>
        <a href="manage_bookings.php" class="list-group-item list-group-item-action border-0 rounded-4 py-3 mb-2 <?= (basename($_SERVER['PHP_SELF']) == 'manage_bookings.php') ? 'bg-danger text-white active shadow' : 'text-muted' ?>">
            <i class="fas fa-receipt me-3"></i>Bookings & Revenue
        </a>
        <a href="view_site.php" class="list-group-item list-group-item-action border-0 rounded-4 py-3 mb-5 text-muted">
            <i class="fas fa-eye me-3"></i>View Site Front
        </a>

        <div class="mt-5 pt-5">
            <a href="logout.php" class="list-group-item list-group-item-action border-0 rounded-4 py-3 text-danger fw-bold">
                <i class="fas fa-power-off me-3"></i>Logout
            </a>
        </div>
    </div>
</aside>