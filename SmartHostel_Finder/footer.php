<footer class="bg-dark text-white pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="project_logo.png" height="80" class="me-2 bg-white rounded-circle border border-secondary">
                    <h5 class="fw-bold mb-0">SmartHostel <span class="text-danger">FINDER</span></h5>
                </div>
                <p class="small text-secondary">The smarter way to find student accommodation in Mbarara. Verified listings, real reviews, and secure bookings.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-secondary"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="text-secondary"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="text-secondary"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>

            <div class="col-6 col-lg-2 ms-lg-auto">
                <h6 class="fw-bold mb-3 text-uppercase small">Discovery</h6>
                <ul class="list-unstyled small text-secondary">
                    <li class="mb-2"><a href="index.php" class="text-decoration-none text-secondary">Browse Hostels</a></li>
                    <li class="mb-2"><a href="index.php?search=Kihumuro" class="text-decoration-none text-secondary">Kihumuro Area</a></li>
                    <li class="mb-2"><a href="index.php?search=Town" class="text-decoration-none text-secondary">Mbarara Town</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="fw-bold mb-3 text-uppercase small">Support</h6>
                <ul class="list-unstyled small text-secondary">
                    <li class="mb-2"><a href="support.php" class="text-decoration-none text-secondary">Help Center</a></li>
                    <li class="mb-2"><a href="safety.php" class="text-decoration-none text-secondary">Safety Rules</a></li>
                    <li class="mb-2"><a href="mailto:<?= $admin_email ?>" class="text-decoration-none text-secondary">Contact Admin</a></li>
                </ul>
            </div>

            <div class="col-lg-3">
                <h6 class="fw-bold mb-3 text-uppercase small">Location</h6>
                <p class="small text-secondary mb-1"><i class="fas fa-map-marker-alt me-2 text-danger"></i> Mbarara, Uganda</p>
                <p class="small text-secondary"><i class="fas fa-envelope me-2 text-danger"></i> support@smarthostel.ug</p>
            </div>
        </div>

        <hr class="my-4 border-secondary opacity-25">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="small text-secondary mb-0">&copy; <?= date('Y') ?> SmartHostel FINDER. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                <a href="privacy.php" class="text-secondary text-decoration-none small me-3">Privacy Policy</a>
                <a href="terms.php" class="text-secondary text-decoration-none small">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>