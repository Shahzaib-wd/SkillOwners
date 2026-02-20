<?php
require_once 'config.php';
$success = getSuccess();
$error = getError();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? 'General Inquiry');
    $message = sanitizeInput($_POST['message'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        showError('Name, Email, and Message are required');
    } else {
        require_once 'models/ContactMessage.php';
        $contactModel = new ContactMessage();
        $contactModel->create($name, $email, $subject, $message);

        require_once 'helpers/MailHelper.php';
        
        $to = ADMIN_EMAIL;
        $fullSubject = "Contact Form: $subject";
        $body = "
        <h3>New Contact Form Submission</h3>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Service:</strong> $subject</p>
        <p><strong>Message:</strong><br>" . nl2br($message) . "</p>";
        
        MailHelper::send($to, $fullSubject, $body);
        
        // Also store in new table
        $db = getDBConnection();
        $stmt = $db->prepare("INSERT INTO contact_submissions (name, email, phone, service_interested, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $subject, $message]);
        
        showSuccess('Message sent successfully! Our team will get back to you within 24 hours.');
    }
    redirect("/contact");
    exit;
}

include 'views/partials/header.php';
?>


<section class="contact-hero">
    <div class="container text-center">
        <span class="hero-tag reveal-up">
            <i class="fas fa-headset"></i>
            <span>Support Center</span>
        </span>
        <h1 class="hero-title reveal-up" style="animation-delay: 0.2s;">How can we <span class="text-gradient">help you?</span></h1>
        <p class="hero-description mx-auto text-center reveal-up" style="animation-delay: 0.3s;">
            Ready to scale your digital presence? Get in touch with our experts today.
        </p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-md-4 reveal-up" style="animation-delay: 0.4s;">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="h5 font-weight-700">Email Support</h3>
                    <p class="text-muted small mb-4">Drop us a line and we'll get back to you within 24 hours.</p>
                    <a href="mailto:info@skillowners.com" class="font-weight-700 text-primary">info@skillowners.com</a>
                </div>
            </div>
            
            <div class="col-md-4 reveal-up" style="animation-delay: 0.5s;">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="h5 font-weight-700">Live Chat</h3>
                    <p class="text-muted small mb-4">Available for premium members 24/7. Average response time: 5 mins.</p>
                    <!-- <a href="#" class="font-weight-700 text-primary">Start Chatting</a> -->
                </div>
            </div>
            
            <div class="col-md-4 reveal-up" style="animation-delay: 0.6s;">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3 class="h5 font-weight-700">Global Offices</h3>
                    <p class="text-muted small mb-4">Our operations hub is located in the vibrant tech landscape of Karachi.</p>
                    <span class="font-weight-700">Karachi, Pakistan</span>
                </div>
            </div>
        </div>

        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-5 mb-lg-0 reveal-right">
                <div class="form-glass">
                    <h2 class="h3 font-weight-800 mb-4">Send us a <span class="text-gradient">Message</span></h2>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-700 small text-uppercase tracking-wider mb-2 d-block">Full Name</label>
                                <input type="text" name="name" class="form-control input-glass" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-700 small text-uppercase tracking-wider mb-2 d-block">Email Address</label>
                                <input type="email" name="email" class="form-control input-glass" placeholder="john@example.com" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-700 small text-uppercase tracking-wider mb-2 d-block">Phone Number</label>
                                <input type="tel" name="phone" class="form-control input-glass" placeholder="+1 (555) 000-0000">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="font-weight-700 small text-uppercase tracking-wider mb-2 d-block">Service Interested In</label>
                                <select name="subject" class="form-select input-glass">
                                    <option value="Web Development">Web Development</option>
                                    <option value="SEO Services">SEO Services</option>
                                    <option value="Digital Marketing">Digital Marketing</option>
                                    <option value="Paid Ads">Paid Ads Management</option>
                                    <option value="Content Writing">Content Writing</option>
                                    <option value="Branding">Branding & Identity</option>
                                    <option value="Maintenance">Website Maintenance</option>
                                </select>
                            </div>
                            <div class="col-12 mb-4">
                                <label class="font-weight-700 small text-uppercase tracking-wider mb-2 d-block">Message</label>
                                <textarea name="message" class="form-control input-glass" rows="4" placeholder="How can we help?" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Send Inquiry</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-5 offset-lg-1 reveal-left">
                <h2 class="h3 font-weight-800 mb-4">Frequently Asked <span class="text-gradient">Questions</span></h2>
                <div class="faq-accordion">
                    <div class="faq-item active">
                        <div class="faq-question">
                            How do I get paid as a freelancer?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            Payments are sent directly by the buyer to the seller without any platform fees, escrow deductions, or intermediary charges from SkillOwners.
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            What is your average project timeline?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            Typical business websites take 2-4 weeks, while complex e-commerce platforms or custom systems may take 6-12 weeks depending on requirements.
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            Do you offer monthly maintenance?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            Yes, we provide 24/7 security monitoring, daily backups, and performance optimization packages to keep your site running perfectly.
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            Can you help with rebranding?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            Absolutely. Our creative team specializes in modern brand identity, logo design, and unified design languages for growing businesses.
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border-bottom: none;">
                        <div class="faq-question">
                            Do you provide a dedicated project manager?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            Every client is assigned a dedicated success manager who ensures clear communication and on-time delivery of all milestones.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            const item = question.parentElement;
            const isActive = item.classList.contains('active');
            
            // Close all items
            document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
});
</script>

<?php include 'views/partials/footer.php'; ?>
