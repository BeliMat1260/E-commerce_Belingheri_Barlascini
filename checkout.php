<?php
require_once "config/database.php";
require_once "config/session.php";

// Redirect if not logged in
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit();
}

// Redirect if cart is empty
$check_cart_sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
$check_cart_stmt = mysqli_prepare($conn, $check_cart_sql);
mysqli_stmt_bind_param($check_cart_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($check_cart_stmt);
$cart_result = mysqli_stmt_get_result($check_cart_stmt);
$cart_count = mysqli_fetch_assoc($cart_result)['count'];

if ($cart_count == 0) {
    setFlashMessage('warning', 'Your cart is empty. Please add some products before checkout.');
    header("Location: cart.php");
    exit();
}

$error = '';
$success = '';

// Get user information
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Process order if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = trim($_POST['shipping_address']);
    $billing_address = trim($_POST['billing_address']);
    $payment_method = $_POST['payment_method'];
    $notes = trim($_POST['notes'] ?? '');
    
    if (empty($shipping_address) || empty($billing_address) || empty($payment_method)) {
        $error = 'All required fields must be filled out.';
    } else {
        // Calculate total and get cart items
        $total = 0;
        $cart_sql = "SELECT c.*, p.price, p.discount_price, p.stock_quantity 
                     FROM cart c 
                     JOIN products p ON c.product_id = p.id 
                     WHERE c.user_id = ?";
        $cart_stmt = mysqli_prepare($conn, $cart_sql);
        mysqli_stmt_bind_param($cart_stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($cart_stmt);
        $cart_result = mysqli_stmt_get_result($cart_stmt);
        
        $cart_items = [];
        while ($row = mysqli_fetch_assoc($cart_result)) {
            $price = $row['discount_price'] ?? $row['price'];
            $total += $price * $row['quantity'];
            $cart_items[] = $row;
        }
        
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Insert order
            $sql = "INSERT INTO orders (user_id, total_amount, shipping_address, billing_address, payment_method, notes) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "idssss", $_SESSION['user_id'], $total, $shipping_address, $billing_address, $payment_method, $notes);
            mysqli_stmt_execute($stmt);
            $order_id = mysqli_insert_id($conn);
            
            // Insert order items and update stock
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            
            foreach ($cart_items as $item) {
                $price = $item['discount_price'] ?? $item['price'];
                mysqli_stmt_bind_param($stmt, "iiid", $order_id, $item['product_id'], $item['quantity'], $price);
                mysqli_stmt_execute($stmt);
                
                // Update stock
                $new_stock = $item['stock_quantity'] - $item['quantity'];
                $update_stock_sql = "UPDATE products SET stock_quantity = ? WHERE id = ?";
                $update_stock_stmt = mysqli_prepare($conn, $update_stock_sql);
                mysqli_stmt_bind_param($update_stock_stmt, "ii", $new_stock, $item['product_id']);
                mysqli_stmt_execute($update_stock_stmt);
            }
            
            // Clear cart
            $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
            $clear_cart_stmt = mysqli_prepare($conn, $clear_cart_sql);
            mysqli_stmt_bind_param($clear_cart_stmt, "i", $_SESSION['user_id']);
            mysqli_stmt_execute($clear_cart_stmt);
            
            // Commit transaction
            mysqli_commit($conn);
            
            // Set success message
            setFlashMessage('success', 'Order placed successfully! Thank you for your purchase.');
            
            // Redirect to order confirmation
            header("Location: order_confirmation.php?id=" . $order_id);
            exit();
        } catch (Exception $e) {
            // Rollback transaction
            mysqli_rollback($conn);
            $error = 'Order processing failed. Please try again.';
        }
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Checkout Section -->
<div class="container py-5">
    <h1 class="mb-4">Checkout</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Order Summary -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Order Summary</h5>
                    <?php
                    $total = 0;
                    $cart_sql = "SELECT c.*, p.name, p.price, p.discount_price 
                                FROM cart c 
                                JOIN products p ON c.product_id = p.id 
                                WHERE c.user_id = ?";
                    $cart_stmt = mysqli_prepare($conn, $cart_sql);
                    mysqli_stmt_bind_param($cart_stmt, "i", $_SESSION['user_id']);
                    mysqli_stmt_execute($cart_stmt);
                    $cart_result = mysqli_stmt_get_result($cart_stmt);
                    
                    while ($item = mysqli_fetch_assoc($cart_result)):
                        $price = $item['discount_price'] ?? $item['price'];
                        $subtotal = $price * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                            <span>$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                    <?php endwhile; ?>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong>$<?php echo number_format($total, 2); ?></strong>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Checkout Form -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Shipping & Payment Information</h5>
                    
                    <?php
                    // Get user's saved addresses
                    $addresses_sql = "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC";
                    $addresses_stmt = mysqli_prepare($conn, $addresses_sql);
                    mysqli_stmt_bind_param($addresses_stmt, "i", $_SESSION['user_id']);
                    mysqli_stmt_execute($addresses_stmt);
                    $addresses_result = mysqli_stmt_get_result($addresses_stmt);
                    $addresses = mysqli_fetch_all($addresses_result, MYSQLI_ASSOC);
                    ?>
                    
                    <form method="POST" action="checkout.php">
                        <!-- Shipping Address -->
                        <div class="mb-3">
                            <label class="form-label">Shipping Address</label>
                            <?php if (!empty($addresses)): ?>
                                <div class="mb-3">
                                    <select class="form-select" id="shipping_address_select" onchange="updateShippingAddress(this.value)">
                                        <option value="">Select a saved address...</option>
                                        <?php foreach ($addresses as $address): ?>
                                            <option value="<?php echo htmlspecialchars(json_encode([
                                                'address_line1' => $address['address_line1'],
                                                'address_line2' => $address['address_line2'],
                                                'city' => $address['city'],
                                                'state' => $address['state'],
                                                'postal_code' => $address['postal_code'],
                                                'country' => $address['country']
                                            ])); ?>">
                                                <?php echo htmlspecialchars($address['address_name']); ?>
                                                <?php echo $address['is_default'] ? ' (Default)' : ''; ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="new">+ Use a different address</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <!-- Billing Address -->
                        <div class="mb-3">
                            <label class="form-label">Billing Address</label>
                            <?php if (!empty($addresses)): ?>
                                <div class="mb-3">
                                    <select class="form-select" id="billing_address_select" onchange="updateBillingAddress(this.value)">
                                        <option value="">Select a saved address...</option>
                                        <?php foreach ($addresses as $address): ?>
                                            <option value="<?php echo htmlspecialchars(json_encode([
                                                'address_line1' => $address['address_line1'],
                                                'address_line2' => $address['address_line2'],
                                                'city' => $address['city'],
                                                'state' => $address['state'],
                                                'postal_code' => $address['postal_code'],
                                                'country' => $address['country']
                                            ])); ?>">
                                                <?php echo htmlspecialchars($address['address_name']); ?>
                                                <?php echo $address['is_default'] ? ' (Default)' : ''; ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="new">+ Use a different address</option>
                                    </select>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="same_as_shipping" onchange="copyShippingAddress()">
                                        <label class="form-check-label" for="same_as_shipping">
                                            Same as shipping address
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <textarea class="form-control" id="billing_address" name="billing_address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" required>
                                <label class="form-check-label" for="credit_card">
                                    Credit Card
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                <label class="form-check-label" for="paypal">
                                    PayPal
                                </label>
                            </div>
                        </div>
                        
                        <!-- Order Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Order Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
