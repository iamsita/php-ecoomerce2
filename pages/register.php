<?php
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $user_data = [
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
    ];
    // Validate input
    $errors = validate_user_data($user_data);

    // If no errors, try to register
    if (empty($errors)) {
        if (register_user($user_data)) {
            $success = true;
        } else {
            $errors['general'] = 'Registration failed. Email might already exist.';
        }
    }
}
?>

<section class="login-section">
    <div class="login-container">
        <h2>Register</h2>
        <?php if ($success) { ?>
            <div class="alert alert-success">
                Registration successful! You can now <a href="?page=login">login</a>.
            </div>
        <?php } else { ?>
            <?php if (isset($errors['general'])) { ?>
                <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
            <?php } ?>
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required/>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required/>
                </div>
                <button type="submit" class="login-btn">Register</button>
            </form>
        <?php } ?>
    </div>
</section>
