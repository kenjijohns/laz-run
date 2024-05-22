<?php
    $pageTitle = "Quiz";
    include 'header.php';
    include 'db.php';


    session_start();

    $_SESSION['quizProgress'] = true;

    $selectedCategory = $_SESSION['selectedCategory'];

    
    // Initialize session variables if not set
    if (!isset($_SESSION['currentQuestion'])) {
        $_SESSION['currentQuestion'] = 1;
    }

    if (!isset($_SESSION['selectedAnswers'])) {
        $_SESSION['selectedAnswers'] = array();
    }

    // Check if a POST request is made
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selectedAnswer = $_POST['selectedAnswer'];
        
        $_SESSION['selectedAnswers'][$_SESSION['currentQuestion']] = $selectedAnswer;

        $_SESSION['currentQuestion']++;

        // Retrieve the conditional question if applicable
        $conditionalQuery = "SELECT cq.cqID, cq.cqContent, cq.cqMaxAnswer
                            FROM conditional_question cq
                            LEFT JOIN trigger_condition tc ON cq.cqID = tc.cqID
                            WHERE tc.answerID = '$selectedAnswer'";
        $conditionalResult = mysqli_query($conn, $conditionalQuery);

        if ($conditionalResult && mysqli_num_rows($conditionalResult) > 0) {
            $conditionalRow = mysqli_fetch_assoc($conditionalResult);
            $cqID           = $conditionalRow['cqID'];
            $cqContent      = $conditionalRow['cqContent'];
            $cqMaxAnswer    = $conditionalRow['cqMaxAnswer'];
            ?>
     
            <!-- Display the conditional question -->
            <div class="body-wrapper bg4">
                <div class="wrapper show-wrapper">
                    <form action="quiz.php" method="post" id="quizForm">
                        <div class="spacer"></div>
                        <div class="q-container">
                            <div class="question-mark">
                                <img src="./assets/icon-questionmark.svg">  
                            </div>
                            <div class="question-box">
                                <h3><?php echo $cqContent; ?></h3>
                            </div>
                        </div>
                        <div class="spacer"></div>
                        <div class="spacer display-none"></div>
                        <div class="options-container">
                            <?php
                            $conditionalAnswerQuery = "SELECT a.answerID, a.answerContent
                                                        FROM answer a
                                                        LEFT JOIN question_answer qa ON qa.answerID = a.answerID
                                                        WHERE qa.cqID = '$cqID'";
                            $conditionalAnswerResult = mysqli_query($conn, $conditionalAnswerQuery);

                            while ($conditionalAnswerRow = mysqli_fetch_assoc($conditionalAnswerResult)) {
                                $conditionalAnswerID = $conditionalAnswerRow['answerID'];
                                $conditionalAnswerContent = $conditionalAnswerRow['answerContent'];
                                echo "<button type='button' class='conditional-answer-btn' data-answer-id='$conditionalAnswerID' onclick='selectAnswer($conditionalAnswerID)'>$conditionalAnswerContent</button>";
                            }
                            ?>
                    </div>
                        <div class="spacer"></div>
                        <button type="submit" class="next" id="nextButton" disabled="disabled">Next</button>
                        <input type="hidden" name="selectedAnswer" id="selectedAnswer" value="">
                        <div class="spacer"></div>
                    </form>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
            <script>
                var selectedAnswer = '';

                $(document).ready(function() {
                    $(document).on('click', '.conditional-answer-btn', function() {
                        selectedAnswer = $(this).data('answer-id');
                        $('.conditional-answer-btn').removeClass('selected');
                        $(this).addClass('selected');
                        $('#nextButton').prop('disabled', false);
                    });

                    $('#nextButton').on('click', function() {
                        $('#selectedAnswer').val(selectedAnswer);
                        $('#quizForm').submit();
                    });
                });
            </script>

            <?php
            exit; 
        }
    }

    // Retrieve the parent question
    $query = "SELECT pqID, pqContent, pqMaxAnswer
            FROM parent_question 
            WHERE pqOrder = {$_SESSION['currentQuestion']} AND categoryID = '$selectedCategory'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row           = mysqli_fetch_assoc($result);
        $pqID          = $row['pqID'];
        $pqContent     = $row['pqContent'];
        $pqMaxAnswer   = $row['pqMaxAnswer'];

        // Retrieve answers for the parent question
        $answerQuery = "SELECT a.answerID, a.answerContent
                        FROM answer a
                        LEFT JOIN question_answer qa ON qa.answerID = a.answerID
                        WHERE qa.pqID = '$pqID' ";
        $answerResult = mysqli_query($conn, $answerQuery);
        ?>
    
        <!-- Display the parent question -->
        <link rel="stylesheet" href="../assets/styles.css">
        <div class="no-animate">
            <div class="progress-bar">
                <div class="progress" style="width: <?php echo (($_SESSION['currentQuestion'] - 1) / $totalQuestions) * 100; ?>%"></div>
            </div>
        </div>
        <div class="body-wrapper bg4">
            <div class="wrapper">
                <form action="quiz.php" method="post" id="quizForm">
                    <div class="spacer"></div>
                    <div class="q-container">
                        <div class="question-mark">
                            <img src="./assets/icon-questionmark.svg">
                        </div>
                        <div class="question-box">
                            <h3><?php echo $pqContent; ?></h3>
                        </div>
                    </div>
                    <div class="spacer"></div>
                    <div class="spacer display-none"></div>
                    <div class="options-container">
                        <?php
                        while ($answerRow = mysqli_fetch_assoc($answerResult)) {
                            $answerID = $answerRow['answerID'];
                            $answerContent = $answerRow['answerContent'];
                            echo "<button type='button' class='parent-answer-btn' data-answer-id='$answerID' onclick='selectAnswer($answerID)'>$answerContent</button>";
                        }
                        ?>
                    <div class="spacer"></div>
                    <button type="submit" class="next" id="nextButton" disabled="disabled">NEXT</button>
                    <input type="hidden" name="selectedAnswer" id="selectedAnswer" value="">
                    </div>
                </form>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script>
            var selectedAnswer = '';

            $(document).ready(function() {
                $(document).on('click', '.parent-answer-btn', function() {
                    selectedAnswer = $(this).data('answer-id');
                    $('.parent-answer-btn').removeClass('selected');
                    $(this).addClass('selected');
                    $('#nextButton').prop('disabled', false);
                });

                $('#nextButton').on('click', function() {
                    $('#selectedAnswer').val(selectedAnswer);
                    $('#quizForm').submit();
                });
            });
        </script>

        <?php
    } else {
        // Tally up selected answers
        $selectedAnswers = $_SESSION['selectedAnswers'];
        $productTally = array();
        
        $selectedAnswers = $_SESSION['selectedAnswers'];
        $productTally = array();

        foreach ($selectedAnswers as $answerID) {
            $productQuery = "SELECT p.prodID, p.prodName
                            FROM product p
                            INNER JOIN product_answer pa ON p.prodID = pa.prodID
                            WHERE pa.answerID = '$answerID'";

            $productResult = mysqli_query($conn, $productQuery);

            while ($productRow = mysqli_fetch_assoc($productResult)) {
                $prodID   = $productRow['prodID'];
                $prodName = $productRow['prodName'];

                if (!isset($productTally[$prodID])) {
                    $productTally[$prodID] = array(
                        'id'    => $prodID,
                        'name'  => $prodName,
                        'tally' => 1,
                    );
                } else {
                    $productTally[$prodID]['tally']++;
                }
            }
        }

        $_SESSION['productTally'] = $productTally;

        header("Location: result.php");
        exit;
    }
?>


<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    var selectedAnswer = '';

    $(document).ready(function() {
        $(document).on('click', '.parent-answer-btn', function() {
            selectedAnswer = $(this).data('answer-id');
            $('.parent-answer-btn').removeClass('selected');
            $(this).addClass('selected');
            $('#nextButton').prop('disabled', false);
        });

        $('#nextButton').on('click', function() {
            $('#selectedAnswer').val(selectedAnswer);
            $('#quizForm').submit();
        });
    });
</script>
