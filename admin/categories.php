<?php
if (! is_admin()) {
    header('Location: index.php');
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = '';

// Get category for editing
$edit_category = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $edit_category = get_category($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_category'])) {
        $name = $_POST['name'];
        if (add_category($name)) {
            $message = 'Category added successfully';
        }
    } elseif (isset($_POST['edit_category'])) {
        $name = $_POST['name'];
        if (update_category($_POST['id'], $name)) {
            header('Location: index.php?page=admin&admin_page=categories');
            exit;
        }
    }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (delete_category($_GET['delete'])) {
        $message = 'Category deleted successfully';
    }
}

// Add after authentication check
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Add before the category list
if ($action !== 'edit') {
    include 'includes/search_bar.php';
}

// Update category listing query
$categories = $search_query ? search_categories($search_query) : get_categories();
?>

<div class="card">
    <div class="card-header">
        <h2><?php echo $action === 'edit' ? 'Edit Category' : 'Manage Categories'; ?></h2>
    </div>
    <div class="card-body">
        <?php if ($message) { ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php } ?>

        <form method="post" class="mb-4">
            <?php if ($edit_category) { ?>
                <input type="hidden" name="id" value="<?php echo $edit_category['id']; ?>">
            <?php } ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Category Name</label>
                        <input type="text" name="name" class="form-control" 
                               value="<?php echo $edit_category ? htmlspecialchars($edit_category['name']) : ''; ?>" 
                               placeholder="Category Name" required>
                    </div>
                    <button type="submit" 
                            name="<?php echo $edit_category ? 'edit_category' : 'add_category'; ?>" 
                            class="btn btn-primary">
                        <?php echo $edit_category ? 'Update Category' : 'Add Category'; ?>
                    </button>
                    <?php if ($edit_category) { ?>
                        <a href="index.php?page=admin&admin_page=categories" class="btn btn-secondary">Cancel</a>
                    <?php } ?>
                </div>
            </div>
        </form>

        <?php if ($action !== 'edit') { ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category) { ?>
                    <tr>
                        <td><?php echo $category['id']; ?></td>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td>
                            <a href="?page=admin&admin_page=categories&action=edit&id=<?php echo $category['id']; ?>" 
                               class="btn btn-sm btn-primary">Edit</a>
                            <a href="?page=admin&admin_page=categories&delete=<?php echo $category['id']; ?>" 
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