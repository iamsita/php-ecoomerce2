<?php
if (! is_logged_in()) {
    $_SESSION['redirect_after_login'] = 'index.php?page=cart';
    header('Location: index.php?page=login');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
        $message = 'Cart updated successfully';
    } elseif (isset($_POST['checkout'])) {
        $order_id = create_order($_SESSION['user_id']);
        if ($order_id) {
            header('Location: index.php?page=orders');
            exit;
        }
    } elseif (isset($_POST['remove_item'])) {
        $product_id = $_POST['remove_item'];
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $message = 'Item removed from cart';
        }
    }
}
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Shopping Cart</h2>
        </div>
        <div class="card-body">
            <?php if ($message) { ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php } ?>

            <?php if (! empty($_SESSION['cart'])) { ?>
                <form method="post">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['cart'] as $product_id => $item) { ?>
                                <tr>
                                    <td>
                                        <?php if (isset($item['image'])) { ?>
                                            <img height="50px" src="<?php echo htmlspecialchars($item['image']); ?>" alt="">
                                        <?php } ?>
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <input type="number" name="quantity[<?php echo $product_id; ?>]" 
                                               value="<?php echo $item['quantity']; ?>" min="0" 
                                               class="form-control" style="width: 80px;">
                                    </td>
                                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    <td>
                                        <button type="submit" name="remove_item" value="<?php echo $product_id; ?>" 
                                                class="btn btn-sm btn-danger">Remove</button>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td colspan="2"><strong>$<?php echo number_format(get_cart_total(), 2); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-end">
                        <button type="submit" name="update_cart" class="btn btn-secondary">Update Cart</button>
                        <a href="index.php?page=checkout" class="btn btn-primary">Proceed to Checkout</a>
                    </div>
                </form>
            <?php } else { ?>
                <p>Your cart is empty.</p>
                <a href="index.php?page=products" class="btn btn-primary">Continue Shopping</a>
            <?php } ?>
        </div>
    </div>
</div>

