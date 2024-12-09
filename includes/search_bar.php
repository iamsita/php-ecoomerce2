

<?php
$page = htmlspecialchars($_GET['page'] ?? (isset($_GET['admin_page']) ? 'dashboard' : 'home'));
$admin_page = isset($_GET['admin_page']) ? htmlspecialchars($_GET['admin_page']) : null;
$category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : null;
$search_value = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
?>
<section class="search-bar">
<form method="get">
    <div>
        <input type="hidden" name="page" value="<?php echo $page; ?>">
        <?php if ($admin_page) { ?>
            <input type="hidden" name="admin_page" value="<?php echo $admin_page; ?>">
        <?php } ?>
        <?php if ($category) { ?>
            <input type="hidden" name="category" value="<?php echo $category; ?>">
        <?php } ?>
        <input type="text" name="search"
               placeholder="Search..."
               value="<?php echo $search_value; ?>"
               class="search-input">
        <button type="submit" class="search-button">Search</button>
        <a href="?page=<?php echo $page; ?>" class="search-button">Clear</a>
    </div>
</form>
</section>
<style>
  .search-bar {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
    margin-bottom: 20px;
  }
  .search-input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    width: 300px; /* Adjust width as needed */
    margin-right: 10px; /* Space between input and button */
  }
  .search-button {
    background-color: #088178;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    text-decoration: none;
  }
  .search-button:hover {
    background-color: #066f66;
  }
</style>