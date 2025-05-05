<?php
require_once __DIR__ . "/../includes/config/database.php";
require_once __DIR__ . "/../includes/config/session.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to view your wishlist.');
    header("Location: /E-commerce_Belingheri_Barlascini/login.php");
    exit();
}

// Get wishlist items with product details
$wishlist_sql = "SELECT w.*, p.name, p.price, p.stock_quantity,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND image_order = 1 LIMIT 1) as product_image
                FROM wishlist w
                JOIN products p ON w.product_id = p.id
                WHERE w.user_id = ?";
$wishlist_stmt = mysqli_prepare($conn, $wishlist_sql);
mysqli_stmt_bind_param($wishlist_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($wishlist_stmt);
$wishlist_result = mysqli_stmt_get_result($wishlist_stmt);

// Include header
include __DIR__ . '/../includes/components/header.php';
?>

<!-- Wishlist Section -->
<div class="container py-5">
    <h1 class="mb-4">My Wishlist</h1>
    
    <div class="row">
        <!-- Account Menu -->
        <?php include __DIR__ . '/../includes/components/account_menu.php'; ?>
        
        <!-- Wishlist Content -->
        <div class="col-md-9">
            <?php if (mysqli_num_rows($wishlist_result) > 0): ?>
                <div class="row">
                    <?php while ($item = mysqli_fetch_assoc($wishlist_result)): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <?php if ($item['product_image']): ?>
                                        <img src="/E-commerce_Belingheri_Barlascini/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                            <span class="text-muted">No image available</span>
                                        </div>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-danger position-absolute top-0 start-0 m-2 remove-from-wishlist"
                                            data-product-id="<?php echo $item['product_id']; ?>"
                                            title="Remove from Wishlist">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                    
                                    <!-- Price -->
                                    <div class="mb-3">
                                        <span class="fw-bold">$<?php echo number_format($item['price'], 2); ?></span>
                                    </div>
                                    
                                    <!-- Stock Status -->
                                    <div class="mb-3">
                                        <span class="badge bg-<?php echo $item['stock_quantity'] > 0 ? 'success' : 'danger'; ?>">
                                            <?php echo $item['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <div class="d-grid gap-2">
                                        <a href="/E-commerce_Belingheri_Barlascini/pages/product.php?id=<?php echo $item['product_id']; ?>" class="btn btn-outline-primary">View Details</a>
                                        <?php if ($item['stock_quantity'] > 0): ?>
                                            <form action="add_to_cart.php" method="POST" class="d-grid">
                                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
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
                <div class="alert alert-info">
                    Your wishlist is empty. <a href="/E-commerce_Belingheri_Barlascini/products.php" class="alert-link">Browse products</a> to add items to your wishlist.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove from wishlist functionality
    const removeButtons = document.querySelectorAll('.remove-from-wishlist');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            fetch('remove_from_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Rimuovi l'elemento dalla pagina
                    this.closest('.col-md-4').remove();
                    
                    // Se non ci sono più elementi, mostra il messaggio
                    if (document.querySelectorAll('.col-md-4').length === 0) {
                        const container = document.querySelector('.col-md-9');
                        container.innerHTML = `
                            <div class="alert alert-info">
                                Your wishlist is empty. <a href="products.php" class="alert-link">Browse products</a> to add items to your wishlist.
                            </div>
                        `;
                    }
                } else {
                    alert(data.message || 'Failed to remove from wishlist.');
                }
            });
        });
    });
});
</script>

<?php include __DIR__ . '/../includes/components/footer.php'; ?> 