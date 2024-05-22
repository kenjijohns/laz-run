<?php
    $pageTitle = "P&G Laz-Run Quiz";
    include 'header.php';
    include 'db.php';
    session_start();

    $selectedCategory = isset($_SESSION['selectedCategory']) ? $_SESSION['selectedCategory'] : 4;
    
    $bonusQuery = "SELECT bq.bqID, bq.bqContent, bq.categoryID, a.answerID, a.answerContent
                FROM bonus_question bq
                LEFT JOIN question_answer qa ON qa.bqID = bq.bqID
                LEFT JOIN answer a ON a.answerID = qa.answerID
                WHERE bq.categoryID = '$selectedCategory'
                ORDER BY bq.bqID";

    $bonusResult = mysqli_query($conn, $bonusQuery);

    $bonusQuestions = array();

    while ($bonusRow = mysqli_fetch_assoc($bonusResult)) {
        $bqID = $bonusRow['bqID'];
        $bqContent = $bonusRow['bqContent'];
        $answerID = $bonusRow['answerID'];
        $answerContent = $bonusRow['answerContent'];

        if (!isset($bonusQuestions[$bqID])) {
            $bonusQuestions[$bqID] = array(
                'content' => $bqContent,
                'answers' => array(),
            );
        }

        if ($answerID !== null) {
            $bonusQuestions[$bqID]['answers'][] = array(
                'id' => $answerID,
                'content' => $answerContent,
            );
        }
    }
?>
<link rel="stylesheet" href="../assets/styles.css">
<div class="no-animate">
            <div class="progress-bar">
                <div class="progress" style="width: <?php echo (($_SESSION['currentQuestion'] - 1) / $totalQuestions) * 100; ?>%"></div>
            </div>
        </div>
<div class="body-wrapper bg4">
    <div class="wrapper">
        <form method="post" action="process_bonus.php">
            <?php if (!empty($bonusQuestions)): ?>
            <div class="spacer"></div>
            <?php foreach ($bonusQuestions as $bonusQuestion): ?>
            <div class="q-container">
                <div class="question-mark">
                    <img src="./assets/icon-questionmark.svg" alt="?">
                </div>
                <div class="question-box">
                    <h3><?php echo $bonusQuestion['content']; ?></h3>
                </div>
            </div>
            <?php if (!empty($bonusQuestion['answers'])): ?>
            <div class="spacer"></div>
            <div class="spacer display-none "></div>
            <div class="options-container">
                <?php foreach ($bonusQuestion['answers'] as $answer): ?>
                    <button type="button" class="bonus-answer-btn" data-answer-id="<?php echo $answer['id']; ?>" onclick="selectBonusAnswer(<?php echo $answer['id']; ?>)">
                        <?php echo $answer['content']; ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <p>No answers found for this bonus question.</p>
            <?php endif; ?>
            <?php endforeach; ?>
            <input type="hidden" name="bonusAnswer" id="selectedBonusAnswer" value="">
            <div class="spacer"></div>
            <button type="submit" class="next">SUBMIT</button>
            <div class="spacer"></div>
            <?php else: ?>
                <p>No bonus questions found for this category.</p>
            <?php endif; ?>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    var selectedBonusAnswer = '';

    function selectBonusAnswer(answerID) {
        selectedBonusAnswer = answerID;
        $('.bonus-answer-btn').removeClass('selected');
        $('[data-answer-id="' + answerID + '"]').addClass('selected');
    }

    $(document).ready(function() {
        $('form').on('submit', function() {
            $('#selectedBonusAnswer').val(selectedBonusAnswer);
        });
    });
</script>
