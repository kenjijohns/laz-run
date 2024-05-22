<?php
    if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
        header("Location: index.php");
        exit();
    }

    if (!isset($_SESSION['last_active_page'])) {
        $_SESSION['last_active_page'] = 'product.php'; 
    }
?>
<style>
    .nav-link.inactive {
        color: darkgray;
    }
</style>

<div class="container w-75 my-3">
    <ul class="nav nav-underline d-flex justify-content-center">
        <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'product.php') ? 'active' : 'inactive'; ?>" href="product.php">Products</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'active' : 'inactive'; ?>" href="categories.php">Product Categories</a>
        </li>
    </ul>
</div>
