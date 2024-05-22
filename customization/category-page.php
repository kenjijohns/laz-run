<?php
    $pageTitle = "P&G Laz-Run Quiz";
    session_start();
    include 'header.php';
    include 'db.php';
    $selectedCategory = isset($_SESSION['selectedCategory']) ? $_SESSION['selectedCategory'] : 4;
    
    $query = "SELECT * FROM category WHERE categoryID = '$selectedCategory'";
    $result = mysqli_query($conn, $query);
    while($row = mysqli_fetch_assoc($result)){
        $categoryDescription    = $row['categoryDescription'];
        $categoryName           = $row['categoryName'];
        $categoryTitle          = $row['categoryTitle'];
?>
<link rel="stylesheet" href="../assets/styles.css">
<div class="body-wrapper bg3">
    <div class="wrapper justify-center gap20">
        <div class="spacer"></div>
        <div class="header-container">
            <h2>READY FOR A</h2>
            <h1 class="quiz">QUIZ?</h1>
            <h3><?php echo $categoryTitle; ?></h3>
        </div>
        <div class="content-container">
            <h2><?php echo $categoryName; ?></h2>
            <p><?php echo $categoryDescription;?></p>
        </div>
        <button onclick="location.href='quiz.php'">CLICK TO START</button>
        <div class="spacer"></div>
    </div>
</div>

<?php
 }
?>