<?php
// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    header("Location: /E-commerce_Belingheri_Barlascini/auth/login.php");
    exit();
}

// Definisci il percorso base del sito
define('BASE_PATH', '/E-commerce_Belingheri_Barlascini');

// Ottieni il percorso corrente
$current_path = $_SERVER['PHP_SELF'];

// Debug: mostra il percorso corrente
error_log("Current path: " . $current_path);

// Funzione per verificare se il percorso corrente corrisponde a una sezione
function isActive($path, $section) {
    $result = strpos($path, BASE_PATH . $section) !== false;
    error_log("Checking section '$section': " . ($result ? 'true' : 'false'));
    return $result;
}

// Funzione per generare l'URL completo
function getUrl($path) {
    return BASE_PATH . $path;
}
?>

<div class="col-md-3">
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Account Menu</h5>
            <div class="list-group">
                <a href="<?php echo getUrl('/account/account.php'); ?>" 
                   class="list-group-item list-group-item-action <?php echo isActive($current_path, '/account/') ? 'active' : ''; ?>">
                    <i class="fas fa-user me-2"></i> Profile
                </a>
                <a href="<?php echo getUrl('/orders/orders.php'); ?>" 
                   class="list-group-item list-group-item-action <?php echo isActive($current_path, '/orders/') ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-bag me-2"></i> Orders
                </a>
                <a href="<?php echo getUrl('/wishlist/wishlist.php'); ?>" 
                   class="list-group-item list-group-item-action <?php echo isActive($current_path, '/wishlist/') ? 'active' : ''; ?>">
                    <i class="fas fa-heart me-2"></i> Wishlist
                </a>
                <a href="<?php echo getUrl('/addresses/addresses.php'); ?>" 
                   class="list-group-item list-group-item-action <?php echo isActive($current_path, '/addresses/') ? 'active' : ''; ?>">
                    <i class="fas fa-map-marker-alt me-2"></i> Addresses
                </a>
                <a href="<?php echo getUrl('/cart/cart.php'); ?>" 
                   class="list-group-item list-group-item-action <?php echo isActive($current_path, '/cart/') ? 'active' : ''; ?>"
                   onclick="console.log('Cart link clicked');">
                    <i class="fas fa-shopping-cart me-2"></i> Cart
                </a>
                <a href="<?php echo getUrl('/auth/logout.php'); ?>" 
                   class="list-group-item list-group-item-action text-danger">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Debug: aggiungi un event listener per il link del carrello
document.querySelector('a[href*="cart/cart.php"]').addEventListener('click', function(e) {
    console.log('Cart link clicked');
    console.log('Href:', this.href);
});
</script> 