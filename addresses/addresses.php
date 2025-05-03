<?php
require_once __DIR__ . "/../includes/config/database.php";
require_once __DIR__ . "/../includes/config/session.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to view your addresses.');
    header("Location: /E-commerce_Belingheri_Barlascini/login.php");
    exit();
}

// Get user addresses
$addresses_sql = "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC";
$addresses_stmt = mysqli_prepare($conn, $addresses_sql);
mysqli_stmt_bind_param($addresses_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($addresses_stmt);
$addresses_result = mysqli_stmt_get_result($addresses_stmt);

// Include header
include __DIR__ . '/../includes/components/header.php';
?>

<!-- Addresses Section -->
<div class="container py-5">
    <h1 class="mb-4">My Addresses</h1>
    
    <div class="row">
        <!-- Account Menu -->
        <?php include __DIR__ . '/../includes/components/account_menu.php'; ?>
        
        <!-- Addresses Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Saved Addresses</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="fas fa-plus me-2"></i> Add New Address
                </button>
            </div>
            
            <?php if (mysqli_num_rows($addresses_result) > 0): ?>
                <div class="row">
                    <?php while ($address = mysqli_fetch_assoc($addresses_result)): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($address['address_name']); ?></h5>
                                        <?php if ($address['is_default']): ?>
                                            <span class="badge bg-primary">Default</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <p class="card-text">
                                        <?php echo htmlspecialchars($address['address_line1']); ?><br>
                                        <?php if ($address['address_line2']): ?>
                                            <?php echo htmlspecialchars($address['address_line2']); ?><br>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($address['city']); ?>, <?php echo htmlspecialchars($address['state']); ?> <?php echo htmlspecialchars($address['postal_code']); ?><br>
                                        <?php echo htmlspecialchars($address['country']); ?>
                                    </p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-primary btn-sm edit-address" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editAddressModal"
                                                data-address-id="<?php echo $address['id']; ?>"
                                                data-address-name="<?php echo htmlspecialchars($address['address_name']); ?>"
                                                data-address-line1="<?php echo htmlspecialchars($address['address_line1']); ?>"
                                                data-address-line2="<?php echo htmlspecialchars($address['address_line2']); ?>"
                                                data-city="<?php echo htmlspecialchars($address['city']); ?>"
                                                data-state="<?php echo htmlspecialchars($address['state']); ?>"
                                                data-postal-code="<?php echo htmlspecialchars($address['postal_code']); ?>"
                                                data-country="<?php echo htmlspecialchars($address['country']); ?>"
                                                data-is-default="<?php echo $address['is_default']; ?>">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </button>
                                        <?php if (!$address['is_default']): ?>
                                            <form action="set_default_address.php" method="POST" class="d-inline">
                                                <input type="hidden" name="address_id" value="<?php echo $address['id']; ?>">
                                                <button type="submit" class="btn btn-outline-success btn-sm">
                                                    <i class="fas fa-star me-2"></i> Set as Default
                                                </button>
                                            </form>
                                            <form action="delete_address.php" method="POST" class="d-inline">
                                                <input type="hidden" name="address_id" value="<?php echo $address['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this address?')">
                                                    <i class="fas fa-trash me-2"></i> Delete
                                                </button>
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
                    You haven't added any addresses yet. Click the "Add New Address" button to add your first address.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="add_address.php" method="POST" id="addAddressForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="address_name" class="form-label">Address Name</label>
                        <input type="text" class="form-control" id="address_name" name="address_name" required>
                        <div class="form-text">e.g., Home, Office, etc.</div>
                    </div>
                    <div class="mb-3">
                        <label for="address_line1" class="form-label">Address Line 1</label>
                        <input type="text" class="form-control" id="address_line1" name="address_line1" required>
                    </div>
                    <div class="mb-3">
                        <label for="address_line2" class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" id="address_line2" name="address_line2">
                        <div class="form-text">Optional</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">State/Province</label>
                            <input type="text" class="form-control" id="state" name="state" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="postal_code" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control" id="country" name="country" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_default" name="is_default">
                            <label class="form-check-label" for="is_default">
                                Set as default address
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Address Modal -->
<div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="edit_address.php" method="POST" id="editAddressForm">
                <input type="hidden" name="address_id" id="edit_address_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_address_name" class="form-label">Address Name</label>
                        <input type="text" class="form-control" id="edit_address_name" name="address_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_address_line1" class="form-label">Address Line 1</label>
                        <input type="text" class="form-control" id="edit_address_line1" name="address_line1" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_address_line2" class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" id="edit_address_line2" name="address_line2">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_city" class="form-label">City</label>
                            <input type="text" class="form-control" id="edit_city" name="city" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_state" class="form-label">State/Province</label>
                            <input type="text" class="form-control" id="edit_state" name="state" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_postal_code" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="edit_postal_code" name="postal_code" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_country" class="form-label">Country</label>
                            <input type="text" class="form-control" id="edit_country" name="country" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_default" name="is_default">
                            <label class="form-check-label" for="edit_is_default">
                                Set as default address
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit address functionality
    const editButtons = document.querySelectorAll('.edit-address');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const addressId = this.dataset.addressId;
            const addressName = this.dataset.addressName;
            const addressLine1 = this.dataset.addressLine1;
            const addressLine2 = this.dataset.addressLine2;
            const city = this.dataset.city;
            const state = this.dataset.state;
            const postalCode = this.dataset.postalCode;
            const country = this.dataset.country;
            const isDefault = this.dataset.isDefault === '1';
            
            document.getElementById('edit_address_id').value = addressId;
            document.getElementById('edit_address_name').value = addressName;
            document.getElementById('edit_address_line1').value = addressLine1;
            document.getElementById('edit_address_line2').value = addressLine2;
            document.getElementById('edit_city').value = city;
            document.getElementById('edit_state').value = state;
            document.getElementById('edit_postal_code').value = postalCode;
            document.getElementById('edit_country').value = country;
            document.getElementById('edit_is_default').checked = isDefault;
        });
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});
</script>

<?php include __DIR__ . '/../includes/components/footer.php'; ?> 