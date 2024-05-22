<?php
    session_start();

    if(isset($_POST['home'])){
        unset($_SESSION['selectedCategory']);
        unset($_SESSION['selectedAnswers']);
        unset($_SESSION['currentQuestion']);
        unset($_SESSION['productTally']);
        unset($_SESSION['prodID']);
        unset($_SESSION['finish_insertion_done']);
        unset($_SESSION['deviceType']);
        unset($_SESSION['bonusAnswered']);
        
        header("Location: index.php");
        exit();
    } elseif (isset($_POST['bonus'])){
        header("Location: bonus.php");
    } elseif (isset($_POST['outbound'])) {
        include 'db.php';
        $guestID = $_SESSION['guestID'];
        $prodID = $_POST['prodID']; 
    
        $query = "UPDATE session
                  SET outbound = '1'
                  WHERE prodID = '$prodID' AND guestID = '$guestID'
                  ORDER BY timestamp DESC 
                  LIMIT 1";

        $result = mysqli_query($conn, $query);
    
        if ($result) {
            header("Location: result.php");
        } else {
            echo "Error updating outbound status.";
        }
    }
?>