function updateShippingAddress(value) {
    const textarea = document.getElementById('shipping_address');
    if (value === 'new') {
        textarea.value = '';
        textarea.removeAttribute('readonly');
    } else if (value) {
        const address = JSON.parse(value);
        let formattedAddress = address.address_line1;
        if (address.address_line2) {
            formattedAddress += '\n' + address.address_line2;
        }
        formattedAddress += '\n' + address.city + ', ' + address.state + ' ' + address.postal_code;
        formattedAddress += '\n' + address.country;
        textarea.value = formattedAddress;
        textarea.setAttribute('readonly', 'readonly');
    }
}

function updateBillingAddress(value) {
    const textarea = document.getElementById('billing_address');
    if (value === 'new') {
        textarea.value = '';
        textarea.removeAttribute('readonly');
        document.getElementById('same_as_shipping').checked = false;
    } else if (value) {
        const address = JSON.parse(value);
        let formattedAddress = address.address_line1;
        if (address.address_line2) {
            formattedAddress += '\n' + address.address_line2;
        }
        formattedAddress += '\n' + address.city + ', ' + address.state + ' ' + address.postal_code;
        formattedAddress += '\n' + address.country;
        textarea.value = formattedAddress;
        textarea.setAttribute('readonly', 'readonly');
        document.getElementById('same_as_shipping').checked = false;
    }
}

function copyShippingAddress() {
    const sameAsShipping = document.getElementById('same_as_shipping');
    const billingSelect = document.getElementById('billing_address_select');
    const billingTextarea = document.getElementById('billing_address');
    const shippingTextarea = document.getElementById('shipping_address');
    
    if (sameAsShipping.checked) {
        billingTextarea.value = shippingTextarea.value;
        billingTextarea.setAttribute('readonly', 'readonly');
        if (billingSelect) {
            billingSelect.value = '';
            billingSelect.setAttribute('disabled', 'disabled');
        }
    } else {
        if (billingSelect) {
            billingSelect.removeAttribute('disabled');
        }
        billingTextarea.removeAttribute('readonly');
    }
}

// Set default address if available
window.addEventListener('DOMContentLoaded', function() {
    const shippingSelect = document.getElementById('shipping_address_select');
    const billingSelect = document.getElementById('billing_address_select');
    
    if (shippingSelect && shippingSelect.options.length > 1) {
        shippingSelect.selectedIndex = 1; // Select first address (default)
        updateShippingAddress(shippingSelect.value);
    }
    
    if (billingSelect && billingSelect.options.length > 1) {
        billingSelect.selectedIndex = 1; // Select first address (default)
        updateBillingAddress(billingSelect.value);
    }
});
</script> 