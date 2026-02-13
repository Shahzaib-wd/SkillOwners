<?php
require_once '../config.php';
require_once '../models/Order.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($token)) {
        showError("Invalid session. Please try again.");
        redirect('/dashboard/' . getUserRole() . '/index.php');
    }

    $orderId = $_POST['order_id'] ?? 0;
    $userId = $_SESSION['user_id'];
    
    $orderModel = new Order();
    $order = $orderModel->findById($orderId);

    if (!$order) {
        showError("Order not found.");
        redirect('/dashboard/' . getUserRole() . '/index.php');
    }

    // Security: Only buyer or seller can confirm
    if ($userId != $order['buyer_id'] && $userId != $order['seller_id']) {
        showError("Unauthorized action.");
        redirect('/dashboard/' . getUserRole() . '/index.php');
    }

    if ($orderModel->confirmOrder($orderId, $userId)) {
        showSuccess("Order confirmation recorded. Waiting for the other party if not yet completed.");
    } else {
        showError("Failed to record confirmation. Please try again.");
    }

    redirect('/dashboard/' . getUserRole() . '/index.php');
} else {
    redirect('/index.php');
}
