<?php
    $pageTitle = "P&G Laz-Run Quiz";
    include 'demo_header.php';
    include '../db.php';

    $sql = "SELECT text FROM `texts` WHERE id = 'home-title'";
    $result = $conn->query($sql);

    // Check if a result was found
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['text'];    // Use $text variable containing the text
    }
?>
<div class="body-wrapper bg2">
    <div class="wrapper">
        <div class="spacer"></div>
        <div class="header-container">
            <h1 id="home-title"><?php echo $title; ?></h1>
        </div>
        <div class="spacer"></div>
        <div class="options-container">
            <div class="buttons">
                <button type="submit" name="category">
                    Category Name 1
                </button>
                <button type="submit" name="category">
                    Category Name 2
                </button>
                <button type="submit" name="category">
                    Category Name 3
                </button>
            </div>
        </div>
    </div>
</div>