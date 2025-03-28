<?php

function get_products($limit = null, $category_id = 0): array
{
    global $db;
    $category_id = (int) $category_id;
    $sql = 'SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id';
    if ($category_id > 0) {
        $sql .= ' WHERE p.category_id = ' . (int) $category_id;
    }
    if ($limit) {
        $sql .= ' LIMIT ' . (int) $limit;
    }
    $stmt = $db->query($sql);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_product($id)
{
    global $db;
    $stmt = $db->prepare('SELECT p.*, c.name as category_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         WHERE p.id = ?');
    $stmt->execute([$id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_categories(): array
{
    global $db;
    $stmt = $db->query('SELECT * FROM categories');

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function add_to_cart($product_id, $quantity = 1): bool
{
    if (! is_logged_in()) {
        return false;
    }

    $product = get_product($product_id);
    if (! $product) {
        return false;
    }

    if (! isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => $product['image'],
        ];
    }

    return true;
}

function get_cart_total()
{
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }

    return $total;
}

function create_order($order_data)
{
    global $db;
    var_dump($order_data);

    if (! isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return false;
    }

    try {
        $db->beginTransaction();

        // Create order
        $stmt = $db->prepare('INSERT INTO orders (
            user_id, 
            total_amount, 
            shipping_address, 
            phone, 
            payment_method
        ) VALUES (?, ?, ?, ?,  ?)');

        $stmt->execute([
            (int) $order_data['user_id'],
            (float) $order_data['total_amount'],
            (string) $order_data['shipping_address'],
            (string) $order_data['phone'],
            (string) $order_data['payment_method'],
        ]);

        $order_id = $db->lastInsertId();

        // Create order items
        $stmt = $db->prepare('INSERT INTO order_items (
            order_id, 
            product_id, 
            quantity, 
            unit_price, 
            total_price
        ) VALUES (?, ?, ?, ?, ?)');

        foreach ($_SESSION['cart'] as $product_id => $item) {
            $total_price = $item['price'] * $item['quantity'];
            $stmt->execute([
                $order_id,
                (int) $product_id,
                (int) $item['quantity'],
                (float) $item['price'],
                (float) $total_price,
            ]);
        }

        $db->commit();
        unset($_SESSION['cart']); // Clear the cart after successful order

        return $order_id;
    } catch (Exception $e) {
        $db->rollBack();

        return false;
    }
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function is_admin(): bool
{
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function login_user($email, $password): bool
{
    global $db;

    $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_type'] = $user['type'];

        return true;
    }

    return false;
}

function register_user($data): bool
{
    global $db;

    try {
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
        $stmt->execute([
            $data['email'],
            $hashed_password,
        ]);

        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function add_category($name): bool
{
    global $db;
    $stmt = $db->prepare('INSERT INTO categories (name) VALUES (?)');

    return $stmt->execute([$name]);
}

function update_category($id, $name): bool
{
    global $db;
    $stmt = $db->prepare('UPDATE categories SET name = ? WHERE id = ?');

    return $stmt->execute([$name, $id]);
}

function delete_category($id): bool
{
    global $db;
    $stmt = $db->prepare('DELETE FROM categories WHERE id = ?');

    return $stmt->execute([$id]);
}

function add_product($name, $category_id, $description, $price, $image): bool
{
    global $db;
    $stmt = $db->prepare('INSERT INTO products (name, category_id, description, price, image) 
                         VALUES (?, ?, ?, ?, ?)');

    return $stmt->execute([$name, $category_id, $description, $price, $image]);
}

function update_product($id, $name, $category_id, $description, $price, $image): bool
{
    global $db;
    $stmt = $db->prepare('UPDATE products SET name = ?, category_id = ?, description = ?, 
                         price = ?, image = ? WHERE id = ?');

    return $stmt->execute([$name, $category_id, $description, $price, $image, $id]);
}

function delete_product($id): bool
{
    global $db;
    $stmt = $db->prepare('DELETE FROM products WHERE id = ?');

    return $stmt->execute([$id]);
}

function get_all_orders(): array
{
    global $db;
    $stmt = $db->query('SELECT o.*, u.email FROM orders o 
                        JOIN users u ON o.user_id = u.id');

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function update_order_status($order_id, $status): bool
{
    global $db;
    $stmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');

    return $stmt->execute([$status, $order_id]);
}

function get_products_by_category($category_id): array
{
    global $db;
    $stmt = $db->prepare('SELECT p.*, c.name as category_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         WHERE p.category_id = ?');
    $stmt->execute([$category_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function validate_user_data($data): array
{
    $errors = [];

    if (empty($data['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($data['password'])) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($data['password']) < 6) {
        $errors['password'] = 'Password must be at least 6 characters';
    }

    return $errors;
}

function get_status_color($status): string
{
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'shipped':
            return 'primary';
        case 'delivered':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}

function handle_image_upload($image_file)
{
    if (! $image_file || $image_file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Set the directory path relative to document root
    $upload_dir = 'assets/products/';
    if (! file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Get file extension
    $ext = strtolower(pathinfo($image_file['name'], PATHINFO_EXTENSION));

    // Only allow certain file types
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (! in_array($ext, $allowed_types)) {
        return null;
    }

    // Generate simple filename: product_timestamp.extension
    $filename = 'product_' . time() . '.' . $ext;

    // Full server path for moving the file
    $target_path = $upload_dir . $filename;

    // URL path for database storage
    $db_path = '/' . $target_path; // Add leading slash for URL path

    if (move_uploaded_file($image_file['tmp_name'], $target_path)) {
        return $db_path; // Return URL path starting with /assets/...
    }

    return null;
}

function get_category($id)
{
    global $db;
    $stmt = $db->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_user_orders($user_id): array
{
    global $db;

    $stmt = $db->prepare('SELECT * FROM orders WHERE user_id = ?');
    $stmt->execute([$user_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_order_items($order_id): array
{
    global $db;
    $stmt = $db->prepare('
        SELECT oi.*, p.name as product_name 
        FROM order_items oi 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ');
    $stmt->execute([$order_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_category_products_count($category_id)
{
    global $db;
    $stmt = $db->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
    $stmt->execute([$category_id]);

    return $stmt->fetchColumn();
}

function search_products($query, $category_id = 0): array
{
    global $db;
    $category_id = (int) $category_id;
    $search = "%{$query}%";
    $sql = 'SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)';

    if ($category_id > 0) {
        $sql .= ' AND p.category_id = ?'; // Filter by category if provided
        $stmt = $db->prepare($sql);
        $stmt->execute([$search, $search, $search, $category_id]);
    } else {
        $stmt = $db->prepare($sql);
        $stmt->execute([$search, $search, $search]);
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function search_categories($query): array
{
    global $db;
    $search = "%{$query}%";
    $stmt = $db->prepare('SELECT * FROM categories WHERE name LIKE ?');
    $stmt->execute([$search]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function search_orders($query): array
{
    global $db;
    $search = "%{$query}%";
    $stmt = $db->prepare('SELECT o.*, u.email 
                         FROM orders o 
                         JOIN users u ON o.user_id = u.id 
                         WHERE o.id LIKE ? 
                         OR u.email LIKE ? 
                         OR o.phone LIKE ?');
    $stmt->execute([$search, $search,  $search]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function search_user_orders($user_id, $query): array
{
    global $db;
    $search = "%{$query}%";
    $stmt = $db->prepare('SELECT * FROM orders 
                         WHERE user_id = ? 
                         AND (id LIKE ? OR phone LIKE ? OR email LIKE ?)');
    $stmt->execute([$user_id, $search, $search, $search]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_related_products($category_id, $current_product_id, $limit = 4): array
{
    global $db;
    $stmt = $db->prepare('SELECT p.*, c.name as category_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = :category_id 
                         AND p.id != :current_id 
                         ORDER BY RAND() 
                         LIMIT :limit');

    // Need to bind LIMIT parameter explicitly as integer
    $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindValue(':current_id', $current_product_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function dd(...$data)
{
    foreach ($data as $item) {

        echo '<pre>';
        print_r($item);
        echo '</pre>';
    }
    exit();
}
