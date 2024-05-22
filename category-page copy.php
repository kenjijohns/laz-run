<?php
    $pageTitle = "P&G Laz-Run Quiz";
    session_start();
    if (!isset($_SESSION['selectedCategory'])) {
        header('Location: index.php');
        exit();
    }
    include 'header.php';
    include 'db.php';
    $selectedCategory = $_SESSION['selectedCategory'];

    // Check if user dropped off the quiz
    if (isset($_SESSION['quizProgress'])) {
        if (!isset($_SESSION['drop_insertion_done'])) {
            $guestID = $_SESSION['guestID'];
            $device  = $_SESSION['deviceType'];
            $country = $_SESSION['country'];
            $source  = $_SESSION['referrer'];
            $lastID  = $_SESSION['last_session_id'];
    
            $query  = "SELECT * FROM session WHERE sessionID = '$lastID'";
            $result = mysqli_query($conn, $query);
    
            if(mysqli_num_rows($result) > 0){
                $query = "UPDATE session
                        SET status = '1'
                        WHERE sessionID = '$lastID' AND guestID = '$guestID'";

                $result = mysqli_query($conn, $query);

                if ($result) {
                    $_SESSION['drop_insertion_done'] = true;
                }
            } else {
                $query = "INSERT INTO session(guestID, device_type, source, status, locationFrom) VALUES ('$guestID', '$device',  '$source', '1', '$country')";
                $result = mysqli_query($conn, $query);
    
                if ($result) {
                    if(isset($_SESSION['last_session_id'])){
                        unset($_SESSION['last_session_id']);
                        $_SESSION['last_session_id'] = mysqli_insert_id($conn);
                    }
                    $_SESSION['drop_insertion_done'] = true;
                }
            }
        }
        unset($_SESSION['quizProgress']);
        unset($_SESSION['selectedAnswers']);
        unset($_SESSION['currentQuestion']);
        unset($_SESSION['productTally']);
    }
    
    $query = "SELECT * FROM category WHERE categoryID = '$selectedCategory'";
    $result = mysqli_query($conn, $query);
    while($row = mysqli_fetch_assoc($result)){
        $categoryDescription    = $row['categoryDescription'];
        $categoryName           = $row['categoryName'];
        $categoryTitle          = $row['categoryTitle'];
?>

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