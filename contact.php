<?php
require_once 'config.php';
$success = getSuccess();
$error = getError();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        showError('All fields are required');
    } else {
        // TODO: Send email or save to database
        showSuccess('Thank you for contacting us! We\'ll respond soon.');
    }
    $success = getSuccess();
    $error = getError();
}

include 'views/partials/header.php';
?>

<div style="padding: 6rem 0 4rem; min-height: 100vh;">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <h1 class="mb-4">Contact Us</h1>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-control" rows="5" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-body">
                        <h3 class="h5 mb-3">Other Ways to Reach Us</h3>
                        <p><i class="fas fa-envelope"></i> Email: <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/partials/footer.php'; ?>
