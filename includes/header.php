<?php
session_start();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Required JavaScript files -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <style>
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .search-form {
            position: relative;
            width: 300px;
        }
        .search-form input {
            padding-right: 40px;
        }
        .search-form button {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            border: none;
            background: none;
            padding: 0 15px;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            font-size: 0.7rem;
        }
        .nav-link {
            position: relative;
            color: rgba(255, 255, 255, 0.85);
            padding: 0.5rem 1rem;
        }
        .nav-link:hover {
            color: #fff;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #fff;
            transition: width 0.3s;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .dropdown-item {
            color: #212529;
            padding: 0.5rem 1rem;
        }
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
        }
        .dropdown-item i {
            margin-right: 0.5rem;
            width: 1.25rem;
            text-align: center;
        }
        
        /* Simple CSS Dropdown */
        .products-dropdown {
            position: relative;
        }
        .products-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            min-width: 200px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: none;
            z-index: 1000;
        }
        .products-dropdown:hover .products-dropdown-menu {
            display: block;
        }
        .products-dropdown-menu a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
        }
        .products-dropdown-menu a:hover {
            background: #f8f9fa;
        }
        
        /* Stili per il menu a tendina */
        .categories-menu {
            position: relative;
            display: inline-block;
        }
        .categories-button {
            background-color: #0d6efd;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .categories-button:hover {
            background-color: #0b5ed7;
        }
        .categories-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            border-radius: 5px;
            padding: 5px 0;
            display: none;
            z-index: 1000;
        }
        .categories-menu:hover .categories-dropdown {
            display: block;
        }
        .category-link {
            padding: 10px 20px;
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .category-link:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
        }
        .category-link i {
            width: 20px;
            text-align: center;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productsDropdown = document.querySelector('.products-dropdown');
            const dropdownMenu = document.querySelector('.products-dropdown-menu');
            
            if (productsDropdown && dropdownMenu) {
                productsDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    dropdownMenu.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!productsDropdown.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }

            const categoryButton = document.querySelector('.category-button');
            const categoryMenu = document.querySelector('.category-menu');
            
            if (categoryButton && categoryMenu) {
                categoryButton.addEventListener('click', function() {
                    categoryMenu.classList.toggle('show');
                });
                
                // Chiudi il menu quando si clicca fuori
                document.addEventListener('click', function(e) {
                    if (!categoryButton.contains(e.target) && !categoryMenu.contains(e.target)) {
                        categoryMenu.classList.remove('show');
                    }
                });
            }
        });
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-store"></i> Premium Pool Shop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-th-large"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">
                            <i class="fas fa-info-circle"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </li>
                </ul>
                <form class="d-flex me-3" action="products.php" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search products...">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <div class="d-flex">
                    <a href="cart.php" class="btn btn-outline-light me-2 position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle"><?php echo count($_SESSION['cart']); ?></span>
                        <?php endif; ?>
                    </a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> My Account
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="account.php">
                                        <i class="fas fa-user-circle"></i> Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="orders.php">
                                        <i class="fas fa-shopping-bag"></i> Orders
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="logout.php">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</body>
</html> 