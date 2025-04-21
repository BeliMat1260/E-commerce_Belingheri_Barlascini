<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "config/database.php";

// Debug: Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if product ID is provided
if (!isset($_GET['id'])) {
    die("No product ID provided");
}

$product_id = (int)$_GET['id'];

// Get product details
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $product_id);
if (!mysqli_stmt_execute($stmt)) {
    die("Execute failed: " . mysqli_stmt_error($stmt));
}

$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    die("Get result failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) === 0) {
    die("No product found with ID: " . $product_id);
}

$product = mysqli_fetch_assoc($result);

// Get related products from the same category
$sql = "SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Prepare failed for related products: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "si", $product['category'], $product_id);
if (!mysqli_stmt_execute($stmt)) {
    die("Execute failed for related products: " . mysqli_stmt_error($stmt));
}

$related_products = mysqli_stmt_get_result($stmt);
if (!$related_products) {
    die("Get result failed for related products: " . mysqli_error($conn));
}

// Include header
include 'includes/header.php';
?>

    <!-- Product Details Section -->
    <div class="container py-5">
        <a href="products.php<?php echo isset($_GET['category']) ? '?category=' . htmlspecialchars($_GET['category']) : ''; ?>" 
           class="back-to-products">
            <i class="fas fa-arrow-left"></i>
            Back to Products
        </a>
        <div class="row">
            <!-- Product Gallery -->
            <div class="col-md-6">
                <div class="product-gallery">
                    <?php
                    $product_dir = "assets/images/products/" . $product['id'];
                    $main_image = $product_dir . "/main.jpg";
                    $alt_image = $product_dir . "/alt.jpg";
                    $extra1_image = $product_dir . "/extra1.jpg";
                    $extra2_image = $product_dir . "/extra2.jpg";
                    
                    if (file_exists($main_image)): ?>
                        <div class="main-image-container mb-3">
                            <a href="<?php echo $main_image; ?>" 
                               data-lightbox="product-gallery" 
                               data-title="<?php echo htmlspecialchars($product['name']); ?>">
                                <img src="<?php echo $main_image; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="img-fluid rounded main-image">
                            </a>
                        </div>
                        <div class="thumbnail-container">
                            <a href="<?php echo $main_image; ?>" 
                               data-lightbox="product-gallery" 
                               data-title="<?php echo htmlspecialchars($product['name']); ?>">
                                <img src="<?php echo $main_image; ?>" 
                                     class="thumbnail active" 
                                     alt="Main view">
                            </a>
                            <?php if (file_exists($alt_image)): ?>
                                <a href="<?php echo $alt_image; ?>" 
                                   data-lightbox="product-gallery" 
                                   data-title="<?php echo htmlspecialchars($product['name']); ?>">
                                    <img src="<?php echo $alt_image; ?>" 
                                         class="thumbnail" 
                                         alt="Alternative view">
                                </a>
                            <?php endif; ?>
                            <?php if (file_exists($extra1_image)): ?>
                                <a href="<?php echo $extra1_image; ?>" 
                                   data-lightbox="product-gallery" 
                                   data-title="<?php echo htmlspecialchars($product['name']); ?>">
                                    <img src="<?php echo $extra1_image; ?>" 
                                         class="thumbnail" 
                                         alt="Extra view 1">
                                </a>
                            <?php endif; ?>
                            <?php if (file_exists($extra2_image)): ?>
                                <a href="<?php echo $extra2_image; ?>" 
                                   data-lightbox="product-gallery" 
                                   data-title="<?php echo htmlspecialchars($product['name']); ?>">
                                    <img src="<?php echo $extra2_image; ?>" 
                                         class="thumbnail" 
                                         alt="Extra view 2">
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-light rounded p-5 text-center">
                            <i class="fas fa-image fa-5x text-muted"></i>
                            <p class="mt-3">No image available</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Product Details -->
            <div class="col-md-6">
                <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="text-muted mb-2">Category: <?php echo ucwords(str_replace('_', ' ', $product['category'])); ?></p>
                <h3 class="text-primary mb-4">$<?php echo number_format($product['price'], 2); ?></h3>
                
                <div class="product-description">
                    <?php if (!empty($product['description'])): ?>
                        <p class="mb-4"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    <?php endif; ?>
                    
                    <div class="product-features">
                        <h4>Key Features</h4>
                        <ul>
                            <?php if (!empty($product['features'])): ?>
                                <?php 
                                $features = json_decode($product['features'] ?? '[]', true);
                                foreach ($features as $feature): 
                                ?>
                                    <li><?php echo htmlspecialchars($feature); ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Premium quality materials</li>
                                <li>Expert craftsmanship</li>
                                <li>Durable construction</li>
                                <li>Exceptional performance</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <form action="add_to_cart.php" method="POST" class="mb-4">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                               value="1" min="1" max="10" style="width: 100px;">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                </form>
                
                <div class="product-details">
                    <h4>Product Specifications</h4>
                    <div class="specifications-grid">
                        <?php if (!empty($product['material'])): ?>
                            <div class="spec-item">
                                <i class="fas fa-cube"></i>
                                <div>
                                    <span class="spec-label">Material</span>
                                    <span class="spec-value"><?php echo htmlspecialchars($product['material']); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($product['size'])): ?>
                            <div class="spec-item">
                                <i class="fas fa-ruler"></i>
                                <div>
                                    <span class="spec-label">Size</span>
                                    <span class="spec-value"><?php echo htmlspecialchars($product['size']); ?> mm</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($product['weight'])): ?>
                            <div class="spec-item">
                                <i class="fas fa-weight"></i>
                                <div>
                                    <span class="spec-label">Weight</span>
                                    <span class="spec-value">
                                        <?php 
                                        if ($product['category'] === 'pool_tables') {
                                            echo number_format($product['weight'] / 1000, 1) . ' kg';
                                        } else {
                                            echo number_format($product['weight'], 0) . ' g';
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($product['color'])): ?>
                            <div class="spec-item">
                                <i class="fas fa-palette"></i>
                                <div>
                                    <span class="spec-label">Color</span>
                                    <span class="spec-value"><?php echo htmlspecialchars($product['color']); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($product['capacity'])): ?>
                            <div class="spec-item">
                                <i class="fas fa-tint"></i>
                                <div>
                                    <span class="spec-label">Capacity</span>
                                    <span class="spec-value"><?php echo htmlspecialchars($product['capacity']); ?> ml</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($product['dimensions'])): ?>
                            <div class="spec-item">
                                <i class="fas fa-ruler-combined"></i>
                                <div>
                                    <span class="spec-label">Dimensions</span>
                                    <span class="spec-value"><?php echo htmlspecialchars($product['dimensions']); ?> cm</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products Section -->
        <?php if (mysqli_num_rows($related_products) > 0): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Related Products</h3>
            </div>
            <?php while ($related = mysqli_fetch_assoc($related_products)): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <?php if (!empty($related['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($related['image_url']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($related['name']); ?>">
                    <?php else: ?>
                        <div class="bg-light p-3 text-center">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($related['name']); ?></h5>
                        <p class="card-text text-primary">$<?php echo number_format($related['price'], 2); ?></p>
                        <a href="product.php?id=<?php echo $related['id']; ?>" class="btn btn-outline-primary">View Details</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>

<?php
// Include footer
include 'includes/footer.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize lightbox
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': 'Image %1 of %2',
            'fadeDuration': 300,
            'imageFadeDuration': 300,
            'showImageNumberLabel': true,
            'alwaysShowNavOnTouchDevices': true,
            'wrapAround': true,
            'disableScrolling': true,
            'fitImagesInViewport': true,
            'maxWidth': 800,
            'maxHeight': 600,
            'positionFromTop': 50,
            'resizeDuration': 200,
            'showImageNumberLabel': true,
            'albumLabel': 'Image %1 of %2'
        });

        // Add back button to lightbox
        const lightboxBackButton = document.createElement('a');
        lightboxBackButton.href = 'products.php<?php echo isset($_GET['category']) ? '?category=' . htmlspecialchars($_GET['category']) : ''; ?>';
        lightboxBackButton.className = 'lightbox-back-button';
        lightboxBackButton.innerHTML = '<i class="fas fa-arrow-left"></i> Back to Products';
        document.body.appendChild(lightboxBackButton);

        // Show/hide back button based on lightbox state
        document.addEventListener('start-ssr', function() {
            lightboxBackButton.style.display = 'none';
        });
        document.addEventListener('after-ssr', function() {
            lightboxBackButton.style.display = 'flex';
        });

        // Thumbnail click handler
        const thumbnails = document.querySelectorAll('.thumbnail');
        const mainImage = document.querySelector('.main-image');
        
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function(e) {
                e.preventDefault();
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                if (this !== mainImage) {
                    mainImage.src = this.src;
                }
            });
        });
    });
</script> 