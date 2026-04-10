<?php include('config.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Privacy Policy | SmartHostel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        .privacy-container {
            max-width: 850px;
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .privacy-card {
            border: none;
            border-radius: 20px;
            border-top: 5px solid #0d6efd;
        }

        .policy-section {
            margin-bottom: 30px;
        }

        h5 {
            color: #212529;
            font-weight: bold;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        h5 i {
            color: #0d6efd;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        p,
        li {
            color: #555;
            line-height: 1.7;
            font-size: 0.95rem;
        }
    </style>
</head>

<body class="bg-light">

    <?php include('navbar.php'); ?>

    <div class="container privacy-container">
        <div class="card privacy-card shadow-sm p-5 bg-white">
            <div class="text-center mb-5">
                <h1 class="fw-bold">Privacy Policy</h1>
                <p class="text-muted">Effective Date: April 08, 2026</p>
            </div>

            <div class="policy-section">
                <h5><i class="fas fa-info-circle"></i> 1. Information We Collect</h5>
                <p>We collect information to provide a better experience for all our users. This includes:</p>
                <ul>
                    <li><strong>Account Data:</strong> Name, Email, and Phone Number provided during registration.</li>
                    <li><strong>Transaction Data:</strong> Mobile Money transaction references (MTN/Airtel) when you pay for a room.</li>
                    <li><strong>Property Data:</strong> Photos and descriptions of hostels uploaded by Landlords.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h5><i class="fas fa-user-shield"></i> 2. How We Use Your Information</h5>
                <p>Your data is used strictly for platform operations:</p>
                <ul>
                    <li>To facilitate bookings between Tenants and Landlords.</li>
                    <li>To verify payments and prevent fraudulent transactions.</li>
                    <li>To allow Landlords to contact Tenants regarding maintenance or check-in.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h5><i class="fas fa-share-alt"></i> 3. Data Sharing</h5>
                <p>SmartHostel <strong>does not sell</strong> your personal data to third parties. We only share information in the following cases:</p>
                <ul>
                    <li><strong>Between Users:</strong> A Landlord will see the name and phone number of a Tenant who has booked their room.</li>
                    <li><strong>Payment Providers:</strong> Transaction data is shared with telecommunication providers (MTN/Airtel) to process payments.</li>
                    <li><strong>Legal Requirements:</strong> If required by Ugandan law to prevent crime or fraud.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h5><i class="fas fa-lock"></i> 4. Data Security</h5>
                <p>We implement industry-standard security measures, including <strong>Bcrypt password hashing</strong> and secure session management, to protect your unauthorized access to your account.</p>
            </div>

            <div class="policy-section">
                <h5><i class="fas fa-mouse-pointer"></i> 5. Your Rights</h5>
                <p>You have the right to access your profile at any time via the <strong>Dashboard</strong> to update or correct your personal information. To delete your account permanently, you must contact the <strong>System Admin</strong>.</p>
            </div>

            <div class="mt-5 pt-4 border-top text-center">
                <p class="small text-muted">Questions about our privacy practices? Contact us at support@smarthostel.ug</p>
                <a href="index.php" class="btn btn-outline-primary px-4 rounded-pill">Back to Home</a>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>

</html>