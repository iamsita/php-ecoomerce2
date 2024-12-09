<?php
if (! is_logged_in()) {
    $_SESSION['redirect_after_login'] = 'index.php?page=checkout';
    header('Location: index.php?page=login');
    exit;
}

if (empty($_SESSION['cart'])) {
    $_SESSION['message'] = 'Your cart is empty';
    header('Location: index.php?page=cart');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate checkout data
    if (empty($_POST['shipping_address'])) {
        $errors['shipping_address'] = 'Shipping address is required';
    }
    if (empty($_POST['phone'])) {
        $errors['phone'] = 'Phone number is required';
    }

    if (empty($errors)) {
        $order_data = [
            'user_id' => $_SESSION['user_id'],
            'total_amount' => get_cart_total(),
            'shipping_address' => $_POST['shipping_address'],
            'phone' => $_POST['phone'],
            'status' => 'pending',
            'payment_method' => 'cod',
        ];

        $order_id = create_order($order_data);
        if ($order_id) {
            $_SESSION['message'] = 'Order placed successfully!';
            header('Location: index.php?page=orders');
            exit;
        } else {
            $errors['general'] = 'Failed to place order. Please try again.';
        }
    }
}
?>


<div class="checkout-container product-section">
    <div class="left-section card">
        <h2 class="mb-2">Shipping Address</h2>
        <form method="post">
            <div class="form-group">
                <label for="address">Shipping Address*</label>
                <input type="text" id="address" name="shipping_address" required placeholder="Enter your shipping address">
            </div>
            <div class="form-group">
                <label for="phone">Phone Number*</label>
                <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number">
            </div>
            <button type="submit" class="btn btn-primary">Continue</button>
            <a href="?page=cart" class="btn btn-secondary">Back to Cart</a>
        </form>
    </div>

    <div class="right-section card">
        <h2>Order Summary</h2>
        <div class="order-summary">
            <?php foreach ($_SESSION['cart'] as $product_id => $item) { ?>
                <div class="summary-item">
                    <span><?php echo htmlspecialchars($item['name']); ?> (×<?php echo $item['quantity']; ?>)</span>
                    <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </div>
            <?php } ?>
            <hr>
            <div class="summary-item total">
                <span>Total</span>
                <span>$<?php echo number_format(get_cart_total(), 2); ?></span>
            </div>
            <button class="btn btn-primary">Proceed to Payment</button>
        </div>
    </div>
</div>
