<?php include('config.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Terms of Service | SmartHostel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        .tos-container {
            max-width: 800px;
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .tos-card {
            border: none;
            border-radius: 20px;
        }

        h4 {
            color: #0d6efd;
            font-weight: 700;
            margin-top: 25px;
        }

        p {
            color: #495057;
            line-height: 1.6;
        }
    </style>
</head>

<body class="bg-light">

    <?php include('navbar.php'); ?>

    <div class="container tos-container">
        <div class="card tos-card shadow-sm p-5 bg-white">
            <div class="text-center mb-5">
                <i class="fas fa-file-contract fa-3x text-primary mb-3"></i>
                <h1 class="fw-bold">Terms of Service</h1>
                <p class="text-muted">Last updated: April 08, 2026</p>
            </div>

            <hr>

            <h4>1. Acceptance of Terms</h4>
            <p>By creating an account or using SmartHostel, you agree to comply with and be legally bound by these Terms of Service. If you do not agree to these terms, you have no right to obtain information from or otherwise continue using the platform.</p>

            <h4>2. Platform Role</h4>
            <p>SmartHostel acts as a digital marketplace. We do not own, manage, or inspect the hostels listed. We provide the technology for <strong>Tenants</strong> to find housing and <strong>Landlords</strong> to manage bookings and payments.</p>

            <h4>3. Payments & Mobile Money</h4>
            <p>All financial transactions are processed via MTN or Airtel Mobile Money.
            <ul>
                <li>SmartHostel is not responsible for failed transactions due to network errors.</li>
                <li>Once a payment is marked as "Completed," the funds are held/transferred according to the landlord's agreement.</li>
                <li>Any refund requests must be initiated through the landlord directly.</li>
            </ul>
            </p>

            <h4>4. User Conduct</h4>
            <p>Landlords must provide accurate photos and pricing. Tenants must provide valid student identification if requested. Fraudulent listings or harassment will result in immediate account termination by the <strong>Admin</strong>.</p>

            <h4>5. Maintenance Issues</h4>
            <p>The "Report Issue" feature is a communication tool. The speed and quality of repairs are the sole responsibility of the Landlord and the tenancy agreement signed between both parties.</p>

            <h4>6. Limitation of Liability</h4>
            <p>SmartHostel shall not be liable for any damages, theft, or personal injury occurring within a hostel listed on our platform.</p>

            <div class="mt-5 pt-4 border-top text-center">
                <p class="small text-muted mb-4">By clicking "I Agree" during registration, you confirm you have read this document.</p>
                <a href="register.php" class="btn btn-primary px-5 rounded-pill fw-bold">Back to Registration</a>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>

</html>