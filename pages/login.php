<?php
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (login_user($email, $password)) {
        $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
        unset($_SESSION['redirect_after_login']); // Clear the stored redirect
        header('Location: ' . $redirect);
        exit;
    } else {
        $error = 'Invalid email or password';
    }
}

// Store the current page as redirect destination if it's not the login page
if (!isset($_SESSION['redirect_after_login']) && isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    if (!strpos($referer, 'login') && !strpos($referer, 'register')) {
        $_SESSION['redirect_after_login'] = $referer;
    }
}
?>

<section class="login-section">
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <form  method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required/>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required/>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</section>


<style>
  
</style>
