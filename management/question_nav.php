<?php
    if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
        header("Location: index.php");
        exit();
    }

    if (!isset($_SESSION['last_active_page'])) {
        $_SESSION['last_active_page'] = 'question.php'; 
    }
?>
<style>
    .nav-link.inactive {
        color: darkgray;
    }
</style>

<h1 class="text-center fw-bold mt-4">Quiz Management</h1>
<hr style="height:1px;border-width:0;color:gray;background-color:gray">
<div class="container w-75 my-3">
    <ul class="nav nav-underline d-flex justify-content-center">
        <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'question.php') ? 'active' : 'inactive'; ?>" href="question.php">Main Questions</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'conditional-question.php') ? 'active' : 'inactive'; ?>" href="conditional-question.php">Conditional Questions</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'voucher-question.php') ? 'active' : 'inactive'; ?>" href="voucher-question.php">Voucher Questions</a>
        </li>
    </ul>
</div>
