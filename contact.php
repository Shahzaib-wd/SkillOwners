<?php
require_once 'config.php';
$success = getSuccess();
$error = getError();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? 'General Inquiry');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        showError('All fields are required');
    } else {
        // In a real app, you would send an email or save to DB here
        // For now, we simulate success
        showSuccess('Message sent successfully! Our team will get back to you within 24 hours.');
    }
    header("Location: contact.php");
    exit;
}

include 'views/partials/header.php';
?>

<style>
.contact-hero {
    padding: 8rem 0 4rem;
    background: var(--gradient-hero);
    position: relative;
    overflow: hidden;
}

.contact-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(var(--primary) 1px, transparent 1px);
    background-size: 40px 40px;
    opacity: 0.05;
}

.contact-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 2rem;
    padding: 2.5rem;
    height: 100%;
    transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
}

.contact-card:hover {
    transform: translateY(-8px);
    background: white;
    box-shadow: var(--shadow-elevated);
    border-color: hsla(150, 100%, 35%, 0.2);
}

.contact-icon {
    width: 64px;
    height: 64px;
    background: hsla(150, 100%, 35%, 0.1);
    color: var(--primary);
    border-radius: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-glass {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.7);
    border-radius: 2.5rem;
    padding: 3.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
}

.input-glass {
    background: rgba(255, 255, 255, 0.5);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 0.875rem 1.25rem;
    transition: all 0.3s ease;
}

.input-glass:focus {
    background: white;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px hsla(150, 100%, 35%, 0.1);
    outline: none;
}

.faq-item {
    border-bottom: 1px solid var(--border);
    padding: 1.5rem 0;
}

.faq-question {
    font-weight: 700;
    font-size: 1.125rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--foreground);
    transition: color 0.3s ease;
}

.faq-question:hover {
    color: var(--primary);
}

.faq-answer {
    margin-top: 1rem;
    color: var(--muted-foreground);
    display: none;
}

.faq-item.active .faq-answer {
    display: block;
}

.faq-item.active .faq-question i {
    transform: rotate(180deg);
}

.faq-question i {
    transition: transform 0.3s ease;
    font-size: 0.875rem;
}
</style>

<section class="contact-hero">
    <div class="container text-center">
        <span class="hero-tag reveal-up">
            <i class="fas fa-headset"></i>
            <span>Support Center</span>
        </span>
        <h1 class="hero-title reveal-up" style="animation-delay: 0.2s;">How can we <span class="text-gradient">help you?</span></h1>
        <p class="hero-description mx-auto text-center reveal-up" style="animation-delay: 0.3s;">
            Whether you're a freelancer, agency, or buyer, our team is here to support your journey on Skill Owners.
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
                    <a href="mailto:support@skillowners.com" class="font-weight-700 text-primary">support@skillowners.com</a>
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
                    <p class="text-muted small mb-4">Our headquarters are located in the heart of London's tech district.</p>
                    <span class="font-weight-700">London, United Kingdom</span>
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
                            <div class="col-12 mb-3">
                                <label class="font-weight-700 small text-uppercase tracking-wider mb-2 d-block">Inquiry Type</label>
                                <select name="subject" class="form-select input-glass">
                                    <option value="General Support">General Support</option>
                                    <option value="Billing">Billing & Payments</option>
                                    <option value="Account Issue">Account Issues</option>
                                    <option value="Partner">Partnership Inquiries</option>
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
                            What are the platform fees?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            SkillOwners is completely free — we do not take any commission. Our goal is to empower freelancers and clients to collaborate freely without platform fees.
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            Is my data secure?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            Absolutely. We use enterprise-grade encryption for all communications and don't store your sensitive payment information on our servers.
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border-bottom: none;">
                        <div class="faq-question">
                            How can I become a verified pro?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            Pro verification is awarded to users who maintain a 4.9+ rating and have completed over 50 projects with a 100% on-time delivery rate.
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
