<?php
// Add this at the top of the file
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

    if (add_to_cart($product_id, $quantity)) {
        $_SESSION['message'] = 'Product added to cart successfully';
        header('Location: index.php?page=home');
        exit;
    }
}

$category_id = isset($_GET['category']) ? (int) $_GET['category'] : 0;
// Add after cart handling
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Add before the products grid

// Update featured products query
$featured_products = $search_query ? search_products($search_query, $category_id) : get_products(20, $category_id);
$categories = get_categories();
?>

<section class="product-section section-p1">

    <?php include 'includes/search_bar.php'; ?>

    <div class="category-list">
        <?php foreach ($categories as $category) { ?>
            <a href="?page=home&category=<?php echo $category['id']; ?>"
               class="<?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($category['name']); ?>
            </a>
        <?php } ?>

    </div>
</section>
<section class="product-section section-p1">

    <div class="pro-collection">
        <?php foreach ($featured_products as $product) { ?>
            <div class="product-cart">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="product image"/>
                <span><?php echo htmlspecialchars($product['category_name']); ?></span>
                <h4 class="name"><?php echo htmlspecialchars($product['name']); ?></h4>
                <h4 class="price">Rs:<?php echo htmlspecialchars($product['price']); ?></h4>
                <?php if (is_logged_in()) { ?>
                    <form method="post" action="index.php?page=home">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                        <input type="hidden" name="add_to_cart" value="1">
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </form>
                <?php } else { ?>
                    <a href="index.php?page=login" class="btn btn-primary">Login to Buy</a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>

<style>
    .category-list {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center
    }

    .category-list a {
        padding: 10px;
        border: 1px solid #088178;
        border-radius: 5px;
        background-color: #088178;
        color: #fff;
        text-align: center;
    }
</style>