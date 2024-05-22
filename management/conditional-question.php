<?php
    $pageTitle = "Question Page";
    include 'header.php';
    include 'navbar.php';
    include '../db.php';
    
    if (isset($_SESSION['username'])) {
        $username    = $_SESSION['username'];
        $userQuery   = "SELECT * FROM user WHERE username = '$username'";
        $userResult  = mysqli_query($conn, $userQuery);
    
        while ($userRow = mysqli_fetch_assoc($userResult)) {
            $userID    = $userRow['userID'];

            $_SESSION['userID'] = $userID;
        }
    } else {
        header('Location: index.php');
        exit();
    }

    $recordsPerPage = 10;
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $recordsPerPage;
    $categoryFilter = isset($_GET['category']) ? $_GET['category'] : 0;

    $query = "SELECT cq.*, pq.*, c.categoryName, 
                GROUP_CONCAT(a.answerContent) as answerContents, 
                GROUP_CONCAT(p.prodName) as prodNames
                FROM conditional_question cq
                LEFT JOIN question_answer qa ON qa.cqID = cq.cqID 
                LEFT JOIN parent_question pq ON qa.pqID = pq.pqID
                LEFT JOIN category c ON pq.categoryID = c.categoryID
                LEFT JOIN answer a ON a.answerID = qa.answerID
                LEFT JOIN product_answer pa ON pa.answerID = a.answerID
                LEFT JOIN product p ON pa.prodID = p.prodID";
    if ($categoryFilter > 0) {
        $query .= " WHERE c.categoryID = $categoryFilter";
    }

    $query .= " GROUP BY cq.cqID DESC
        LIMIT $offset, $recordsPerPage";

    $result = mysqli_query($conn, $query);
?>

<div class="container w-75">
    <?php include 'question_nav.php';?>
    <form class="form-inline d-inline">
        <select class="custom-select mr-3" name="category" id="category" onchange="this.form.submit()">
            <option value="0" <?php echo ($categoryFilter == 0) ? 'selected' : ''; ?>>All Categories</option>
            <?php
            $categoriesQuery = "SELECT * FROM category";
            $categoriesResult = mysqli_query($conn, $categoriesQuery);

            while ($categoryRow = mysqli_fetch_assoc($categoriesResult)) {
                $categoryId = $categoryRow['categoryID'];
                $categoryName = $categoryRow['categoryName'];
                echo '<option value="' . $categoryId . '" ' . ($categoryFilter == $categoryId ? 'selected' : '') . '>' . $categoryName . '</option>';
            }
            ?>
        </select>
    </form>
    <button class="btn btn-dark float-end mb-2" data-bs-toggle="modal" data-bs-target="#addConditionalQuestionModal">Add Conditional Question</button>
