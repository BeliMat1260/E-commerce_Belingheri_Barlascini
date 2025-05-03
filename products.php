<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once __DIR__ . "/includes/config/database.php";
require_once __DIR__ . "/includes/config/session.php";
require_once __DIR__ . "/includes/config/functions.php";

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
        (SELECT image_url FROM product_images WHERE product_id = p.id AND image_order = 1 LIMIT 1) as primary_image
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";

$params = [];
$types = "";

if ($category_id) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}

if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
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
    case 'name_desc':
        $sql .= " ORDER BY p.name DESC";
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

// Include header
include __DIR__ . '/includes/components/header.php';
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
                    <form action="/E-commerce_Belingheri_Barlascini/products.php" method="GET" id="filterForm">
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
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            <a href="/E-commerce_Belingheri_Barlascini/products.php" class="btn btn-outline-secondary">
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
            <div class="row">
                    <?php while ($product = mysqli_fetch_assoc($result)): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
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
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text text-primary fw-bold">$<?php echo number_format($product['price'], 2); ?></p>
                                    <p class="card-text">
                                        <small class="text-muted">Category: <?php echo htmlspecialchars($product['category_name']); ?></small>
                                    </p>
                                    <div class="d-grid gap-2">
                                        <a href="/E-commerce_Belingheri_Barlascini/pages/product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary">View Details</a>
                                        <?php if ($product['stock_quantity'] > 0): ?>
                                            <form action="/E-commerce_Belingheri_Barlascini/cart.php" method="POST">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" name="add_to_cart" class="btn btn-primary w-100">
                                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-secondary w-100" disabled>Out of Stock</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        No products found matching your criteria.
                    </div>
                </div>
                <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

<?php include __DIR__ . '/includes/components/footer.php'; ?> 