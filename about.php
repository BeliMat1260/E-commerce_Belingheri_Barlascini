<?php
require_once "config/database.php";
require_once "config/session.php";

// Include header
include 'includes/header.php';
?>

<!-- About Section -->
<div class="container py-5">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold mb-3">About Us</h1>
            <p class="lead text-secondary">Discover our story and meet the team behind your favorite products</p>
        </div>
    </div>

    <!-- Our Story Section -->
    <div class="row mb-5 align-items-center">
        <div class="col-md-6 mb-4 mb-md-0">
            <img src="assets/images/about/store.jpg" alt="Our Store" class="img-fluid rounded shadow-lg">
        </div>
        <div class="col-md-6">
            <h2 class="fw-bold mb-4">Our Story</h2>
            <p class="text-secondary mb-4">
                Founded in 2024, our e-commerce platform has grown from a small local business to a trusted online destination 
                for quality products. We believe in providing exceptional value and service to our customers while maintaining 
                the highest standards of quality and reliability.
            </p>
            <p class="text-secondary">
                Our journey began with a simple idea: to make shopping online easy, enjoyable, and trustworthy. Today, 
                we're proud to serve customers worldwide with our carefully curated selection of products and 
                commitment to customer satisfaction.
            </p>
        </div>
    </div>

    <!-- Mission & Values -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="fw-bold">Our Mission & Values</h2>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="fas fa-star text-primary fa-3x mb-3"></i>
                    <h3 class="h5 fw-bold mb-3">Quality First</h3>
                    <p class="text-secondary mb-0">
                        We carefully select each product to ensure it meets our high standards of quality and reliability.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="fas fa-heart text-primary fa-3x mb-3"></i>
                    <h3 class="h5 fw-bold mb-3">Customer Focus</h3>
                    <p class="text-secondary mb-0">
                        Your satisfaction is our priority. We're committed to providing exceptional service and support.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="fas fa-leaf text-primary fa-3x mb-3"></i>
                    <h3 class="h5 fw-bold mb-3">Sustainability</h3>
                    <p class="text-secondary mb-0">
                        We're committed to sustainable practices and reducing our environmental impact.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="fw-bold">Meet Our Team</h2>
            <p class="text-secondary">The passionate people behind our success</p>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <img src="assets/images/team/team1.jpg" class="card-img-top" alt="Team Member">
                <div class="card-body text-center p-4">
                    <h3 class="h5 fw-bold mb-2">John Doe</h3>
                    <p class="text-primary mb-3">Founder & CEO</p>
                    <p class="text-secondary mb-0">
                        With over 10 years of experience in e-commerce, John leads our company with vision and passion.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <img src="assets/images/team/team2.jpg" class="card-img-top" alt="Team Member">
                <div class="card-body text-center p-4">
                    <h3 class="h5 fw-bold mb-2">Jane Smith</h3>
                    <p class="text-primary mb-3">Operations Manager</p>
                    <p class="text-secondary mb-0">
                        Jane ensures smooth operations and maintains our high standards of service.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <img src="assets/images/team/team3.jpg" class="card-img-top" alt="Team Member">
                <div class="card-body text-center p-4">
                    <h3 class="h5 fw-bold mb-2">Mike Johnson</h3>
                    <p class="text-primary mb-3">Customer Service Lead</p>
                    <p class="text-secondary mb-0">
                        Mike and his team are dedicated to providing exceptional customer support.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row text-center">
                        <div class="col-md-3 mb-4 mb-md-0">
                            <h3 class="h2 fw-bold text-primary mb-2">10K+</h3>
                            <p class="text-secondary mb-0">Happy Customers</p>
                        </div>
                        <div class="col-md-3 mb-4 mb-md-0">
                            <h3 class="h2 fw-bold text-primary mb-2">1K+</h3>
                            <p class="text-secondary mb-0">Products</p>
                        </div>
                        <div class="col-md-3 mb-4 mb-md-0">
                            <h3 class="h2 fw-bold text-primary mb-2">50+</h3>
                            <p class="text-secondary mb-0">Team Members</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="h2 fw-bold text-primary mb-2">24/7</h3>
                            <p class="text-secondary mb-0">Support</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="row">
        <div class="col-12 text-center">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body p-5">
                    <h2 class="fw-bold mb-4">Join Our Journey</h2>
                    <p class="lead mb-4">Experience the difference of shopping with a company that truly cares about its customers.</p>
                    <a href="products.php" class="btn btn-light btn-lg">Shop Now</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 