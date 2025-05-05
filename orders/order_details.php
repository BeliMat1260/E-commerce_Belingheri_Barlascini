<?php
require_once __DIR__ . "/../includes/config/database.php";
require_once __DIR__ . "/../includes/config/session.php";
require_once __DIR__ . "/../includes/config/functions.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('warning', 'Please log in to view order details.');
    header("Location: /E-commerce_Belingheri_Barlascini/auth/login.php");
    exit();
}

// Verifica se l'ID dell'ordine è fornito
if (!isset($_GET['id'])) {
    header("Location: /E-commerce_Belingheri_Barlascini/orders/orders.php");
    exit();
}

$order_id = (int)$_GET['id'];

// Recupera i dettagli dell'ordine
$order_sql = "SELECT o.*, 
              u.first_name, u.last_name, u.email
              FROM orders o
              JOIN users u ON o.user_id = u.id
              WHERE o.id = ? AND o.user_id = ?";

$stmt = mysqli_prepare($conn, $order_sql);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($order_result) === 0) {
    header("Location: orders.php");
    exit();
}

$order = mysqli_fetch_assoc($order_result);

// Recupera gli elementi dell'ordine
$items_sql = "SELECT oi.*, p.name,
              (SELECT image_url FROM product_images WHERE product_id = p.id AND image_order = 1 LIMIT 1) as product_image
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = ?";

$stmt = mysqli_prepare($conn, $items_sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);

include __DIR__ . '/../includes/components/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../includes/components/account_menu.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Order #<?php echo $order['id']; ?></h2>
                <a href="orders.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Orders
                </a>
            </div>

            <!-- Order Status -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Order Status</h5>
                            <span class="badge bg-<?php 
                                echo match($order['status']) {
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'shipped' => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                            ?> fs-6">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                            <p class="text-muted mt-2">
                                Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <?php if ($order['status'] === 'pending'): ?>
                                <button class="btn btn-outline-danger cancel-order" data-order-id="<?php echo $order['id']; ?>">
                                    Cancel Order
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                        <div class="row mb-3 pb-3 border-bottom">
                            <div class="col-md-2">
                                <?php if ($item['product_image']): ?>
                                    <img src="/E-commerce_Belingheri_Barlascini/<?php echo htmlspecialchars($item['product_image']); ?>"
                                         class="img-fluid rounded"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 100px;">
                                        <span class="text-muted">No image</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <p class="mb-0">Quantity: <?php echo $item['quantity']; ?></p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <p class="mb-0">$<?php echo number_format($item['price'], 2); ?> each</p>
                                <p class="fw-bold">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?> total</p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Shipping Address</h6>
                            <address class="mb-0">
                                <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                            </address>
                        </div>
                        <div class="col-md-6">
                            <h6>Billing Address</h6>
                            <address class="mb-0">
                                <?php echo nl2br(htmlspecialchars($order['billing_address'])); ?>
                            </address>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Payment Method</h6>
                            <p class="mb-0"><?php echo ucfirst($order['payment_method']); ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6>Total Amount</h6>
                            <p class="h4 mb-0">$<?php echo number_format($order['total_amount'], 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong>Name:</strong> <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?>
                            </p>
                            <p class="mb-0">
                                <strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
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

<?php include __DIR__ . '/../includes/components/footer.php'; ?> 