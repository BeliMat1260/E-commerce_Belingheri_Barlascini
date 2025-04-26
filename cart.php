<?php
require_once "config/database.php";
require_once "config/session.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to view your cart.');
    header("Location: login.php");
    exit();
}

// Handle remove item from cart
if (isset($_POST['remove_item'])) {
    $product_id = (int)$_POST['product_id'];
    
    // Prima elimina l'elemento
    $delete_sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "ii", $_SESSION['user_id'], $product_id);
    mysqli_stmt_execute($stmt);
    
    // Controlla se il carrello è vuoto
    $check_sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    $count = mysqli_fetch_assoc($result)['count'];
    
    if ($count == 0) {
        setFlashMessage('info', 'Your cart is now empty.');
    } else {
        setFlashMessage('success', 'Item removed from cart.');
    }
    
    // Redirect per evitare il ricaricamento del form
    header("Location: cart.php");
    exit();
}

// Handle update quantity
if (isset($_POST['update_quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0) {
        // Prima verifica se il prodotto esiste già nel carrello
        $check_sql = "SELECT id FROM cart WHERE user_id = ? AND product_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "ii", $_SESSION['user_id'], $product_id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($result) > 0) {
            // Aggiorna la quantità se il prodotto esiste
            $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($stmt, "iii", $quantity, $_SESSION['user_id'], $product_id);
        } else {
            // Inserisci il nuovo prodotto nel carrello
            $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($stmt, "iii", $_SESSION['user_id'], $product_id, $quantity);
        }
        mysqli_stmt_execute($stmt);
        setFlashMessage('success', 'Cart updated successfully.');
    } else {
        // Se la quantità è 0, rimuovi l'elemento
        $delete_sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($stmt, "ii", $_SESSION['user_id'], $product_id);
        mysqli_stmt_execute($stmt);
        
        // Controlla se il carrello è vuoto
        $check_sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        $count = mysqli_fetch_assoc($result)['count'];
        
        if ($count == 0) {
            setFlashMessage('info', 'Your cart is now empty.');
        } else {
            setFlashMessage('info', 'Item removed from cart.');
        }
    }
    
    // Redirect per evitare il ricaricamento del form
    header("Location: cart.php");
    exit();
}

// Get cart items with product details
$cart_sql = "SELECT c.*, p.name, p.price, p.stock_quantity, p.discount_price,
            (SELECT image_url FROM product_images WHERE product_id = p.id AND image_order = 1 LIMIT 1) as product_image
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?";
$cart_stmt = mysqli_prepare($conn, $cart_sql);
mysqli_stmt_bind_param($cart_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($cart_stmt);
$cart_result = mysqli_stmt_get_result($cart_stmt);

$cart_items = [];
$total = 0;

while ($row = mysqli_fetch_assoc($cart_result)) {
    $item = [
        'id' => $row['id'],
        'product_id' => $row['product_id'],
        'name' => $row['name'],
        'quantity' => $row['quantity'],
        'price' => $row['discount_price'] ?: $row['price'],
        'stock_quantity' => $row['stock_quantity'],
        'product_image' => $row['product_image']
    ];
    
    $cart_items[] = $item;
    $total += $item['price'] * $item['quantity'];
}

// Include header
include 'includes/header.php';
?>

<!-- Cart Section -->
<div class="container py-5">
    <h1 class="mb-4">My Cart</h1>
    
    <div class="row">
        <!-- Account Menu -->
        <?php include 'includes/account_menu.php'; ?>
        
        <!-- Cart Content -->
        <div class="col-md-9">
            <?php if (empty($cart_items)): ?>
                <div class="alert alert-info">
                    Your cart is empty. <a href="products.php" class="alert-link">Continue shopping</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($item['product_image']): ?>
                                            <img src="<?php echo htmlspecialchars($item['product_image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                 class="img-thumbnail" 
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 80px; height: 80px;">
                                                <span class="text-muted small">No image</span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="ms-3">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <form method="POST" class="d-flex">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="0" class="form-control" style="width: 80px;">
                                        <button type="submit" name="update_quantity" class="btn btn-sm btn-primary ms-2">Update</button>
                                    </form>
                                </td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <button type="submit" name="remove_item" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td colspan="2"><strong>$<?php echo number_format($total, 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="products.php" class="btn btn-outline-primary">Continue Shopping</a>
                    <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 