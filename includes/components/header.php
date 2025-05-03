<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/functions.php";
require_once __DIR__ . "/../config/session.php";

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    setFlashMessage('error', 'Your session has expired. Please login again.');
    header('Location: /E-commerce_Belingheri_Barlascini/login.php');
    exit();
}
$_SESSION['last_activity'] = time();

// Get categories from database
$categories_sql = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_sql);
$categories = mysqli_fetch_all($categories_result, MYSQLI_ASSOC);

// Get cart count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $cart_count_sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
    $cart_count_stmt = mysqli_prepare($conn, $cart_count_sql);
    mysqli_stmt_bind_param($cart_count_stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($cart_count_stmt);
    $cart_count_result = mysqli_stmt_get_result($cart_count_stmt);
    $cart_count = mysqli_fetch_assoc($cart_count_result)['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Pool Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .nav-link {
            font-weight: 500;
            padding: 0.5rem 1rem !important;
        }
        .nav-link:hover {
            color: #0d6efd !important;
        }
        .dropdown-menu {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .dropdown-item {
            padding: 0.5rem 1.5rem;
        }
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
        }
        .alert {
            margin-bottom: 0;
            border-radius: 0;
        }
        .search-form {
            position: relative;
            min-width: 200px;
        }
        .search-form .form-control {
            padding-right: 40px;
        }
        .search-form .btn {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        @media (max-width: 991.98px) {
            .search-form {
                width: 100%;
                margin: 1rem 0;
            }
            .navbar-nav {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Flash Messages -->
    <?php
    $flash = getFlashMessage();
    if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/E-commerce_Belingheri_Barlascini/index.php">
                <i class="fas fa-store"></i> Premium Pool Shop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/E-commerce_Belingheri_Barlascini/index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="productsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-th-large"></i> Products
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="productsDropdown">
                            <li>
                                <a class="dropdown-item" href="/E-commerce_Belingheri_Barlascini/products.php">
                                    <i class="fas fa-th"></i> All Products
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <?php foreach ($categories as $category): ?>
                            <li>
                                <a class="dropdown-item" href="/E-commerce_Belingheri_Barlascini/products.php?category=<?php echo $category['id']; ?>">
                                    <i class="<?php echo $category['icon'] ?? 'fas fa-tag'; ?>"></i> <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/E-commerce_Belingheri_Barlascini/pages/contact.php">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </li>
                </ul>
                <form class="d-flex me-3 search-form" action="/E-commerce_Belingheri_Barlascini/products.php" method="GET">
                    <input class="form-control" type="search" name="search" placeholder="Search products...">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <div class="d-flex">
                    <a href="/E-commerce_Belingheri_Barlascini/cart/cart.php" class="btn btn-outline-light me-2 position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if($cart_count > 0): ?>
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <?php if(isLoggedIn()): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars(getCurrentUsername()); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="/E-commerce_Belingheri_Barlascini/account.php">
                                        <i class="fas fa-user-circle"></i> Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/E-commerce_Belingheri_Barlascini/orders/orders.php">
                                        <i class="fas fa-shopping-bag"></i> Orders
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/E-commerce_Belingheri_Barlascini/wishlist/wishlist.php">
                                        <i class="fas fa-heart"></i> Wishlist
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/E-commerce_Belingheri_Barlascini/addresses/addresses.php">
                                        <i class="fas fa-map-marker-alt"></i> Addresses
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/E-commerce_Belingheri_Barlascini/cart/cart.php">
                                        <i class="fas fa-shopping-cart"></i> Cart
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="/E-commerce_Belingheri_Barlascini/logout.php">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="/E-commerce_Belingheri_Barlascini/login.php" class="btn btn-outline-light">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 