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
        exit();
    }
?>
