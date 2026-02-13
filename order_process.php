<?php
require_once 'config.php';
require_once 'models/Order.php';
require_once 'models/Gig.php';

requireLogin();

if (getUserRole() !== 'buyer') {
    showError("Only buyers can place orders.");
    redirect('/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($token)) {
        showError("Invalid session. Please try again.");
        redirect('/index.php');
    }

    $gigId = $_POST['gig_id'] ?? 0;
    $gigModel = new Gig();
    $gig = $gigModel->findById($gigId);

    if (!$gig) {
        showError("Gig not found.");
        redirect('/browse.php');
    }

    $buyerId = $_SESSION['user_id'];
    $sellerId = $gig['user_id'];
    
    // Safety check: Cannot order own gig
    if ($buyerId == $sellerId) {
        showError("You cannot order your own gig.");
        redirect('/gig.php?id=' . $gigId);
    }

    $amount = $gig['price'];
    $deliveryDays = $gig['delivery_time'];
    $deliveryDate = date('Y-m-d', strtotime("+$deliveryDays days"));

    $orderModel = new Order();
    $orderData = [
        'gig_id' => $gigId,
        'buyer_id' => $buyerId,
        'seller_id' => $sellerId,
        'amount' => $amount,
        'delivery_date' => $deliveryDate
    ];

    if ($orderModel->create($orderData)) {
        showSuccess("Order placed successfully!");
        redirect('/dashboard/buyer/index.php');
    } else {
        showError("Something went wrong while processing your order. Please try again.");
        redirect('/checkout.php?id=' . $gigId);
    }
} else {
    redirect('/index.php');
}
