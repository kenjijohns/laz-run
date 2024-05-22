<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION)) {
    session_start();
}

$allowedPages = array('product.php', 'question.php', 'analytics.php', 'voucher.php');
?>
<style>
    .navbar {
        background-color: #000;
    }

    .navbar-brand {
        font-size: 2rem;
        font-weight: bold;
    }

    .navb {
        color: white !important;
        transition: background-color 0.3s, color 0.3s;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark">

    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a href="index.php">
            <img src="../assets/images/PG.png" alt="P&G Logo" style="height: 30px; margin: 10px;">
        </a>
        <?php if (isset($_SESSION['user_authenticated'])) : ?>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav nav-underline ms-auto text-end">
                    <?php if ($_SESSION['user_role'] == 'admin') : ?>
                        <?php if ($_SESSION['user_role'] == 'admin') : ?>
                            <li class="nav-item">
                                <a class="navb nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'product.php' || basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'active' : 'inactive'; ?>" href="product.php">Products</a>
                            </li>
                            <li class="nav-item">
                                <a class="navb nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'question.php' || basename($_SERVER['PHP_SELF']) == 'conditional-question.php') ? 'active' : 'inactive'; ?>" href="question.php">Questions</a>
                            </li>
                            <li class="nav-item">
                                <a class="navb nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'voucher.php' || basename($_SERVER['PHP_SELF']) == 'conditional-voucher.php') ? 'active' : 'inactive'; ?>" href="voucher.php">Vouchers</a>
                            </li>
                            <li class="nav-item">
                                <a class="navb nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'analytics.php') ? 'active' : 'inactive'; ?>" href="analytics.php">Analytics</a>
                            </li>
                            <li class="nav-item">
                                <a class="navb nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'customize.php') ? 'active' : 'inactive'; ?>" href="customize.php">Customization</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="navb nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
    </div>
</nav>
<?php endif; ?>
</ul>
</div>
</nav>
</div>



<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>