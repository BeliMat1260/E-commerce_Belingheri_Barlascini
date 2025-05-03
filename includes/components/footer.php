    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">About Us</h5>
                    <p>Your premium destination for pool equipment and high-quality water bottles since 2024. We provide the best quality products for your pool and hydration needs.</p>
                    <div class="social-links mt-3">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="/E-commerce_Belingheri_Barlascini/products.php?category=pool_balls" class="text-light text-decoration-none">
                                <i class="fas fa-chevron-right me-2"></i>Pool Balls
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/E-commerce_Belingheri_Barlascini/products.php?category=pool_tables" class="text-light text-decoration-none">
                                <i class="fas fa-chevron-right me-2"></i>Pool Tables
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/E-commerce_Belingheri_Barlascini/products.php?category=water_bottles" class="text-light text-decoration-none">
                                <i class="fas fa-chevron-right me-2"></i>Water Bottles
                            </a>
                        </li>

                        <li>
                            <a href="/E-commerce_Belingheri_Barlascini/pages/contact.php" class="text-light text-decoration-none">
                                <i class="fas fa-chevron-right me-2"></i>Contact Us
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">Contact Info</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            123 Pool Street, Water City, WC 12345
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-phone me-2"></i>
                            <a href="tel:+1234567890" class="text-light text-decoration-none">+1 (234) 567-890</a>
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:info@premiumpoolshop.com" class="text-light text-decoration-none">info@premiumpoolshop.com</a>
                        </li>
                        <li>
                            <i class="fas fa-clock me-2"></i>
                            Mon - Fri: 9:00 AM - 6:00 PM
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; 2024 Premium Pool Shop. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="/E-commerce_Belingheri_Barlascini/pages/privacy.php" class="text-light text-decoration-none me-3">Privacy Policy</a>
                    <a href="/E-commerce_Belingheri_Barlascini/pages/terms.php" class="text-light text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Initialize Bootstrap components -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize all popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            // Initialize all dropdowns
            var dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            dropdownTriggerList.forEach(function (dropdownTriggerEl) {
                new bootstrap.Dropdown(dropdownTriggerEl);
            });
        });
    </script>
    <?php include __DIR__ . '/../config/init.php'; ?>
</body>
</html> 