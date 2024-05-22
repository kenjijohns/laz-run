<?php
    $pageTitle = "P&G Laz-Run Quiz";
    include 'header.php';
    include 'db.php';
    session_start();
    if (isset($_SESSION['quizProgress'])) {
        unset($_SESSION['quizProgress']);
    }
    
    if (!isset($_SESSION['selectedCategory']) || !isset($_SESSION['productTally'])) {
        $selectedCategory = 4;
        $currentQuestion = 1;
        $productTally = array();
        $answers = array();
    }
    else{
        $selectedCategory = $_SESSION['selectedCategory'];
        $currentQuestion = $_SESSION['currentQuestion'];
        $productTally = $_SESSION['productTally'];
        $answers = $_SESSION['selectedAnswers'];
    }
   
    
    $bonusQuestionQuery = "SELECT bqID, bqContent FROM bonus_question WHERE categoryID = '$selectedCategory'";
    $bonusQuestionResult = mysqli_query($conn, $bonusQuestionQuery);
    $bonusQuestion = mysqli_fetch_assoc($bonusQuestionResult);

    $maxTallyProducts = array();
    $maxTally = 0;

    if (!isset($_SESSION['prodID'])) {
        $_SESSION['prodID'] = array();
    }

    foreach ($productTally as $product) {
        if ($product['tally'] == $maxTally) {
            $maxTallyProducts[] = $product;
            $_SESSION['prodID'][] = $product['id']; 
        } elseif ($product['tally'] > $maxTally) {
            $maxTally = $product['tally'];
            $maxTallyProducts = array($product);
            $_SESSION['prodID'] = array($product['id']); 
        }
    }
?>
<link rel="stylesheet" href="../assets/styles.css">
<div class="body-wrapper bg5">
    <div class="wrapper justify-center gap20">
        <div class="result-title">
            <h3>Thanks for waiting</h3>
            <p>Here are the products that might interest you.</p>
        </div>
        <div class="suggested-products masked-overflow">
        <?php if (empty($maxTallyProducts)): ?>
            <div class="product-container">
                        <div class="product-body">
                        <img src="management/<?php echo $prodImage; ?>" class="suggested-image" alt="Product Image" class="img-fluid">
                               
                                <form action="process.php" method="post" style="margin:0px;">
                                    <input type="hidden" name="prodID" value="<?php echo $prodID; ?>">
                                    <button name="outbound" onclick="window.open('<?php echo $prodURL; ?>', '_blank')" class="view-product-button">VIEW PRODUCT</button>
                                </form>
                        </div>
                        </div>
            <?php endif; ?>
            <?php if (!empty($maxTallyProducts)): ?>
                <?php foreach ($maxTallyProducts as $maxTallyProduct): ?>
                    <div class="product-container">
                        <div class="product-body">
                            <?php
                            $prodID = $maxTallyProduct['id'];
                            $productDetailsQuery = "SELECT prodImage, prodURL, prodDescription FROM product WHERE prodID = '$prodID' AND categoryID = '$selectedCategory'";
                            $productDetailsResult = mysqli_query($conn, $productDetailsQuery);

                            if ($productDetailsRow  = mysqli_fetch_assoc($productDetailsResult)) {
                                $prodImage          = $productDetailsRow['prodImage'];
                                $prodURL            = $productDetailsRow['prodURL'];
                                $prodDescription    = $productDetailsRow['prodDescription'];
                                if (!isset($_SESSION['finish_insertion_done'])) {
                                    $guestID    = $_SESSION['guestID'];
                                    $device     = $_SESSION['deviceType'];
                                    $country    = $_SESSION['country'];
                                    $source     = $_SESSION['referrer'];
                                    $lastID     = $_SESSION['last_session_id'];
                                    
                                    $query  = "SELECT * FROM session WHERE sessionID = '$lastID' AND prodID IS NULL";
                                    $result = mysqli_query($conn, $query);
                                }
                                ?>
                                <img src="management/<?php echo $prodImage; ?>" class="suggested-image" alt="Product Image" class="img-fluid">
                                <p><?php echo $maxTallyProduct['name']; ?></p>
                                <form action="process.php" method="post" style="margin:0px;">
                                    <input type="hidden" name="prodID" value="<?php echo $prodID; ?>">
                                    <button name="outbound" onclick="window.open('<?php echo $prodURL; ?>', '_blank')" class="view-product-button">VIEW PRODUCT</button>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No favorable product found.</p>
        <?php endif; ?>
        </div>
        <div class="voucher">
            <?php if ($bonusQuestion && isset($_SESSION['bonusAnswered'])): ?>
                <?php
                if ($_SESSION['bonusAnswered']) {
                    $voucherQuery = "SELECT voucherCode FROM voucher WHERE categoryID = '$selectedCategory'";
                    $voucherResult = mysqli_query($conn, $voucherQuery);
                    
                    if ($voucherResult && $voucher = mysqli_fetch_assoc($voucherResult)['voucherCode']) {
                        ?>
                        <h4><strong>Congratulations!</strong><br>Here's your voucher code</h4>
                        <div class="voucher-code-container">
                            <h1 id='voucher'><?php echo $voucher; ?></h1>
                            <i onclick="copyToClipboard('#voucher')" class="fa-regular fa-clone" id="copy"></i>
                            
                        </div>
                        <form action="process.php" method="post">
                            <div class="nav-buttons">
                                <button type="submit" name="home">BACK TO HOME</button>
                            </div>
                        </form>
                    <?php
                    } 
                } else {
                    echo "<p>Thanks for trying! Unfortunately, you didn't get the correct answer.</p>";
                    ?>
                    <form action="process.php" method="post">
                        <div class="nav-buttons">
                            <button type="submit" name="home">BACK TO HOME</button>
                        </div>
                    </form>
                <?php
                }
            ?>
        </div>
    <?php elseif ($bonusQuestion): ?> 
        <form action="process.php" method="post">
            <div class="nav-buttons">
                <button type="submit" name="bonus">GET VOUCHER</button>
                <button type="submit" name="home">BACK TO HOME</button>
            </div>
        </form>
    <?php else: ?>
        <form action="process.php" method="post">
            <div class="nav-buttons">
                <button type="submit" name="home">BACK TO HOME</button>
            </div>
        </form>
    <?php endif;  ?>
    </div>
</div>

<script>
    document.getElementById('voucher').style.cursor = 'pointer'; 
    document.getElementById('voucher').addEventListener('click', function() {
        copyToClipboard('#voucher');
    });

    function copyToClipboard(elementId) {
        var voucherCode = document.querySelector(elementId).innerText;
        var tempInput = document.createElement("input");
        tempInput.value = voucherCode;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);

        var copyDiv = document.getElementById("copy");
        if (copyDiv) {
            copyDiv.classList.remove("fa-regular", "fa-clone");
            copyDiv.classList.add("fa-solid", "fa-check");
        }
    }
</script>