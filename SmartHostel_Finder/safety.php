<?php include('config.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Safety & Security Rules | SmartHostel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        .safety-header {
            background: #198754;
            color: white;
            padding: 60px 0;
            border-radius: 0 0 50px 50px;
        }

        .rule-card {
            border: none;
            border-radius: 15px;
            transition: 0.3s;
        }

        .rule-card:hover {
            transform: scale(1.02);
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            background: #e8f5e9;
            color: #198754;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-light">

    <?php include('navbar.php'); ?>

    <div class="safety-header text-center">
        <div class="container">
            <h1 class="fw-bold"><i class="fas fa-shield-alt me-2"></i> Safety First</h1>
            <p class="lead opacity-75">Our guidelines to ensure a secure and peaceful living environment for all students.</p>
        </div>
    </div>

    <div class="container py-5 mt-n5">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card rule-card shadow-sm p-4 h-100 bg-white">
                    <div class="icon-circle"><i class="fas fa-user-graduate"></i></div>
                    <h4 class="fw-bold">For Students (Tenants)</h4>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>ID Verification:</strong> Always keep your university ID card updated in your profile.</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Visitor Policy:</strong> Strictly follow the hostel's visitor hours to ensure the privacy of your roommates.</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Locking Up:</strong> Ensure your room and the main hostel gate are locked at night or when leaving.</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Report Hazards:</strong> Use the maintenance tool to report broken locks or electrical issues immediately.</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card rule-card shadow-sm p-4 h-100 bg-white">
                    <div class="icon-circle"><i class="fas fa-building"></i></div>
                    <h4 class="fw-bold">For Hostel Owners</h4>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Security Personnel:</strong> Ensure a 24/7 guard or secure biometric access is functional.</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Fire Safety:</strong> Maintain functional fire extinguishers on every floor.</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Lighting:</strong> Ensure all corridors and external gates are well-lit at night.</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Emergency Contacts:</strong> Post police and medical emergency numbers in the common area.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 bg-danger text-white p-4 rounded-4 shadow">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                            <h4 class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Immediate Danger?</h4>
                            <p class="mb-0">If there is an ongoing emergency, do not wait for a support ticket. Contact local authorities.</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <a href="tel:999" class="btn btn-light fw-bold px-4 rounded-pill">Call 999 (Police)</a>
                            <a href="support.php" class="btn btn-outline-light ms-2 px-4 rounded-pill">Platform Support</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>

</html>