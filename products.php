<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if database connection file exists
if (!file_exists("config/database.php")) {
    die("Error: database.php configuration file not found");
}

// Check if session file exists
if (!file_exists("config/session.php")) {
    die("Error: session.php configuration file not found");
}

require_once "config/database.php";
require_once "config/session.php";

// Verify database connection
if (!isset($conn) || !$conn) {
    die("Error: Database connection failed");
}

// Get filters with proper validation
$category_id = isset($_GET['category_id']) && !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : null;
$min_rating = isset($_GET['min_rating']) && is_numeric($_GET['min_rating']) ? (int)$_GET['min_rating'] : null;
$in_stock = isset($_GET['in_stock']) ? true : false;

// Build SQL query with proper joins and conditions
$sql = "SELECT p.*, c.name as category_name,
        (SELECT COUNT(*) FROM product_reviews WHERE product_id = p.id) as review_count,
        COALESCE((SELECT AVG(rating) FROM product_reviews WHERE product_id = p.id), 0) as avg_rating,
        (SELECT image_url FROM product_images WHERE product_id = p.id AND image_order = 1 LIMIT 1) as primary_image
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.is_active = 1";

$params = [];
$types = "";

if ($category_id) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}

if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if ($min_price !== null) {
    $sql .= " AND p.price >= ?";
    $params[] = $min_price;
    $types .= "d";
}

if ($max_price !== null) {
    $sql .= " AND p.price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

if ($min_rating !== null) {
    $sql .= " AND (SELECT AVG(rating) FROM product_reviews WHERE product_id = p.id) >= ?";
    $params[] = $min_rating;
    $types .= "i";
}

if ($in_stock) {
    $sql .= " AND p.stock_quantity > 0";
}

// Add sorting with proper column references
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'rating_desc':
        $sql .= " ORDER BY avg_rating DESC, review_count DESC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY p.name DESC";
        break;
    case 'newest':
        $sql .= " ORDER BY p.created_at DESC";
        break;
    default:
        $sql .= " ORDER BY p.name ASC";
}

try {
$stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($conn));
    }

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error executing statement: " . mysqli_stmt_error($stmt));
    }
    
$result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        throw new Exception("Error getting result: " . mysqli_stmt_error($stmt));
    }
} catch (Exception $e) {
    // Log the error and show a user-friendly message
    error_log("Database error: " . $e->getMessage());
    $error_message = "An error occurred while loading products. Please try again later.";
}

// Get categories for filter
$categories_sql = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_sql);
if (!$categories_result) {
    error_log("Error fetching categories: " . mysqli_error($conn));
}

// Check if header file exists
if (!file_exists('includes/header.php')) {
    die("Error: header.php file not found");
}

// Include header
include 'includes/header.php';
?>

