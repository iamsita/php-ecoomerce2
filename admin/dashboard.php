<?php
if (! is_admin()) {
    header('Location: index.php');
    exit;
}

// Get summary statistics
$total_products = count(get_products());
$total_categories = count(get_categories());
$total_orders = count(get_all_orders());

// Get recent orders
$recent_orders = array_slice(get_all_orders(), 0, 5);
?>

<div class="card mb-4">
   
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <h5>Total Products</h5>
                        <h2><?php echo $total_products; ?></h2>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="index.php?page=admin&admin_page=products">View Products</a>
                        <div class="small text-white"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <h5>Total Categories</h5>
                        <h2><?php echo $total_categories; ?></h2>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="index.php?page=admin&admin_page=categories">View Categories</a>
                        <div class="small text-white"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white mb-4">
                    <div class="card-body">
                        <h5>Total Orders</h5>
                        <h2><?php echo $total_orders; ?></h2>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="index.php?page=admin&admin_page=orders">View Orders</a>
                        <div class="small text-white"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4>Recent Orders</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order) { ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['email']); ?></td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo get_status_color($order['status']); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 