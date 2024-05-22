<?php
    session_start();
    $pageTitle = "P&G Laz-Run Quiz";
    include 'header.php';
    include 'db.php';
    include_once 'detect.php'; 


    // Category Session (DO NOT TOUCH)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $_SESSION['selectedCategory'] = $_POST['category'];
        header("Location: category-page.php");
        exit();
    }
?>
<link rel="stylesheet" href="../assets/styles.css">
<div class="body-wrapper bg2">
    <div class="wrapper">
        <div class="spacer"></div>
        <div class="header-container">
            <h1>Solutions you're<br>looking for</h1>
        </div>
        <div class="spacer"></div>
        <div class="options-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form" method="post">
                <?php
                $categoryQuery = "SELECT * FROM category";
                $categoryResult = mysqli_query($conn, $categoryQuery);

                while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                    $categoryID = $categoryRow['categoryID'];
                    $categoryName = $categoryRow['categoryName'];
                    ?>
                    <div class="buttons">
                        <button type="submit" name="category" value="<?php echo $categoryID; ?>">
                            <?php echo $categoryName; ?>
                        </button>
                    </div>
                <?php
                }
                ?>
            </form>
        </div>
    </div>
</div>