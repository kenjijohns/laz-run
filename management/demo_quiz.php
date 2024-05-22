<?php
    $pageTitle = "Quiz";
    include 'demo_header.php';
    include '../db.php';
?>

<!-- Display the parent question -->
<div class="body-wrapper bg4">
    <div class="wrapper">
        <div class="progress-bar">
            <div class="progress" style="width: 5%"></div>
        </div>
        <form action="quiz.php" method="post" id="quizForm">
            <div class="spacer"></div>
            <div class="q-container">
                <div class="question-mark">
                    <img src="../assets/icon-questionmark.svg">
                </div>
                <div class="question-box">
                    <h3 id="quiz-title">Question Content. Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta facilis velit ducimus quae obcaecati iste quo totam?</h3>
                </div>
            </div>
            <div class="spacer"></div>
            <div class="spacer display-none"></div>
            <div class="options-container">
                <button type="button" class="parent-answer-btn" data-answer-id="$answerID" >Answer Option 1</button>
                <button type="button" class="parent-answer-btn" data-answer-id="$answerID" >Answer Option 2</button>
                <button type="button" class="parent-answer-btn" data-answer-id="$answerID" >Answer Option 3</button>
                <button type="button" class="parent-answer-btn" data-answer-id="$answerID" >Answer Option 4</button>
                <div class="spacer"></div>
                <button type="submit" class="next" id="nextButton" disabled="disabled">NEXT</button>
                <input type="hidden" name="selectedAnswer" id="selectedAnswer" value="">
            </div>
        </form>
    </div>
</div>