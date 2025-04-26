<?php
// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Ottieni il nome della pagina corrente
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="col-md-3">
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Account Menu</h5>
            <div class="list-group">
                <a href="account.php" class="list-group-item list-group-item-action <?php echo $current_page == 'account.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user me-2"></i> Profile
                </a>
                <a href="orders.php" class="list-group-item list-group-item-action <?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-bag me-2"></i> Orders
                </a>
                <a href="wishlist.php" class="list-group-item list-group-item-action <?php echo $current_page == 'wishlist.php' ? 'active' : ''; ?>">
                    <i class="fas fa-heart me-2"></i> Wishlist
                </a>
                <a href="addresses.php" class="list-group-item list-group-item-action <?php echo $current_page == 'addresses.php' ? 'active' : ''; ?>">
                    <i class="fas fa-map-marker-alt me-2"></i> Addresses
                </a>
                <a href="cart.php" class="list-group-item list-group-item-action <?php echo $current_page == 'cart.php' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart me-2"></i> Cart
                </a>
                <a href="logout.php" class="list-group-item list-group-item-action text-danger">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div> 