<?php
require_once 'config.php';
include 'views/partials/header.php';
?>

<section class="about-hero">
    <div class="container text-center">
        <h1 class="display-3 font-weight-900 mb-4 reveal-up">Our <span class="text-gradient">Core Services</span></h1>
        <p class="lead mb-5 opacity-75 mx-auto reveal-up" style="max-width: 700px; animation-delay: 0.1s;">
            We don't just build websites; we build growth engines. In today's digital world, if your business isn't on Google, it doesn't exist. We make sure you're seen, heard, and profitable.
        </p>
    </div>
</section>

<div class="container py-5">
    <?php
    $db = getDBConnection();
    $servicesStmt = $db->query("SELECT * FROM services ORDER BY order_index ASC");
    $services = $servicesStmt->fetchAll();

    foreach ($services as $index => $service):
        $isEven = $index % 2 === 0;
    ?>
    <section id="service-<?php echo $service['id']; ?>" class="py-5">
        <div class="row align-items-center <?php echo !$isEven ? 'flex-row-reverse' : ''; ?>">
            <div class="col-lg-6 mb-4 mb-lg-0 reveal-<?php echo $isEven ? 'left' : 'right'; ?>">
                <div class="about-icon-box mb-3">
                    <i class="<?php echo $service['icon']; ?>"></i>
                </div>
                <h2 class="display-6 font-weight-800 mb-3"><?php echo htmlspecialchars($service['title']); ?></h2>
                <p class="lead opacity-75 mb-4"><?php echo htmlspecialchars($service['description']); ?></p>
                
                <div class="mb-4">
                    <h5 class="font-weight-700 mb-3">What's Included:</h5>
                    <ul class="list-unstyled">
                        <?php 
                        $benefits = [
                            'Customized Strategy',
                            'Modern Tech Stack',
                            'Conversion Optimization',
                            '24/7 Dedicated Support'
                        ];

                        if (str_contains(strtolower($service['title']), 'seo')) {
                            $benefits = [
                                'Keyword Research & Goal Setting',
                                'On-Page & Technical Audit',
                                'Premium Backlink Strategy',
                                'Monthly Ranking Reports'
                            ];
                        } elseif (str_contains(strtolower($service['title']), 'web') || str_contains(strtolower($service['title']), 'dev')) {
                            $benefits = [
                                'Custom Premium Design',
                                'Mobile-First Performance',
                                'SEO-Ready Architecture',
                                'Secure Cloud Hosting Setup'
                            ];
                        }

                        foreach ($benefits as $benefit): ?>
                            <li class="mb-2 d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-primary"></i>
                                <span><?php echo $benefit; ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <a href="<?php echo SITE_URL; ?>/request_quote?service=<?php echo urlencode($service['title']); ?>" class="btn btn-primary btn-lg">Request Quote</a>
            </div>
            <div class="col-lg-6 reveal-<?php echo $isEven ? 'right' : 'left'; ?>">
                <div class="rounded-3 overflow-hidden shadow-lg border border-white-5">
                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80" alt="<?php echo $service['title']; ?>" class="img-fluid opacity-80 hover-opacity-100 transition-all">
                </div>
            </div>
        </div>
    </section>
    <hr class="opacity-10 my-5">
    <?php endforeach; ?>
</div>

<!-- Extra SEO Section -->
<section class="py-5" style="background: hsla(0, 0%, 100%, 0.02); border-top: 1px solid var(--border);">
    <div class="container py-5 text-center">
        <h2 class="display-5 font-weight-800 mb-4">Need a <span class="text-gradient">Custom Solution?</span></h2>
        <p class="lead opacity-75 mb-5 mx-auto" style="max-width: 700px;">
            Every business is unique. We provide tailored digital ecosystems that fit your specific workflows and goals.
        </p>
        <a href="<?php echo SITE_URL; ?>/contact" class="btn btn-primary btn-lg px-5">Get in Touch</a>
    </div>
</section>

<?php include 'views/partials/footer.php'; ?>
