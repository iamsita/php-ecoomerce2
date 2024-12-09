<?php
if (! is_logged_in()) {
    $_SESSION['redirect_after_login'] = 'index.php?page=orders';
    header('Location: index.php?page=login');
    exit;
}

// Add after authentication check
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Add before the orders list
include 'includes/search_bar.php';

// Get user's orders
$orders = $search_query ? search_user_orders($_SESSION['user_id'], $search_query) : get_user_orders($_SESSION['user_id']);
?>


<div class="card">
    <div class="card-header p-2">
        <h2>My Orders</h2>
    </div>
    <div class="card-body">
        <?php if (empty($orders)) { ?>
            <p>You haven't placed any orders yet.</p>
            <a href="index.php?page=home" class="btn btn-primary">Start Shopping</a>
        <?php } else { ?>
            <table class="table" border>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Status</th>
                        <th>Order Items</th>
                        <th>Total</th>
                        <th>Shipping Address</th>
                        <th>Contact</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) { ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td>
                                <span class="badge bg-<?php echo get_status_color($order['status']); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <table border class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $order_items = get_order_items($order['id']);

                        foreach ($order_items as $item) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td>$<?php echo number_format($item['unit_price'] ?? 0, 2); ?></td>
                                                <td>$<?php echo number_format($item['total_price'] ?? 0, 2); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></td>
                            <td>
                                Phone: <?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?><br>
                            </td>
                            <td>
                              
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>

<style>
    /* General Styles */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f6f8;
    margin: 0;
    padding: 0;
}




.badge {
    padding: 8px 15px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 20px;
}

.badge.bg-success {
    background-color: #28a745;
}

.badge.bg-warning {
    background-color: #ffc107;
}

.badge.bg-danger {
    background-color: #dc3545;
}

/* Order Items */
.order-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.order-item span {
    font-size: 14px;
    color: #333;
}

.order-item .price {
    font-weight: bold;
    color: #007bff;
}

hr {
    border: 0;
    border-top: 1px solid #e0e0e0;
    margin: 20px 0;
}

/* Total Price */
.d-flex {
    display: flex;
    justify-content: space-between;
}

.d-flex strong {
    font-size: 16px;
    font-weight: 600;
}

/* Shipping and Contact Info */
.card-body .col-md-4 h6 {
    font-weight: bold;
    color: #333;
}

.card-body .col-md-4 p {
    color: #555;
    font-size: 14px;
}

.card-body .col-md-4 p a {
    color: #007bff;
    text-decoration: none;
}

.card-body .col-md-4 p a:hover {
    text-decoration: underline;
}

/* Empty Orders Message */
.card-body .empty-orders {
    text-align: center;
    padding: 50px;
    color: #777;
}

.card-body .empty-orders a {
    font-weight: bold;
    color: #007bff;
    text-decoration: none;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.card-body .empty-orders a:hover {
    background-color: #0056b3;
}

/* Search Bar Styles */
.search-bar {
    padding: 15px;
    margin-bottom: 20px;
    background-color: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}

.search-bar input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    margin-right: 10px;
}

.search-bar button {
    padding: 10px 20px;
    border: none;
    background-color: #007bff;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.search-bar button:hover {
    background-color: #0056b3;
}

/* Responsive Design */
@media (max-width: 768px) {
    .card-body .col-md-8, .card-body .col-md-4 {
        flex: 0 0 100%;
    }

    .search-bar input {
        margin-bottom: 10px;
    }
}



</style>