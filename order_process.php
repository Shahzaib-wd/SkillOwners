<?php
require_once 'config.php';
require_once 'models/Order.php';
require_once 'models/Gig.php';

requireLogin();

if (getUserRole() !== 'buyer') {
    showError("Only buyers can place orders.");
    redirect('/index');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($token)) {
        showError("Invalid session. Please try again.");
        redirect('/index');
    }

    $gigId = $_POST['gig_id'] ?? 0;
    $gigModel = new Gig();
    $gig = $gigModel->findById($gigId);

    if (!$gig) {
        showError("Gig not found.");
        redirect('/browse');
    }

    $buyerId = $_SESSION['user_id'];
    $sellerId = $gig['user_id'];
    
    // Safety check: Cannot order own gig
    if ($buyerId == $sellerId) {
        showError("You cannot order your own gig.");
        redirect('/gig?id=' . $gigId);
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
        // Send confirmation email
        require_once 'helpers/MailHelper.php';
        
        $seller = (new User())->findById($sellerId);
        $buyer = (new User())->findById($buyerId);
        
        $subject = "New Order Received - " . SITE_NAME;
        $body = "
        <h3>New Order Notification</h3>
        <p>Hello " . htmlspecialchars($seller['full_name']) . ",</p>
        <p>You have received a new order from <strong>" . htmlspecialchars($buyer['full_name']) . "</strong>.</p>
        <p><strong>Gig:</strong> " . htmlspecialchars($gig['title']) . "</p>
        <p><strong>Amount:</strong> $" . number_format($amount, 2) . "</p>
        <p><strong>Expected Delivery:</strong> " . $deliveryDate . "</p>
        <p>Please log in to your dashboard to view the order details and start working.</p>";
        
        MailHelper::send($seller['email'], $subject, $body);
        
        showSuccess("Order placed successfully!");
        redirect('/dashboard/buyer/index');
    } else {
        showError("Something went wrong while processing your order. Please try again.");
        redirect('/checkout?id=' . $gigId);
    }
} else {
    redirect('/index');
}
