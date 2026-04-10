<nav class="navbar navbar-expand-lg sticky-top bg-white border-bottom py-3">
    <div class="container">
        <a class="navbar-brand fw-800 d-flex align-items-center" href="index.php">
            <img src="project_logo.png" height="45" class="me-2 rounded-circle border shadow-sm">
            <span class="text-dark">SmartHostel <span class="text-danger">FINDER</span></span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <!-- <?php if ($_SESSION['role'] == 'tenant'): ?> -->
                    <a class="nav-link fw-bold text-dark px-3" href="find_hostels.php">Browse</a>
                    <!-- <?php else: ?> -->
                    <a class="nav-link fw-bold text-dark px-3" href="index.php">Browse</a>
                    <!-- <?php endif; ?> -->
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-dark px-3" href="dashboard.php">Dashboard</a>
                    </li>

                    <?php if ($_SESSION['role'] == 'landlord'): ?>
                        <li class="nav-item">
                            <a class="btn btn-danger rounded-pill px-4 ms-lg-2 fw-bold shadow-sm" href="add_hostel.php">
                                <i class="fas fa-plus me-1"></i> Add Hostel
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item dropdown ms-lg-3">
                        <a class="nav-link dropdown-toggle fw-bold text-danger" href="#" role="button" data-bs-toggle="dropdown">
                            Hi, <?= explode(' ', $_SESSION['name'])[0] ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3">
                            <li><a class="dropdown-item py-2" href="profile.php"><i class="fas fa-user-edit me-2 opacity-50"></i> Profile</a></li>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <li><a class="dropdown-item py-2" href="admin.php"><i class="fas fa-user-shield me-2 opacity-50"></i> Admin Panel</a></li>
                            <?php endif; ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item py-2 text-danger" href="logout.php"><i class="fas fa-power-off me-2"></i> Logout</a></li>
                        </ul>
                    </li>

                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-dark px-3" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger rounded-pill px-4 ms-lg-2 fw-bold shadow-sm" href="register.php">Join Now</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>