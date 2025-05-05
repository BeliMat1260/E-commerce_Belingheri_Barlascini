<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once __DIR__ . "/../includes/config/database.php";
require_once __DIR__ . "/../includes/config/session.php";
require_once __DIR__ . "/../includes/config/functions.php";

// Debug: Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: /E-commerce_Belingheri_Barlascini/products.php');
    exit();
}

// Get product details
$sql = "SELECT p.*, c.name as category_name,
        (SELECT image_url FROM product_images WHERE product_id = p.id AND image_order = 1 LIMIT 1) as primary_image,
        " . (isLoggedIn() ? "(SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = p.id) as in_wishlist" : "0 as in_wishlist") . "
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (isLoggedIn()) {
    mysqli_stmt_bind_param($stmt, "ii", $_SESSION['user_id'], $product_id);
} else {
    mysqli_stmt_bind_param($stmt, "i", $product_id);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header('Location: /E-commerce_Belingheri_Barlascini/products.php');
    exit();
}

// Get all product images
$images_sql = "SELECT * FROM product_images WHERE product_id = ? ORDER BY image_order";
$images_stmt = mysqli_prepare($conn, $images_sql);
mysqli_stmt_bind_param($images_stmt, "i", $product_id);
mysqli_stmt_execute($images_stmt);
$images_result = mysqli_stmt_get_result($images_stmt);
$images = mysqli_fetch_all($images_result, MYSQLI_ASSOC);

// Include header
include __DIR__ . '/../includes/components/header.php';
?>

<!-- Product Details Section -->
<div class="container py-5">
    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6">
            <!-- Main Image -->
            <div class="mb-4 d-flex justify-content-center position-relative">
                <?php if ($product['primary_image']): ?>
                    <img src="/E-commerce_Belingheri_Barlascini/<?php echo htmlspecialchars($product['primary_image']); ?>"
                         class="img-fluid rounded"
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         style="height: 400px; object-fit: contain;">
                    <?php if (isLoggedIn()): ?>
                        <button class="btn btn-sm position-absolute top-0 end-0 m-2 wishlist-btn <?php echo $product['in_wishlist'] ? 'btn-danger' : 'btn-outline-danger'; ?>"
                                data-product-id="<?php echo $product['id']; ?>"
                                title="<?php echo $product['in_wishlist'] ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>">
                            <i class="fas fa-heart"></i>
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 400px; width: 100%;">
                    <span class="text-muted">No image available</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Thumbnails -->
            <?php if (count($images) > 1): ?>
                <div class="row g-2 justify-content-center">
                    <?php foreach ($images as $image): ?>
                        <div class="col-3">
                            <div class="d-flex justify-content-center">
                                <img src="/E-commerce_Belingheri_Barlascini/<?php echo htmlspecialchars($image['image_url']); ?>"
                                     class="img-fluid rounded cursor-pointer"
                                     alt="<?php echo htmlspecialchars($product['name']); ?> - Image <?php echo $image['image_order']; ?>"
                                     style="height: 100px; object-fit: cover;"
                                     onclick="changeMainImage(this.src)">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="col-md-6">
            <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="text-muted mb-3">Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
            <h2 class="mb-4">$<?php echo number_format($product['price'], 2); ?></h2>
            
            <div class="mb-4">
                <h5>Description</h5>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>

            <?php if ($product['size']): ?>
                <div class="mb-3">
                    <strong>Size:</strong> <?php echo htmlspecialchars($product['size']); ?>
                </div>
            <?php endif; ?>

            <?php if ($product['weight']): ?>
                <div class="mb-3">
                    <strong>Weight:</strong> <?php echo htmlspecialchars($product['weight']); ?> g
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <strong>Availability:</strong>
                <?php if ($product['stock_quantity'] > 0): ?>
                    <span class="text-success">In Stock (<?php echo $product['stock_quantity']; ?> available)</span>
                <?php else: ?>
                    <span class="text-danger">Out of Stock</span>
                <?php endif; ?>
            </div>

            <?php if ($product['stock_quantity'] > 0): ?>
                <?php if (isLoggedIn()): ?>
                    <form action="/E-commerce_Belingheri_Barlascini/cart/add_to_cart.php" method="POST" class="mb-4">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <label for="quantity" class="form-label">Quantity:</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" 
                                       value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" 
                                       style="width: 80px;">
                            </div>
                            <div class="col">
                                <button type="submit" name="add_to_cart" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="mb-4">
                        <a href="/E-commerce_Belingheri_Barlascini/auth/login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Add to Cart
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/components/footer.php'; ?> 

<script>
function changeMainImage(src) {
    const mainImage = document.querySelector('.col-md-6 img.img-fluid');
    if (mainImage) {
        mainImage.src = src;
    }
}

// Wishlist functionality
document.addEventListener('DOMContentLoaded', function() {
    const wishlistButton = document.querySelector('.wishlist-btn');
    if (wishlistButton) {
        wishlistButton.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const isInWishlist = this.classList.contains('btn-danger');
            const action = isInWishlist ? 'remove_from' : 'add_to';
            
            // Disable button during request
            this.disabled = true;
            
            fetch(`/E-commerce_Belingheri_Barlascini/wishlist/${action}_wishlist.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Error parsing JSON:', text);
                    throw new Error('Invalid JSON response');
                }
            }))
            .then(data => {
                if (data.success) {
                    // Toggle button state
                    if (isInWishlist) {
                        this.classList.remove('btn-danger');
                        this.classList.add('btn-outline-danger');
                        this.title = 'Add to Wishlist';
                    } else {
                        this.classList.add('btn-danger');
                        this.classList.remove('btn-outline-danger');
                        this.title = 'Remove from Wishlist';
                    }
                    
                    // Show success message
                    const message = isInWishlist ? 'Product removed from wishlist' : 'Product added to wishlist';
                    const toast = document.createElement('div');
                    toast.className = 'position-fixed bottom-0 end-0 p-3';
                    toast.style.zIndex = '5';
                    toast.innerHTML = `
                        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header">
                                <i class="fas fa-heart me-2"></i>
                                <strong class="me-auto">Wishlist</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                ${message}
                            </div>
                        </div>
                    `;
                    document.body.appendChild(toast);
                    
                    // Remove toast after 3 seconds
                    setTimeout(() => {
                        toast.remove();
                    }, 3000);
                } else {
                    if (data.message && data.message.includes('log in')) {
                        window.location.href = '/E-commerce_Belingheri_Barlascini/auth/login.php';
                    } else {
                        alert(data.message || 'An error occurred');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the wishlist. Please try again.');
            })
            .finally(() => {
                // Re-enable button after request completes
                this.disabled = false;
            });
        });
    }
});
</script> 