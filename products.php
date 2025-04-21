<?php
session_start();
require_once "config/database.php";

// Get category filter if set
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build SQL query
$sql = "SELECT * FROM products";
if ($category) {
    $sql .= " WHERE category = ?";
}

$stmt = mysqli_prepare($conn, $sql);
if ($category) {
    mysqli_stmt_bind_param($stmt, "s", $category);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Pool & Water Bottles Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .product-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .product-card img {
            height: 250px;
            object-fit: cover;
        }
        .product-card .card-body {
            padding: 1.5rem;
        }
        .product-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .product-spec {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .product-spec i {
            width: 20px;
            text-align: center;
            margin-right: 5px;
        }
        .category-filter {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .sort-options {
            margin-bottom: 1rem;
        }
        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .product-image-container {
            position: relative;
            height: 250px;
            overflow: hidden;
        }
        .product-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 0.3s;
            position: absolute;
            top: 0;
            left: 0;
        }
        .product-image-container .main-image {
            opacity: 1;
        }
        .product-image-container .alt-image,
        .product-image-container .extra1-image,
        .product-image-container .extra2-image {
            opacity: 0;
        }
        .product-image-container:hover .main-image {
            opacity: 0;
        }
        .product-image-container:hover .alt-image {
            opacity: 1;
        }
        .product-image-container:hover .extra1-image {
            opacity: 0;
        }
        .product-image-container:hover .extra2-image {
            opacity: 0;
        }
        .product-image-container:hover .alt-image {
            animation: fadeInOut 3s infinite;
        }
        .product-image-container:hover .extra1-image {
            animation: fadeInOut 3s infinite 1s;
        }
        .product-image-container:hover .extra2-image {
            animation: fadeInOut 3s infinite 2s;
        }
        @keyframes fadeInOut {
            0% { opacity: 0; }
            20% { opacity: 1; }
            40% { opacity: 0; }
            100% { opacity: 0; }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <h1 class="mb-4">Our Products</h1>
        
        <!-- Category Filter and Sort Options -->
        <div class="category-filter">
            <div class="row">
                <div class="col-md-8">
                    <div class="btn-group">
                        <a href="products.php" class="btn btn-outline-primary <?php echo !$category ? 'active' : ''; ?>">
                            <i class="fas fa-th-large"></i> All Products
                        </a>
                        <a href="products.php?category=pool_balls" class="btn btn-outline-primary <?php echo $category === 'pool_balls' ? 'active' : ''; ?>">
                            <i class="fas fa-basketball-ball"></i> Pool Balls
                        </a>
                        <a href="products.php?category=pool_tables" class="btn btn-outline-primary <?php echo $category === 'pool_tables' ? 'active' : ''; ?>">
                            <i class="fas fa-table"></i> Pool Tables
                        </a>
                        <a href="products.php?category=water_bottles" class="btn btn-outline-primary <?php echo $category === 'water_bottles' ? 'active' : ''; ?>">
                            <i class="fas fa-wine-bottle"></i> Water Bottles
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sort-options">
                        <select class="form-select" onchange="window.location.href=this.value">
                            <option value="products.php?sort=name_asc">Sort by Name (A-Z)</option>
                            <option value="products.php?sort=name_desc">Sort by Name (Z-A)</option>
                            <option value="products.php?sort=price_asc">Sort by Price (Low to High)</option>
                            <option value="products.php?sort=price_desc">Sort by Price (High to Low)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row">
            <?php while ($product = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card h-100">
                        <?php if ($product['is_new']): ?>
                            <span class="product-badge bg-success">NEW</span>
                        <?php endif; ?>
                        <?php if ($product['discount'] > 0): ?>
                            <span class="product-badge bg-danger">-<?php echo $product['discount']; ?>%</span>
                        <?php endif; ?>
                        <div class="product-image-container">
                            <?php
                            $product_dir = "assets/images/products/" . $product['id'];
                            $main_image = $product_dir . "/main.jpg";
                            $alt_image = $product_dir . "/alt.jpg";
                            $extra1_image = $product_dir . "/extra1.jpg";
                            $extra2_image = $product_dir . "/extra2.jpg";
                            
                            if (file_exists($main_image)): ?>
                                <img src="<?php echo $main_image; ?>" 
                                     class="card-img-top main-image" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php if (file_exists($alt_image)): ?>
                                    <img src="<?php echo $alt_image; ?>" 
                                         class="card-img-top alt-image" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php endif; ?>
                                <?php if (file_exists($extra1_image)): ?>
                                    <img src="<?php echo $extra1_image; ?>" 
                                         class="card-img-top extra1-image" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php endif; ?>
                                <?php if (file_exists($extra2_image)): ?>
                                    <img src="<?php echo $extra2_image; ?>" 
                                         class="card-img-top extra2-image" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="bg-light rounded p-5 text-center">
                                    <i class="fas fa-image fa-5x text-muted"></i>
                                    <p class="mt-3">No image available</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-muted mb-3"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                            <div class="product-spec mb-3">
                                <?php if ($product['category'] === 'pool_balls'): ?>
                                    <p><i class="fas fa-ruler"></i> Size: <?php echo htmlspecialchars($product['size']); ?> mm</p>
                                    <p><i class="fas fa-weight"></i> Weight: <?php echo number_format($product['weight'], 0); ?> g</p>
                                <?php elseif ($product['category'] === 'pool_tables'): ?>
                                    <p><i class="fas fa-ruler-combined"></i> Dimensions: <?php echo htmlspecialchars($product['dimensions']); ?> cm</p>
                                    <p><i class="fas fa-weight"></i> Weight: <?php echo number_format($product['weight'] / 1000, 1); ?> kg</p>
                                <?php elseif ($product['category'] === 'water_bottles'): ?>
                                    <p><i class="fas fa-tint"></i> Capacity: <?php echo htmlspecialchars($product['capacity']); ?> ml</p>
                                    <p><i class="fas fa-weight"></i> Weight: <?php echo number_format($product['weight'], 0); ?> g</p>
                                <?php endif; ?>
                                <p><i class="fas fa-cube"></i> Material: <?php echo htmlspecialchars($product['material']); ?></p>
                                <p><i class="fas fa-palette"></i> Color: <?php echo htmlspecialchars($product['color']); ?></p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="product-price">$<?php echo number_format($product['price'], 2); ?></span>
                                    <?php if ($product['discount'] > 0): ?>
                                        <span class="text-muted text-decoration-line-through ms-2">$<?php echo number_format($product['price'] * (1 + $product['discount']/100), 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 