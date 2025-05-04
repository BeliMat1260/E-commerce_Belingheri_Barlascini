<?php
require_once __DIR__ . '/includes/config/database.php';
require_once __DIR__ . '/includes/config/session.php';

// Include header
include __DIR__ . '/includes/components/header.php';
?>

<!-- Hero Section -->
<div class="container py-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="display-4 fw-bold mb-4">Welcome to Premium Pool Shop</h1>
            <p class="lead mb-4">Your one-stop destination for premium pool equipment and high-quality water bottles.</p>
            <a href="products.php" class="btn btn-primary btn-lg">Shop Now</a>
        </div>
        <div class="col-md-6">
            <img src="assets/images/hero.jpg" alt="Premium Pool Shop" class="img-fluid rounded shadow-lg">
        </div>
    </div>
</div>

<!-- Featured Categories -->
<div class="container py-5">
    <h2 class="text-center mb-4">Featured Categories</h2>
    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="assets/images/homepage/pool-balls.jpg" class="card-img-top" alt="Pool Balls">
                <div class="card-body">
                    <h5 class="card-title">Pool Balls</h5>
                    <p class="card-text">High-quality pool balls for professional and casual players.</p>
                    <a href="products.php?category=pool_balls" class="btn btn-outline-primary">View Products</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="assets/images/homepage/water-bottles.jpg" class="card-img-top" alt="Water Bottles">
                <div class="card-body">
                    <h5 class="card-title">Water Bottles</h5>
                    <p class="card-text">Premium water bottles for your hydration needs.</p>
                    <a href="products.php?category=water_bottles" class="btn btn-outline-primary">View Products</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Why Choose Us -->
<div class="container py-5 bg-light">
    <h2 class="text-center mb-4">Why Choose Us</h2>
    <div class="row">
        <div class="col-md-3 text-center mb-4">
            <i class="fas fa-shipping-fast fa-3x mb-3 text-primary"></i>
            <h5>Fast Shipping</h5>
            <p>Quick delivery to your doorstep</p>
        </div>
        <div class="col-md-3 text-center mb-4">
            <i class="fas fa-medal fa-3x mb-3 text-primary"></i>
            <h5>Quality Products</h5>
            <p>Premium quality guaranteed</p>
        </div>
        <div class="col-md-3 text-center mb-4">
            <i class="fas fa-headset fa-3x mb-3 text-primary"></i>
            <h5>24/7 Support</h5>
            <p>Always here to help you</p>
        </div>
        <div class="col-md-3 text-center mb-4">
            <i class="fas fa-undo fa-3x mb-3 text-primary"></i>
            <h5>Easy Returns</h5>
            <p>30-day return policy</p>
        </div>
    </div>
</div>

<?php include 'includes/components/footer.php'; ?> 