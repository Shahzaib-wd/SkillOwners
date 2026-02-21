<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitizeInput($_POST['full_name'] ?? '');
    $companyName = sanitizeInput($_POST['company_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $budget = sanitizeInput($_POST['budget_range'] ?? '');
    $service = sanitizeInput($_POST['service_type'] ?? '');
    $description = sanitizeInput($_POST['project_description'] ?? '');
    $timeline = sanitizeInput($_POST['timeline'] ?? '');

    if (empty($fullName) || empty($email) || empty($description)) {
        showError('Please fill in all required fields.');
    } else {
        $db = getDBConnection();
        $stmt = $db->prepare("INSERT INTO quote_requests (full_name, company_name, email, phone, budget_range, service_type, project_description, timeline) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$fullName, $companyName, $email, $phone, $budget, $service, $description, $timeline])) {
            showSuccess('Thank you! Your quote request has been received. Our analysts will review it and contact you shortly.');
            redirect('/request_quote');
        } else {
            showError('An error occurred. Please try again later.');
        }
    }
}

include 'views/partials/header.php';
?>

<section class="contact-hero">
    <div class="container text-center">
        <span class="hero-tag reveal-up">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Project Discovery</span>
        </span>
        <h1 class="hero-title reveal-up" style="animation-delay: 0.2s;">Get a <span class="text-gradient">Custom Quote</span></h1>
        <p class="hero-description mx-auto text-center reveal-up" style="animation-delay: 0.3s;">
            Tell us about your project and we'll provide a detailed proposal within 24-48 hours.
        </p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- Process Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-4 reveal-up" style="animation-delay: 0.4s;">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="h5 font-weight-700">Discovery</h3>
                    <p class="text-muted small mb-0">We dive deep into your requirements to understand your business goals and technical needs.</p>
                </div>
            </div>
            
            <div class="col-md-4 reveal-up" style="animation-delay: 0.5s;">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-magic"></i>
                    </div>
                    <h3 class="h5 font-weight-700">Solution Design</h3>
                    <p class="text-muted small mb-0">Our architects design a scalable, high-performance solution tailored specifically for you.</p>
                </div>
            </div>
            
            <div class="col-md-4 reveal-up" style="animation-delay: 0.6s;">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3 class="h5 font-weight-700">Execution</h3>
                    <p class="text-muted small mb-0">Rapid development and precision deployment using industry-leading quality standards.</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-glass reveal-up" style="animation-delay: 0.7s;">
                    <h2 class="h3 font-weight-800 mb-4">Project <span class="text-gradient">Details</span></h2>
                    <form method="POST" id="quoteForm">

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="font-weight-700 small text-uppercase tracking-wider mb-2 d-block">Full Name *</label>
                                <input type="text" name="full_name" class="form-control input-glass" placeholder="Jane Smith" required>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-700 small text-uppercase tracking-wider mb-2 d-block">Email Address *</label>
                                <input type="email" name="email" class="form-control input-glass" placeholder="jane@acme.com" required>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="font-weight-700 small text-uppercase tracking-wider mb-2 d-block">Service Interested In</label>
                                <select name="service_type" class="form-select input-glass">
                                    <option value="Web Development">Web Development</option>
                                    <option value="SEO">SEO & Google Rankings</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="font-weight-700 small text-uppercase tracking-wider mb-2 d-block">Tell us about your project *</label>
                                <textarea name="project_description" class="form-control input-glass" rows="5" placeholder="What are your goals? (e.g., I need a new website for my law firm...)" required></textarea>
                                <div class="text-muted small mt-2">More details help us provide a more accurate quote.</div>
                            </div>

                            <!-- Optional Fields Collapsible or Secondary Group -->
                            <div class="col-12 pt-2">
                                <p class="small text-muted mb-3 font-weight-700 text-uppercase tracking-widest">Additional Details (Optional)</p>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <select name="budget_range" class="form-select input-glass">
                                            <option value="" selected disabled>Select Budget Range</option>
                                            <option value="Less than $1k">Under $1,000</option>
                                            <option value="$1k - $5k">$1,000 - $5,000</option>
                                            <option value="$5k - $15k">$5,000 - $15,000</option>
                                            <option value="$15k+">$15,000+</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="company_name" class="form-control input-glass" placeholder="Company Name">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="tel" name="phone" class="form-control input-glass" placeholder="Phone Number">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12 pt-3">
                                <button type="submit" class="btn btn-primary btn-lg px-5 w-100">Send Quote Request</button>
                                <p class="text-center small text-muted mt-3 mb-0">No commitment required. We'll respond within 24 hours.</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('quoteForm').addEventListener('submit', function(e) {
    const desc = document.querySelector('textarea[name="project_description"]').value;
    if (desc.trim().length < 20) {
        e.preventDefault();
        alert('Please provide a more detailed project description (at least 20 characters).');
    }
});
</script>

<?php include 'views/partials/footer.php'; ?>