<!-- Products Section -->
<div class="container py-5">
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger fade-in">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Filters</h5>
                    <form action="products.php" method="GET" id="filterForm">
                        <!-- Search -->
                        <div class="mb-4">
                            <label for="search" class="form-label fw-medium">Search</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-secondary"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="search" name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>"
                                       placeholder="Search products...">
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="mb-4">
                            <label for="category_id" class="form-label fw-medium">Category</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">All Categories</option>
                                <?php 
                                if ($categories_result) {
                                    while ($category = mysqli_fetch_assoc($categories_result)): 
                                ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                            <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php 
                                    endwhile;
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">$</span>
                                        <input type="number" class="form-control border-start-0" name="min_price" 
                                               placeholder="Min" value="<?php echo $min_price; ?>"
                                               min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">$</span>
                                        <input type="number" class="form-control border-start-0" name="max_price" 
                                               placeholder="Max" value="<?php echo $max_price; ?>"
                                               min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="mb-4">
                            <label for="min_rating" class="form-label fw-medium">Minimum Rating</label>
                            <select class="form-select" id="min_rating" name="min_rating">
                                <option value="">Any Rating</option>
                                <option value="4" <?php echo $min_rating == 4 ? 'selected' : ''; ?>>
                                    <i class="fas fa-star"></i> 4+ Stars
                                </option>
                                <option value="3" <?php echo $min_rating == 3 ? 'selected' : ''; ?>>
                                    <i class="fas fa-star"></i> 3+ Stars
                                </option>
                                <option value="2" <?php echo $min_rating == 2 ? 'selected' : ''; ?>>
                                    <i class="fas fa-star"></i> 2+ Stars
                                </option>
                                <option value="1" <?php echo $min_rating == 1 ? 'selected' : ''; ?>>
                                    <i class="fas fa-star"></i> 1+ Stars
                                </option>
                            </select>
                        </div>

                        <!-- Stock Filter -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="in_stock" name="in_stock" 
                                       <?php echo $in_stock ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="in_stock">
                                    In Stock Only
                                </label>
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="mb-4">
                            <label for="sort" class="form-label fw-medium">Sort By</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                                <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                                <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price (Low to High)</option>
                                <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price (High to Low)</option>
                                <option value="rating_desc" <?php echo $sort == 'rating_desc' ? 'selected' : ''; ?>>Rating (High to Low)</option>
                                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            <a href="products.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-md-9">
            <?php if (isset($result) && mysqli_num_rows($result) > 0): ?>
            <div class="row" id="productsGrid">
                    <?php while ($product = mysqli_fetch_assoc($result)): ?>
                        <div class="col-md-4 mb-4 product-item" 
                             data-price="<?php echo $product['price']; ?>"
                             data-rating="<?php echo $product['avg_rating']; ?>"
                             data-date="<?php echo strtotime($product['created_at']); ?>">
                            <div class="card h-100 fade-in">
                                <div class="position-relative">
                                    <?php if ($product['primary_image']): ?>
                                        <img src="<?php echo htmlspecialchars($product['primary_image']); ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                            <span class="text-muted">No image available</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($product['discount_price']): ?>
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-danger">
                                                <?php 
                                                $discount = (($product['price'] - $product['discount_price']) / $product['price']) * 100;
                                                echo round($discount) . '% OFF';
                                                ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isLoggedIn()): ?>
                                        <button class="btn btn-sm btn-outline-primary position-absolute top-0 start-0 m-2 add-to-wishlist"
                                                data-product-id="<?php echo $product['id']; ?>"
                                                title="Add to Wishlist">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text text-muted small">SKU: <?php echo htmlspecialchars($product['sku']); ?></p>
                                    
                                    <!-- Rating -->
                                    <div class="mb-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $product['avg_rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                        <?php endfor; ?>
                                        <span class="text-muted small">(<?php echo $product['review_count']; ?> reviews)</span>
                                    </div>

                                    <!-- Price -->
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <?php if ($product['discount_price']): ?>
                                                <span class="text-decoration-line-through text-muted">$<?php echo number_format($product['price'], 2); ?></span>
                                                <span class="text-danger fw-bold">$<?php echo number_format($product['discount_price'], 2); ?></span>
                                            <?php else: ?>
                                                <span class="fw-bold">$<?php echo number_format($product['price'], 2); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="badge bg-<?php echo $product['stock_quantity'] > 0 ? 'success' : 'danger'; ?>">
                                            <?php echo $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <div class="d-grid gap-2">
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary">View Details</a>
                                        <?php if ($product['stock_quantity'] > 0): ?>
                                            <form action="add_to_cart.php" method="POST" class="d-grid">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-primary">Add to Cart</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="alert alert-info fade-in">
                    <i class="fas fa-info-circle me-2"></i>
                            No products found matching your criteria.
                    </div>
                <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to wishlist functionality
    const wishlistButtons = document.querySelectorAll('.add-to-wishlist');
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
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
                    this.innerHTML = '<i class="fas fa-heart"></i>';
                } else {
                    alert(data.message || 'Failed to add to wishlist.');
                }
            });
        });
    });

    // Price range validation
    const minPriceInput = document.querySelector('input[name="min_price"]');
    const maxPriceInput = document.querySelector('input[name="max_price"]');

    function validatePriceRange() {
        const minPrice = parseFloat(minPriceInput.value);
        const maxPrice = parseFloat(maxPriceInput.value);

        if (minPrice && maxPrice && minPrice > maxPrice) {
            maxPriceInput.setCustomValidity('Maximum price must be greater than minimum price');
        } else {
            maxPriceInput.setCustomValidity('');
        }
    }

    minPriceInput.addEventListener('input', validatePriceRange);
    maxPriceInput.addEventListener('input', validatePriceRange);

    // Form submission with loading state
    const filterForm = document.getElementById('filterForm');
    const submitButton = filterForm.querySelector('button[type="submit"]');
    
    filterForm.addEventListener('submit', function(e) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Applying Filters...';
    });
});
</script>

<?php 
// Check if footer file exists
if (!file_exists('includes/footer.php')) {
    die("Error: footer.php file not found");
}

include 'includes/footer.php'; 
?> 