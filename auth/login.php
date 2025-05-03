<?php
require_once "config/database.php";
require_once "config/session.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['last_activity'] = time();
                
                // Set flash message
                setFlashMessage('success', 'Welcome back, ' . htmlspecialchars($row['username']) . '!');
                
                // Redirect to previous page or home
                $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.php';
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirect");
                exit();
            } else {
                $error = 'Invalid password.';
            }
        } else {
            $error = 'Username not found.';
        }
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Login Form -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card fade-in">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="card-title fw-bold">Welcome Back</h2>
                        <p class="text-secondary">Please sign in to continue</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger fade-in">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="login.php" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="username" class="form-label fw-medium">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-user text-secondary"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="username" name="username" 
                                       placeholder="Enter your username" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-secondary"></i>
                                </span>
                                <input type="password" class="form-control border-start-0" id="password" name="password" 
                                       placeholder="Enter your password" required>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">Don't have an account? 
                            <a href="register.php" class="text-primary fw-medium">Create one now</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 