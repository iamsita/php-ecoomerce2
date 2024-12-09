<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
$page = $_GET['page'] ?? 'home';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Ecommerce website</title>
    <link rel="stylesheet" href="assets/css/style.css"/>
    <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
    />
</head>
<body>
<div id="header">
    <div class="header-logo">
        <a href="?page=home"><img src="assets/images/logo.png" alt=""/></a>
    </div>
    <div class="header-list">
        <nav class="header-list-nav">
            <ul>
                <li><a class="<?php echo $page === 'home' ? 'active' : ''; ?>" href="?page=home">Home</a></li>
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <li><a class="<?php echo $page === 'cart' ? 'active' : ''; ?>" href="?page=cart">Cart</a></li>
                    <li><a class="<?php echo $page === 'orders' ? 'active' : ''; ?>" href="?page=orders">My Orders</a></li>
                    <li><a class="<?php echo $page === 'logout' ? 'active' : ''; ?>" href="?page=logout">Logout</a></li>
                <?php } else { ?>
                    <li><a class="<?php echo $page === 'login' ? 'active' : ''; ?>" href="?page=login">Login</a></li>
                    <li><a class="<?php echo $page === 'register' ? 'active' : ''; ?>" href="?page=register">Register</a></li>
                <?php } ?>
                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') { ?>
                    <li><a class="<?php echo $page === 'admin' ? 'active' : ''; ?>" href="?page=admin">Admin Panel</a></li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</div>
<?php
switch ($page) {
    case 'home':
        include 'pages/home.php';
        break;
    case 'cart':
        include 'pages/cart.php';
        break;
    case 'login':
        include 'pages/login.php';
        break;
    case 'register':
        include 'pages/register.php';
        break;
    case 'orders':
        include 'pages/orders.php';
        break;
    case 'admin':
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
            include 'admin/index.php';
        } else {
            header('Location: index.php');
            exit;
        }
        break;
    case 'logout':
        include 'pages/logout.php';
        break;
    case 'checkout':
        include 'pages/checkout.php';
        break;
    case 'product_detail':
        include 'pages/product_detail.php';
        break;
    default:
        include 'pages/404.php';
        break;
}
?>
</body>
</html>
