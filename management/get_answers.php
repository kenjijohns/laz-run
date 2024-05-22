<?php
include '../db.php';

if (isset($_GET['pqID'])) {
    $pqID = $_GET['pqID'];

    $answersQuery = "SELECT * 
                     FROM answer a
                     LEFT JOIN question_answer qa ON qa.answerID = a.answerID
                     WHERE qa.pqID = $pqID";
    $answersResult = mysqli_query($conn, $answersQuery);

    $options = '<option value="">Select answer</option>';
    while ($answerRow = mysqli_fetch_assoc($answersResult)) {
        $answerID = $answerRow['answerID'];
        $answerContent = $answerRow['answerContent'];
        $options .= "<option value=\"$answerID\">$answerContent</option>";
    }

    echo $options;
}
?>
