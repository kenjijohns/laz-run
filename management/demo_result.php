<?php
    $pageTitle = "P&G Laz-Run Quiz";
    include 'demo_header.php';

    $sql = "SELECT text FROM `texts` WHERE id = 'text-3'";
    $result = $conn->query($sql);
    
    // Check if a result was found
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $text_3 = $row['text'];    // Use $text variable containing the text
    }

    $sql = "SELECT text FROM `texts` WHERE id = 'text-4'";
    $result = $conn->query($sql);
    
    // Check if a result was found
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $text_4 = $row['text'];    // Use $text variable containing the text
    }
    
?>

<div class="body-wrapper bg5">
    <div class="wrapper justify-center gap20">
        <div class="result-title">
            <h3 id = "text-3" class = "text-3"><?php echo $text_3; ?></h3>
            <p id="text-4" class="text-4"><?php echo $text_4; ?></p>
        </div>
        <div class="suggested-products masked-overflow">
            <div class="product-container">
                <div class="product-body">
                    <img src="uploads/product.jpg" class="suggested-image" class="img-fluid">
                    <p>Product Name</p>
                    <button name="outbound" class="view-product-button">VIEW PRODUCT</button>
                </div>
            </div>
        </div>
        <div class="nav-buttons">
            <button type="submit" name="home">BACK TO HOME</button>
        </div>
    </div>
</div>
