<?php
require_once "config/database.php";
require_once "config/session.php";

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Validate input
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Here you would typically send the email or save to database
        // For now, we'll just show a success message
        $success_message = 'Thank you for your message. We will get back to you soon!';
        
        // Clear form data
        $name = $email = $subject = $message = '';
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Contact Section -->
<div class="container py-5">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
            <p class="lead text-secondary">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>
    </div>

    <div class="row">
        <!-- Contact Information -->
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h2 class="h4 fw-bold mb-4">Get in Touch</h2>
                    
                    <!-- Address -->
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-primary fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="h6 fw-bold mb-2">Address</h3>
                            <p class="text-secondary mb-0">
                                123 E-commerce Street<br>
                                Digital City, DC 12345<br>
                                United States
                            </p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-phone text-primary fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="h6 fw-bold mb-2">Phone</h3>
                            <p class="text-secondary mb-0">
                                <a href="tel:+1234567890" class="text-decoration-none">+1 (234) 567-890</a>
                            </p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-envelope text-primary fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="h6 fw-bold mb-2">Email</h3>
                            <p class="text-secondary mb-0">
                                <a href="mailto:info@example.com" class="text-decoration-none">info@example.com</a>
                            </p>
                        </div>
                    </div>

                    <!-- Business Hours -->
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-primary fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="h6 fw-bold mb-2">Business Hours</h3>
                            <p class="text-secondary mb-0">
                                Monday - Friday: 9:00 AM - 6:00 PM<br>
                                Saturday: 10:00 AM - 4:00 PM<br>
                                Sunday: Closed
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <?php if ($success_message): ?>
                        <div class="alert alert-success fade-in">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger fade-in">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <h2 class="h4 fw-bold mb-4">Send us a Message</h2>
                    <form method="POST" action="contact.php" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-medium">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-medium">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-medium">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="message" class="form-label fw-medium">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.30591910525!2d-74.25986432970718!3d40.697149422113014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2s!4v1645564757466!5m2!1sen!2s" 
                        width="100%" 
                        height="450" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

<?php include 'includes/footer.php'; ?> 