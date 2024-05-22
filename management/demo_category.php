<?php
    $pageTitle = "P&G Laz-Run Quiz";
    include 'demo_header.php';
    include '../db.php';

    $query = "SELECT * FROM category WHERE categoryID = '4'";
    $result = mysqli_query($conn, $query);
    while($row = mysqli_fetch_assoc($result)){
        $categoryDescription    = $row['categoryDescription'];
        $categoryName           = $row['categoryName'];
        $categoryTitle          = $row['categoryTitle'];

    $sql = "SELECT text FROM `texts` WHERE id = 'text-1'";
    $result = $conn->query($sql);
    
    // Check if a result was found
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $text_1 = $row['text'];    // Use $text variable containing the text
    }

    $sql = "SELECT text FROM `texts` WHERE id = 'text-2'";
    $result = $conn->query($sql);
    
    // Check if a result was found
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $text_2 = $row['text'];    // Use $text variable containing the text
    }
    
?>

<div class="body-wrapper bg3">
    <div class="wrapper justify-center gap20">
        <div class="spacer"></div>
        <div class="header-container">
            <h2 class = "text-1" id = "text-1"><?php echo $text_1; ?></h2>
            <h1 class = "text-2" id = "text-2"><?php echo $text_2; ?></h1>
            <h3 class="category-title">Category Title</h3>
        </div>
        <div class="content-container">
            <h2 class ="category-name">Category Name</h2>
            <p>Category Description. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque ut auctor lorem. Aliquam ut nisl non mi gravida aliquam. Cras gravida lorem a felis tristique, nec congue felis bibendum. </p>
        </div>
        <button>CLICK TO START</button>
        <div class="spacer"></div>
    </div>
</div>

<?php
 }
?>