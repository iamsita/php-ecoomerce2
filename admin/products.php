<?php
if (! is_admin()) {
    header('Location: index.php');
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product']) || isset($_POST['edit_product'])) {
        $name = $_POST['name'];
        $category_id = $_POST['category_id'];
        $description = $_POST['description'];
        $price = $_POST['price'];

        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = handle_image_upload($_FILES['image']);
        }

        if (isset($_POST['add_product'])) {
            if (add_product($name, $category_id, $description, $price, $image)) {
                $message = 'Product added successfully';
            } else {
                $message = 'Error adding product';
            }
        } elseif (isset($_POST['edit_product'])) {
            if (update_product($_POST['id'], $name, $category_id, $description, $price, $image)) {
                header('Location: index.php?page=admin&admin_page=products');
                exit;
            } else {
                $message = 'Error updating product';
            }
        }
    }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (delete_product($_GET['delete'])) {
        $message = 'Product deleted successfully';
    }
}

$edit_product = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $edit_product = get_product($_GET['id']);
}

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($action !== 'edit') {
    include 'includes/search_bar.php';
}

$products = $search_query ? search_products($search_query) : get_products();
?>

<div class="card">
    <div class="card-header">
        <h2><?php echo $action === 'edit' ? 'Edit Product' : 'Manage Products'; ?></h2>
    </div>
    <div class="card-body">
        <?php if ($message) { ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php } ?>

        <form method="post" enctype="multipart/form-data" class="mb-4">
            <?php if ($edit_product) { ?>
                <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
            <?php } ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Product Name</label>
                        <input type="text" name="name" class="form-control" 
                               value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>" 
                               required>
                    </div>
                    <div class="mb-3">
                        <label>Category</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php foreach (get_categories() as $category) { ?>
                                <option value="<?php echo $category['id']; ?>"
                                    <?php echo ($edit_product && $edit_product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Price</label>
                        <input type="number" name="price" class="form-control" step="0.01" 
                               value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>" 
                               required>
                    </div>
                    <div class="mb-3">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control">
                        <?php if ($edit_product && $edit_product['image']) { ?>
                            <div class="mt-2">
                                <img src="<?php echo htmlspecialchars($edit_product['image']); ?>" 
                                     alt="Current image" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        <?php } ?>
                    </div>
                    <button type="submit" name="<?php echo $edit_product ? 'edit_product' : 'add_product'; ?>" 
                            class="btn btn-primary">
                        <?php echo $edit_product ? 'Update Product' : 'Add Product'; ?>
                    </button>
                </div>
            </div>
        </form>

        <?php if ($action !== 'edit') { ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) { ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <?php if ($product['image']) { ?>
                                <img height="100px" width="100px" src="<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="" class="product-thumbnail">
                            <?php } ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <a href="?page=admin&admin_page=products&action=edit&id=<?php echo $product['id']; ?>" 
                               class="btn btn-sm btn-primary">Edit</a>
                            <a href="?page=admin&admin_page=products&delete=<?php echo $product['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div> 