<?php
require_once 'config.php';
require_once 'models/Review.php';
require_once 'models/Order.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($token)) {
        showError("Invalid session. Please try again.");
        redirect('/dashboard/buyer/orders.php');
    }

    $orderId = $_POST['order_id'] ?? 0;
    $rating = $_POST['rating'] ?? 0;
    $comment = sanitizeInput($_POST['comment'] ?? '');

    // Basic Validation
    if ($rating < 1 || $rating > 5) {
        showError("Please provide a rating between 1 and 5.");
        redirect('/dashboard/buyer/orders.php');
    }

    if (empty($comment)) {
        showError("Please provide a comment for your review.");
        redirect('/dashboard/buyer/orders.php');
    }

    // Profanity Filter
    if (containsProfanity($comment)) {
        showError("Your review contains inappropriate language. Please keep it professional.");
        redirect('/dashboard/buyer/orders.php');
    }

    $orderModel = new Order();
    $order = $orderModel->findById($orderId);

    if (!$order || $order['buyer_id'] != $_SESSION['user_id']) {
        showError("Unauthorized order review.");
        redirect('/dashboard/buyer/orders.php');
    }

    if ($order['status'] !== 'completed') {
        showError("You can only review completed orders.");
        redirect('/dashboard/buyer/orders.php');
    }

    $reviewModel = new Review();
    if ($reviewModel->hasReviewed($orderId)) {
        showError("You have already reviewed this order.");
        redirect('/dashboard/buyer/orders.php');
    }

    $reviewData = [
        'order_id' => $orderId,
        'gig_id' => $order['gig_id'],
        'buyer_id' => $_SESSION['user_id'],
        'rating' => $rating,
        'comment' => $comment
    ];

    if ($reviewModel->create($reviewData)) {
        showSuccess("Your review has been submitted successfully!");
    } else {
        showError("Failed to submit review. Please try again.");
    }

    redirect('/dashboard/buyer/orders.php');
} else {
    redirect('/index.php');
}
