<?php

include 'db.php';
session_start();

if (isset($_SESSION['prodID'])) {
    foreach ($_SESSION['prodID'] as $prodID) {
        $product .= mysqli_real_escape_string($conn, $prodID) . ',';
    }
    $product = rtrim($product, ',');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bonusAnswer'])) {
        $answer = mysqli_real_escape_string($conn, $_POST['bonusAnswer']);

        $query = "SELECT v.voucherCode
                  FROM voucher v 
                  LEFT JOIN category c on v.categoryID = c.categoryID
                  LEFT JOIN bonus_question bq ON bq.categoryID = c.categoryID
                  LEFT JOIN question_answer qa ON qa.bqID = bq.bqID
                  LEFT JOIN answer a ON a.answerID = qa.answerID
                  LEFT JOIN product_answer pa ON pa.answerID = a.answerID
                  WHERE pa.prodID = '$product' AND pa.answerID='$answer'";
        $result = mysqli_query($conn, $query);

        if ($result && $voucher = mysqli_fetch_assoc($result)['voucherCode']) {
            $_SESSION['bonusAnswered'] = true;
        } else {
            $_SESSION['bonusAnswered'] = false;
        }
    }

    header('Location: result.php');
    exit();
}