</div>
<div class="container w-75">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="text-center">
                <tr>
                    <th>Category Name</th>
                    <th>Question Content</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    while ($row = mysqli_fetch_assoc($result)){
                        $cqID           = $row['cqID'];
                        $cqContent      = $row['cqContent'];
                        $cqNumOptions   = $row['cqNumOptions'];
                        $cqMaxAnswer    = $row['cqMaxAnswer'];
                        $categoryName   = $row['categoryName'];
                        $prodNames      = $row['prodNames'];
                        $answerContents = $row['answerContents'];
                ?>
                <tr>
                    <td><?php echo $categoryName;?></td>
                    <td><?php echo $cqContent;?></td>
                    <td>
                    <div class="d-flex justify-content-center align-items-center">
                            <!-- View Button -->
                            <div class="text-center me-1">
                                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#viewQuestionModal<?php echo $pqID; ?>">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-eye"></i>
                                    </div>
                                </button>
                            </div>
                            <!-- Edit Button -->
                            <div class="text-center">
                                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#editQuestionModal<?php echo $pqID; ?>">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </div>
                                </button>
                            </div>
                        </div>
                    <?php
                        }
                    ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="container w-75">
    <ul class="pagination justify-content-center">
        <?php
        $totalRecordsQuery = "SELECT COUNT(*) AS totalRecords FROM conditional_question";
        if ($categoryFilter > 0) {
            $totalRecordsQuery .= " WHERE categoryID = $categoryFilter";
        }
        
        $totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
        $totalRecordsRow = mysqli_fetch_assoc($totalRecordsResult);
        $totalRecords = $totalRecordsRow['totalRecords'];
        $totalPages = ceil($totalRecords / $recordsPerPage);

        if ($totalPages > 1) {
            for ($i = 1; $i <= $totalPages; $i++) {
                echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
                echo '<a class="page-link" href="?page=' . $i . '&category=' . $categoryFilter . '">' . $i . '</a>';
                echo '</li>';
            }
        }
        ?>
    </ul>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addConditionalQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-center">Add Conditional Question Form</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form -->
                <form action="functions.php" method="post" id="questionForm">
                <div class="main-form">
                    <div class="form-group my-2">
                        <div class="form-floating">
                            <select name="mainQuestion" id="mainForm_mainQuestion" class="form-control w-100" onchange="toggleInputs('mainForm')">
                                <option value="">Select main question</option>
                                <?php
                                    $pqQuery = "SELECT * FROM parent_question";
                                    $pqResult = mysqli_query($conn, $pqQuery);
                                    
                                    while($row = mysqli_fetch_assoc($pqResult)){
                                        $pqID       = $row['pqID'];
                                        $pqContent  = $row['pqContent'];
                                        
                                        echo "<option value=\"$pqID\">$pqContent</option>";
                                    }
                                    ?>
                            </select>
                            <label for="mainForm_mainQuestion">Choose Main Question</label>
                        </div>
                    </div>
                    <div class="form-group my-2">
                        <div class="form-floating">
                            <select name="mainQuestionAnswer" id="mainForm_mainQuestionAnswer" class="form-control w-100" required disabled>
                                <option value="">Select answer</option>
                            </select>
                            <label for="mainForm_mainQuestionAnswer">Choose Answer</label>
                        </div>
                    </div>
                    <div class="form-group my-2">
                        <div class="form-floating">
                            <textarea type="text" style="resize: none; height: 100px;" class="form-control" id="mainForm_conditionalQuestion" name="conditionalQuestion" placeholder="Enter question here" required disabled></textarea> 
                            <label for="mainForm_conditionalQuestion">Question:</label>
                        </div>
                    </div>
                    <div class="form-group my-2">
                        <div class="row">
                            <div class="col">
                                <div class="form-floating">
                                    <select name="cqNumOptions" id="mainForm_numOptions" class="form-control w-100" onchange="updateAnswerInputs('mainForm')" required disabled>
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                    <label for="mainForm_numOptions">Number of Options</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select name="cqNumAnswer" id="mainForm_numAnswer" class="form-control w-100" required disabled>
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                    <label for="mainForm_numAnswer">Maximum Number of Answers</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="mainForm_answerInputsContainer">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button name="addConditionalQuestion" class="btn btn-success" type="submit" id="submitAdd" disabled>Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@2.0.1/dist/js/multi-select-tag.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script>
    function toggleInputs(formPrefix) {
        var mainQuestionSelect   = document.getElementById(formPrefix + '_mainQuestion');
        var mainQuestionAnswer   = document.getElementById(formPrefix + '_mainQuestionAnswer');
        var questionInput        = document.getElementById(formPrefix + '_conditionalQuestion'); 
        var answersSelect        = document.getElementById(formPrefix + '_numOptions');
        var numAnswerSelect      = document.getElementById(formPrefix + '_numAnswer');
        var submitButton         = document.getElementById('submitAdd');

        if (mainQuestionSelect.value == 0) {
            mainQuestionAnswer.disabled     = true;
            questionInput.disabled          = true;
            answersSelect.disabled          = true;
            numAnswerSelect.disabled        = true;
            submitButton.disabled           = true;
        } else {
            mainQuestionAnswer.disabled     = false;
            questionInput.disabled          = false;
            answersSelect.disabled          = false;
            numAnswerSelect.disabled        = false;
            submitButton.disabled           = false;
        }
    }
    function updateAnswerInputs(formPrefix) {
        const numOptions = document.getElementById(formPrefix + '_numOptions').value;
        const answerInputsContainer = document.getElementById(formPrefix + '_answerInputsContainer');
        
        const pqID = document.getElementById(formPrefix + '_mainQuestion').value;

        $.ajax({
            url: 'fetch_products.php',
            type: 'GET',
            data: { pqID: pqID }, 
            success: function (data) {
                let inputs = '';
                for (let i = 0; i < numOptions; i++) {
                    const uniqueId = formPrefix + '_productAnswers' + i;
                    inputs += `
                        <div class="row mb-3">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" name="answer[]" id="${formPrefix}_answerOption${i + 1}" placeholder="Enter answer option ${i + 1} here." required class="form-control">
                                    <label for="${formPrefix}_answerOption${i + 1}">Answer Option ${i + 1}</label>
                                </div>
                            </div>
                            <div class="col">
                                <label for="answerProduct" class="text-muted">Product/s</label>
                                <select id="${uniqueId}" name="answer_type[${i}][]" class="form-control ${formPrefix}_productAnswers" multiple>
                                    ${data} 
                                </select>
                            </div>
                        </div>`;
                }
                answerInputsContainer.innerHTML = inputs;

                $('.' + formPrefix + '_productAnswers').each(function () {
                    new MultiSelectTag(this.id);
                });

                toggleInputs(formPrefix);
            },
            error: function () {
                console.error('Error fetching answer types');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        var mainQuestionSelect = document.getElementById('mainForm_mainQuestion');
        var mainQuestionAnswer = document.getElementById('mainForm_mainQuestionAnswer');

        if (mainQuestionSelect && mainQuestionAnswer) {
            mainQuestionSelect.addEventListener('change', function() {
                var selectedPQID = mainQuestionSelect.value;

                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        mainQuestionAnswer.innerHTML = xhr.responseText;
                    }
                };

                xhr.open('GET', 'get_answers.php?pqID=' + selectedPQID, true);
                xhr.send();
            });
        }
    });
</script>