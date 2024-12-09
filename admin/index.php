<?php
if (! isset($_SESSION['user_id']) || ! is_admin()) {
    header('Location: index.php?page=login');
    exit;
}

$admin_page = $_GET['admin_page'] ?? 'dashboard';
?>

<div class="row mt-5">
    <div class="col-12">
        <div class="list-group">
            <a href="index.php?page=admin&admin_page=dashboard" class="list-group-item">Dashboard</a>
            <a href="index.php?page=admin&admin_page=products" class="list-group-item">Products</a>
            <a href="index.php?page=admin&admin_page=categories" class="list-group-item">Categories</a>
            <a href="index.php?page=admin&admin_page=orders" class="list-group-item">Orders</a>
        </div>
    </div>
    <div class="col-12">
        <?php
        switch ($admin_page) {
            case 'products':
                include 'admin/products.php';
                break;
            case 'categories':
                include 'admin/categories.php';
                break;
            case 'orders':
                include 'admin/orders.php';
                break;
            default:
                include 'admin/dashboard.php';
                break;
        }
?>
    </div>
</div> 
<style>
   .list-group{
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    margin-left: 10px;
    margin-top: 2rem;
   }
   .list-group-item{
    border: none;
    border-bottom: 1px solid #e0e0e0;
    padding: 1rem;
    background-color: #f8f9fa;
    color: #333;
   }
</style>