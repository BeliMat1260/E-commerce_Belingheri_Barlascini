<?php
require_once "config/database.php";
require_once "config/session.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('warning', 'Please log in to view your orders.');
    header("Location: login.php");
    exit();
}

// Recupera gli ordini dell'utente
$orders_sql = "SELECT o.*, 
               COUNT(oi.id) as total_items,
               GROUP_CONCAT(p.name SEPARATOR ', ') as product_names
               FROM orders o
               LEFT JOIN order_items oi ON o.id = oi.order_id
               LEFT JOIN products p ON oi.product_id = p.id
               WHERE o.user_id = ?
               GROUP BY o.id
               ORDER BY o.created_at DESC";

$stmt = mysqli_prepare($conn, $orders_sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$orders_result = mysqli_stmt_get_result($stmt);

include 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">My Orders</h1>
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Account Menu</h5>
                    <div class="list-group list-group-flush">
                        <a href="account.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>
                        <a href="orders.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-shopping-bag me-2"></i> Orders
                        </a>
                        <a href="wishlist.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-heart me-2"></i> Wishlist
                        </a>
                        <a href="addresses.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-map-marker-alt me-2"></i> Addresses
                        </a>
                        <a href="cart.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-cart me-2"></i> Cart
                        </a>
                        <a href="logout.php" class="list-group-item list-group-item-action text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <?php if (mysqli_num_rows($orders_result) > 0): ?>
                <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">Order #<?php echo $order['id']; ?></h5>
                                    <small class="text-muted">
                                        Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                                    </small>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <span class="badge bg-<?php 
                                        echo match($order['status']) {
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'shipped' => 'primary',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'secondary'
                                        };
                                    ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6>Products</h6>
                                    <p class="text-muted"><?php echo htmlspecialchars($order['product_names']); ?></p>
                                    <p class="mb-0">
                                        <strong>Total Items:</strong> <?php echo $order['total_items']; ?><br>
                                        <strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?><br>
                                        <strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?>
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-primary">
                                        View Details
                                    </a>
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <button class="btn btn-outline-danger mt-2 cancel-order" data-order-id="<?php echo $order['id']; ?>">
                                            Cancel Order
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    You haven't placed any orders yet.
                    <a href="products.php" class="alert-link">Start shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this order? This action cannot be undone.</p>
                <form id="cancelOrderForm" action="cancel_order.php" method="POST">
                    <input type="hidden" name="order_id" id="cancelOrderId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="cancelOrderForm" class="btn btn-danger">Cancel Order</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cancel order modal
    const cancelButtons = document.querySelectorAll('.cancel-order');
    const cancelModal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
    const cancelOrderId = document.getElementById('cancelOrderId');

    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            cancelOrderId.value = orderId;
            cancelModal.show();
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?> 