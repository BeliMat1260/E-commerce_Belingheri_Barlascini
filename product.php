<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "config/database.php";
require_once "config/session.php";

// Debug: Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = (int)$_GET['id'];

// Get product details with category name and ratings
$sql = "SELECT p.*, c.name as category_name,
        (SELECT COUNT(*) FROM product_reviews WHERE product_id = p.id) as review_count,
        (SELECT AVG(rating) FROM product_reviews WHERE product_id = p.id) as avg_rating,
        (SELECT image_url FROM product_images WHERE product_id = p.id AND image_order = 1 LIMIT 1) as primary_image
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ? AND p.is_active = 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    header("Location: products.php");
    exit();
}

$product = mysqli_fetch_assoc($result);

// Get product images with order number
$images_sql = "SELECT *, 
              (SELECT COUNT(*) + 1 FROM product_images pi2 
               WHERE pi2.product_id = pi1.product_id 
               AND pi2.id < pi1.id) as image_order 
              FROM product_images pi1 
              WHERE product_id = ? 
              ORDER BY image_order ASC";
$images_stmt = mysqli_prepare($conn, $images_sql);
mysqli_stmt_bind_param($images_stmt, "i", $product_id);
mysqli_stmt_execute($images_stmt);
$images_result = mysqli_stmt_get_result($images_stmt);

// Get product reviews with user information
$reviews_sql = "SELECT r.*, u.first_name, u.last_name, u.profile_image 
                FROM product_reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = ? 
                ORDER BY r.created_at DESC";
$reviews_stmt = mysqli_prepare($conn, $reviews_sql);
mysqli_stmt_bind_param($reviews_stmt, "i", $product_id);
mysqli_stmt_execute($reviews_stmt);
$reviews_result = mysqli_stmt_get_result($reviews_stmt);

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && isLoggedIn()) {
    $rating = (int)$_POST['rating'];
    $review = trim($_POST['review']);
    
    $errors = [];
    
    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Please select a valid rating.';
    }
    
    if (empty($review)) {
        $errors[] = 'Please write your review.';
    } elseif (strlen($review) < 10) {
        $errors[] = 'Review must be at least 10 characters long.';
    }
    
    // Check if user has already reviewed this product
    $check_review_sql = "SELECT id FROM product_reviews WHERE product_id = ? AND user_id = ?";
    $check_review_stmt = mysqli_prepare($conn, $check_review_sql);
    mysqli_stmt_bind_param($check_review_stmt, "ii", $product_id, $_SESSION['user_id']);
    mysqli_stmt_execute($check_review_stmt);
    if (mysqli_stmt_get_result($check_review_stmt)->num_rows > 0) {
        $errors[] = 'You have already reviewed this product.';
    }
    
    if (empty($errors)) {
        $insert_sql = "INSERT INTO product_reviews (product_id, user_id, rating, review) VALUES (?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($insert_stmt, "iiis", $product_id, $_SESSION['user_id'], $rating, $review);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            setFlashMessage('success', 'Thank you for your review!');
            header("Location: product.php?id=" . $product_id);
            exit();
        } else {
            setFlashMessage('error', 'Failed to submit review. Please try again.');
        }
    } else {
        setFlashMessage('error', implode('<br>', $errors));
    }
}

// Get related products from the same category
$related_sql = "SELECT p.*, 
                (SELECT COUNT(*) FROM product_reviews WHERE product_id = p.id) as review_count,
                (SELECT AVG(rating) FROM product_reviews WHERE product_id = p.id) as avg_rating,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND image_order = 1 LIMIT 1) as primary_image
                FROM products p 
                WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 
                LIMIT 4";
$related_stmt = mysqli_prepare($conn, $related_sql);
mysqli_stmt_bind_param($related_stmt, "ii", $product['category_id'], $product_id);
mysqli_stmt_execute($related_stmt);
$related_products = mysqli_stmt_get_result($related_stmt);

