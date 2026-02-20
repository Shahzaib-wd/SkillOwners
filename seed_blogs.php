<?php
require_once 'config.php';
$db = getDBConnection();

$posts = [
    [
        'title' => 'The Future of Web Development in 2026',
        'slug' => 'future-web-development-2026',
        'category' => 'Technology',
        'excerpt' => 'How AI and edge computing are reshaping the way we build and deploy digital experiences.',
        'content' => '<p>The landscape of web development is undergoing a seismic shift. As we venture into late 2026, the integration of Artificial Intelligence (AI) and Edge Computing has moved from bleeding-edge experimentation to industry-standard practice.</p><h2>The Rise of AI-Driven Architecture</h2><p>Modern applications are no longer just static interfaces. They are dynamic systems that anticipate user needs. By leveraging LLMs for real-time personalization, developers are creating experiences that adapt to individual user behavior.</p><h3>Edge Computing: Zero Latency is the New Standard</h3><p>Serverless functions at the edge have revolutionized performance. By moving computation closer to the user, we have effectively eliminated the geographical barriers to speed, ensuring a premium experience regardless of location.</p>',
        'featured_image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200'
    ],
    [
        'title' => 'Mastering SEO for Enterprise Growth',
        'slug' => 'mastering-seo-enterprise-growth',
        'category' => 'SEO',
        'excerpt' => 'Strategic approaches for scaling your search presence in highly competitive global markets.',
        'content' => '<p>Search Engine Optimization is no longer just about keywords; it is about authority and semantic relevance. For enterprise-level organizations, the challenge is maintaining consistency while scaling across multiple regions.</p><h2>The Power of Semantic Search</h2><p>Search engines have evolved to understand intent. Our strategy focuses on building topical clusters that establish your brand as a definitive source of truth in your industry.</p><h3>Technical SEO at Scale</h3><p>Architecture matters. We implement robust schema marup and high-performance server configurations that ensure search crawlers can index your content with institutional efficiency.</p>',
        'featured_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200'
    ],
    [
        'title' => 'Scaling Your Business with Performance Ads',
        'slug' => 'scaling-business-performance-ads',
        'category' => 'Marketing',
        'excerpt' => 'A data-driven approach to maximizing ROI across social and search advertising channels.',
        'content' => '<p>In a world of fragmented attention, performance marketing provides the precision needed to drive measurable growth. Our approach combines creative excellence with rigorous data analysis.</p><h2>The DNA of High-Conversion Ads</h2><p>Great marketing is the intersection of psychology and analytics. We test thousands of variables to identify the exact message that resonates with your target demographic.</p><h3>Maximizing ROI Through Attribution</h3><p>Understanding the customer journey is critical. We implement advanced attribution models that reveal the true value of every marketing dollar spent, allowing for surgical budget allocation.</p>',
        'featured_image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200'
    ]
];

try {
    $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, category, excerpt, content, featured_image, status) VALUES (?, ?, ?, ?, ?, ?, 'published')");
    foreach($posts as $post) {
        $stmt->execute([$post['title'], $post['slug'], $post['category'], $post['excerpt'], $post['content'], $post['featured_image']]);
    }
    echo "Successfully seeded " . count($posts) . " blog posts!";
} catch (Exception $e) {
    echo "Seeding failed: " . $e->getMessage();
}
?>
