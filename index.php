<?php
require_once "config/database.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Pool & Water Bottles Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <div class="hero-section bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold">Welcome to Premium Pool Shop</h1>
                    <p class="lead">Discover our premium collection of pool equipment and high-quality water bottles for your perfect game experience.</p>
                    <a href="products.php" class="btn btn-light btn-lg">Shop Now</a>
                </div>
                <div class="col-md-6">
                    <img src="images/hero-image.jpg" alt="Pool Equipment and Water Bottles" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <section class="categories py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Categories</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow">
                        <img src="images/pool-balls.jpg" class="card-img-top" alt="Pool Balls">
                        <div class="card-body text-center">
                            <h3 class="card-title">Pool Balls</h3>
                            <p class="card-text">Premium quality pool balls made from phenolic resin for professional play.</p>
                            <a href="products.php?category=pool_balls" class="btn btn-primary">View Collection</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow">
                        <img src="images/pool-tables.jpg" class="card-img-top" alt="Pool Tables">
                        <div class="card-body text-center">
                            <h3 class="card-title">Pool Tables</h3>
                            <p class="card-text">Professional pool tables crafted with premium materials for the perfect game.</p>
                            <a href="products.php?category=pool_tables" class="btn btn-primary">View Collection</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow">
                        <img src="images/water-bottles.jpg" class="card-img-top" alt="Water Bottles">
                        <div class="card-body text-center">
                            <h3 class="card-title">Water Bottles</h3>
                            <p class="card-text">Eco-friendly and stylish water bottles to keep you hydrated during the game.</p>
                            <a href="products.php?category=water_bottles" class="btn btn-primary">View Collection</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Featured Products</h2>
            <div class="row">
                <?php
                $featured_sql = "SELECT p.*, 
                                (SELECT COUNT(*) FROM product_reviews WHERE product_id = p.id) as review_count,
                                (SELECT AVG(rating) FROM product_reviews WHERE product_id = p.id) as avg_rating,
                                (SELECT image_url FROM product_images WHERE product_id = p.id AND image_order = 1 LIMIT 1) as primary_image
                                FROM products p 
                                WHERE p.is_active = 1 
                                ORDER BY p.created_at DESC 
                                LIMIT 4";
                $result = mysqli_query($conn, $featured_sql);
                if(mysqli_num_rows($result) > 0):
                    while($row = mysqli_fetch_assoc($result)):
                ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 border-0 shadow">
                        <?php if ($row['primary_image']): ?>
                            <img src="<?php echo htmlspecialchars($row['primary_image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($row['name']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                <span class="text-muted">No image available</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text text-primary fw-bold">$<?php echo number_format($row['price'], 2); ?></p>
                            <a href="product.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
                <?php
                    endwhile;
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="why-choose-us py-5">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Us</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <i class="fas fa-medal fa-3x text-primary mb-3"></i>
                        <h4>Premium Quality</h4>
                        <p>All our products are made from the finest materials to ensure durability and performance.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                        <h4>Fast Shipping</h4>
                        <p>Quick and reliable delivery to get your products to you as soon as possible.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                        <h4>24/7 Support</h4>
                        <p>Our customer service team is always ready to help you with any questions or concerns.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
