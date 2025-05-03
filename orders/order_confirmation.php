<?php
require_once "config/database.php";
require_once "config/session.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to view your order.');
    header("Location: login.php");
    exit();
}

// Get order ID from URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get order details
$order_sql = "SELECT o.*, u.email 
              FROM orders o 
              JOIN users u ON o.user_id = u.id 
              WHERE o.id = ? AND o.user_id = ?";
$order_stmt = mysqli_prepare($conn, $order_sql);
mysqli_stmt_bind_param($order_stmt, "ii", $order_id, $_SESSION['user_id']);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);
$order = mysqli_fetch_assoc($order_result);

// Redirect if order not found or doesn't belong to user
if (!$order) {
    setFlashMessage('error', 'Order not found.');
    header("Location: orders.php");
    exit();
}

// Get order items
$items_sql = "SELECT oi.*, p.name, 
              (SELECT image_url FROM product_images WHERE product_id = p.id AND image_order = 1 LIMIT 1) as product_image
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$items_stmt = mysqli_prepare($conn, $items_sql);
mysqli_stmt_bind_param($items_stmt, "i", $order_id);
mysqli_stmt_execute($items_stmt);
$items_result = mysqli_stmt_get_result($items_stmt);

// Include header
include 'includes/header.php';
?>

<!-- Order Confirmation Section -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    <h1 class="mt-3">Thank You for Your Order!</h1>
                    <h2 class="mb-4">Order Confirmation</h2>
                    <p>Thank you for your order! Your order has been successfully placed.</p>
                    <p>Order Number: #<?php echo $order['id']; ?></p>
                    <p>Order Date: <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                    <p>Total Amount: $<?php echo number_format($order['total_amount'], 2); ?></p>
                    <div id="delivery-status" class="mt-3">
                        <p>Order Status: <span id="status-text"><?php echo ucfirst($order['status']); ?></span></p>
                        <div class="progress" style="height: 20px;">
                            <div id="delivery-progress" class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Shipping Address:</h6>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Billing Address:</h6>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['billing_address'])); ?></p>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <h6>Items Ordered:</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($item['product_image']): ?>
                                                    <img src="<?php echo htmlspecialchars($item['product_image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                         class="img-thumbnail me-3" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php endif; ?>
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </div>
                                        </td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td class="text-end">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end"><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Payment Info -->
                    <div class="mt-4">
                        <h6>Payment Method:</h6>
                        <p><?php echo htmlspecialchars(ucfirst($order['payment_method'])); ?></p>
                    </div>

                    <?php if ($order['notes']): ?>
                        <div class="mt-4">
                            <h6>Order Notes:</h6>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <a href="products.php" class="btn btn-outline-primary">
                    <i class="fas fa-shopping-cart me-2"></i>Continue Shopping
                </a>
                <a href="orders.php" class="btn btn-primary">
                    <i class="fas fa-list me-2"></i>View All Orders
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
let deliveryTimer;
let progressBar;
let statusText;
let timeLeft = 60; // 1 minute in seconds

function updateDeliveryStatus() {
    if (timeLeft > 0) {
        const progress = ((60 - timeLeft) / 60) * 100;
        progressBar.style.width = progress + '%';
        statusText.textContent = 'Processing';
        timeLeft--;
    } else {
        clearInterval(deliveryTimer);
        fetch(`update_order_status.php?order_id=<?php echo $order['id']; ?>`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    progressBar.style.width = '100%';
                    statusText.textContent = 'Delivered';
                    progressBar.classList.remove('progress-bar-animated');
                    progressBar.classList.add('bg-success');
                }
            })
            .catch(error => console.error('Error:', error));
    }
}

document.addEventListener('DOMContentLoaded', function() {
    progressBar = document.getElementById('delivery-progress');
    statusText = document.getElementById('status-text');
    
    if (statusText.textContent.toLowerCase() !== 'delivered') {
        deliveryTimer = setInterval(updateDeliveryStatus, 1000);
    } else {
        progressBar.style.width = '100%';
        progressBar.classList.add('bg-success');
    }
});
</script> 