// Include header
include 'includes/header.php';
?>

    <!-- Product Details Section -->
    <div class="container py-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                <?php if ($product['category_name']): ?>
                    <li class="breadcrumb-item"><a href="products.php?category_id=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Images -->
            <div class="col-md-6">
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php 
                        $has_images = false;
                        $first = true;
                        mysqli_data_seek($images_result, 0);
                        while ($image = mysqli_fetch_assoc($images_result)): 
                            $has_images = true;
                        ?>
                            <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                                <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                                     class="d-block w-100" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     style="height: 400px; object-fit: cover;">
                            </div>
                        <?php 
                        $first = false;
                        endwhile; 
                        
                        if (!$has_images): ?>
                            <div class="carousel-item active">
                                <div class="d-flex align-items-center justify-content-center bg-light" style="height: 400px;">
                                    <span class="text-muted h4">No image available</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($has_images && mysqli_num_rows($images_result) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    <?php endif; ?>
                </div>
                
                <!-- Thumbnails -->
                <?php if ($has_images && mysqli_num_rows($images_result) > 1): ?>
                    <div class="row mt-3">
                        <?php 
                        mysqli_data_seek($images_result, 0);
                        while ($image = mysqli_fetch_assoc($images_result)): 
                        ?>
                            <div class="col-3">
                                <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                                     class="img-thumbnail thumbnail" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     style="height: 80px; object-fit: cover; cursor: pointer;">
                                <small class="d-block text-center text-muted">Image <?php echo $image['image_order']; ?></small>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Product Info -->
            <div class="col-md-6">
                <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="text-muted">SKU: <?php echo htmlspecialchars($product['sku']); ?></p>
                
                <div class="mb-4">
                    <h4 class="mb-2">Description</h4>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
                
                <div class="mb-4">
                    <h4 class="mb-2">Details</h4>
                    <ul class="list-unstyled">
                        <?php if ($product['material']): ?>
                            <li><strong>Material:</strong> <?php echo htmlspecialchars($product['material']); ?></li>
                        <?php endif; ?>
                        <?php if ($product['size']): ?>
                            <li><strong>Size:</strong> <?php echo htmlspecialchars($product['size']); ?></li>
                        <?php endif; ?>
                        <?php if ($product['weight']): ?>
                            <li><strong>Weight:</strong> <?php echo htmlspecialchars($product['weight']); ?> kg</li>
                        <?php endif; ?>
                        <?php if ($product['color']): ?>
                            <li><strong>Color:</strong> <?php echo htmlspecialchars($product['color']); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="mb-4">
                    <h4 class="mb-2">Price</h4>
                    <?php if ($product['discount_price']): ?>
                        <p class="text-decoration-line-through text-muted">$<?php echo number_format($product['price'], 2); ?></p>
                        <p class="h3 text-danger">$<?php echo number_format($product['discount_price'], 2); ?></p>
                    <?php else: ?>
                        <p class="h3">$<?php echo number_format($product['price'], 2); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <h4 class="mb-2">Availability</h4>
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <p class="text-success">In Stock (<?php echo $product['stock_quantity']; ?> available)</p>
                    <?php else: ?>
                        <p class="text-danger">Out of Stock</p>
                    <?php endif; ?>
                </div>
                
                <?php if ($product['stock_quantity'] > 0): ?>
                    <form action="add_to_cart.php" method="POST" class="mb-4">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label for="quantity" class="col-form-label">Quantity:</label>
                            </div>
                            <div class="col-auto">
                                <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Add to Cart</button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
                
                <div class="mb-4">
                    <h4 class="mb-2">Share</h4>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-primary"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-outline-info"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-outline-danger"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reviews Section -->
        <div class="row mt-5">
            <div class="col-12">
                <h3>Customer Reviews</h3>
                
                <?php
                // Check if user has purchased the product
                $can_review = false;
                if (isLoggedIn()) {
                    $check_purchase_sql = "SELECT COUNT(*) as purchased 
                                         FROM orders o 
                                         JOIN order_items oi ON o.id = oi.order_id 
                                         WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'completed'";
                    $check_purchase_stmt = mysqli_prepare($conn, $check_purchase_sql);
                    mysqli_stmt_bind_param($check_purchase_stmt, "ii", $_SESSION['user_id'], $product_id);
                    mysqli_stmt_execute($check_purchase_stmt);
                    $purchase_result = mysqli_stmt_get_result($check_purchase_stmt);
                    $purchase_data = mysqli_fetch_assoc($purchase_result);
                    $can_review = $purchase_data['purchased'] > 0;
                    
                    // Check if user has already reviewed
                    $has_reviewed = false;
                    $check_review_sql = "SELECT id FROM product_reviews WHERE product_id = ? AND user_id = ?";
                    $check_review_stmt = mysqli_prepare($conn, $check_review_sql);
                    mysqli_stmt_bind_param($check_review_stmt, "ii", $product_id, $_SESSION['user_id']);
                    mysqli_stmt_execute($check_review_stmt);
                    $review_check_result = mysqli_stmt_get_result($check_review_stmt);
                    if (mysqli_num_rows($review_check_result) > 0) {
                        $can_review = false;
                        $has_reviewed = true;
                    }
                }
                ?>
                
                <?php if (isLoggedIn() && $can_review): ?>
                    <!-- Review Form -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Write a Review</h5>
                            <form method="POST" action="product.php?id=<?php echo $product_id; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Rating</label>
                                    <div class="rating">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" required>
                                            <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="review" class="form-label">Your Review</label>
                                    <textarea class="form-control" id="review" name="review" rows="3" required></textarea>
                                    <div class="form-text">Minimum 10 characters required.</div>
                                </div>
                                <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                            </form>
                        </div>
                    </div>
                <?php elseif (isLoggedIn() && !$can_review && !$has_reviewed): ?>
                    <div class="alert alert-info">
                        You can only review products that you have purchased.
                    </div>
                <?php elseif (!isLoggedIn()): ?>
                    <div class="alert alert-info">
                        Please <a href="login.php">login</a> to write a review.
                    </div>
                <?php endif; ?>

                <!-- Reviews List -->
                <?php if (mysqli_num_rows($reviews_result) > 0): ?>
                    <div class="reviews-container">
                        <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center">
                                            <?php if ($review['profile_image']): ?>
                                                <img src="<?php echo htmlspecialchars($review['profile_image']); ?>" 
                                                     class="rounded-circle me-2" 
                                                     alt="<?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?>"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-secondary me-2 d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></h6>
                                                <small class="text-muted"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></small>
                                            </div>
                                        </div>
                                        <div class="rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="card-text"><?php echo nl2br(htmlspecialchars($review['review'])); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-comments fa-3x mb-3 text-muted"></i>
                        <h5>No Reviews Available</h5>
                        <p class="mb-0">Be the first to review this product!</p>
                    </div>
                <?php endif; ?>
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
                    <div class="position-relative">
                        <?php if ($related['primary_image']): ?>
                            <img src="<?php echo htmlspecialchars($related['primary_image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($related['name']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                <span class="text-muted">No image available</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($related['discount_price']): ?>
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-danger">
                                    <?php 
                                    $discount = (($related['price'] - $related['discount_price']) / $related['price']) * 100;
                                    echo round($discount) . '% OFF';
                                    ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($related['name']); ?></h5>
                        
                        <!-- Rating -->
                        <div class="mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $related['avg_rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                            <?php endfor; ?>
                            <span class="text-muted small">(<?php echo $related['review_count']; ?> reviews)</span>
                        </div>
                        
                        <!-- Price -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <?php if ($related['discount_price']): ?>
                                    <span class="text-decoration-line-through text-muted">$<?php echo number_format($related['price'], 2); ?></span>
                                    <span class="text-danger fw-bold">$<?php echo number_format($related['discount_price'], 2); ?></span>
                                <?php else: ?>
                                    <span class="fw-bold">$<?php echo number_format($related['price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="badge bg-<?php echo $related['stock_quantity'] > 0 ? 'success' : 'danger'; ?>">
                                <?php echo $related['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-grid gap-2">
                            <a href="product.php?id=<?php echo $related['id']; ?>" class="btn btn-outline-primary">View Details</a>
                            <?php if ($related['stock_quantity'] > 0): ?>
                                <form action="add_to_cart.php" method="POST" class="d-grid">
                                    <input type="hidden" name="product_id" value="<?php echo $related['id']; ?>">
                                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                                </form>
                            <?php endif; ?>
                        </div>
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

        // Quantity increment/decrement
        window.incrementQuantity = function() {
            const input = document.getElementById('quantity');
            const max = parseInt(input.getAttribute('max'));
            const current = parseInt(input.value);
            if (current < max) {
                input.value = current + 1;
            }
        };

        window.decrementQuantity = function() {
            const input = document.getElementById('quantity');
            const current = parseInt(input.value);
            if (current > 1) {
                input.value = current - 1;
            }
        };

        // Add to wishlist functionality
        const wishlistButton = document.querySelector('.add-to-wishlist');
        if (wishlistButton) {
            wishlistButton.addEventListener('click', function() {
                const productId = this.dataset.productId;
                fetch('add_to_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.classList.remove('btn-outline-primary');
                        this.classList.add('btn-danger');
                        this.innerHTML = '<i class="fas fa-heart"></i> Added to Wishlist';
                    } else {
                        alert(data.message || 'Failed to add to wishlist.');
                    }
                });
            });
        }

        // Thumbnail click handler
        const thumbnails = document.querySelectorAll('.thumbnail');
        const carousel = document.querySelector('#productCarousel');
        
        thumbnails.forEach((thumb, index) => {
            thumb.addEventListener('click', function() {
                const carouselInstance = new bootstrap.Carousel(carousel);
                carouselInstance.to(index);
            });
        });
    });
</script>

<style>
.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating input {
    display: none;
}

.rating label {
    cursor: pointer;
    font-size: 1.5rem;
    color: #ddd;
    padding: 0 0.1em;
}

.rating input:checked ~ label,
.rating label:hover,
.rating label:hover ~ label {
    color: #ffc107;
}

.thumbnail {
    transition: all 0.3s ease;
}

.thumbnail:hover {
    transform: scale(1.05);
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
</style> 