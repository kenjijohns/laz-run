<?php
    require 'class/class_quiz.php';
    
    $classQuiz = new Quiz;
    session_start();
    if (isset($_POST['login'])){
        // Login
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $result = $classQuiz->login($username, $password);

            if ($result){
                $_SESSION['user_role'] = $result;
                $_SESSION['user_authenticated'] = true;
                $_SESSION['username'] = $username;

                header("Location: index.php");
                exit();
            } else{
                echo "<script>alert('Invalid login details');window.location.href='login.php';</script>";
            }
        } 
    } // Main Question CRUD 
     elseif (isset($_POST['addMainQuestion'])) {
        $parentQuestion = $_POST['parentQuestion'];
        $numOptions     = $_POST['numOptions'];
        $numAnswer      = $_POST['numAnswer'];
        $categoryID     = $_POST['category'];
    
        $answersData = array();
        for ($i = 0; $i < $numOptions; $i++) {
            $answerContent  = $_POST['answer'][$i];
            $productIDs     = $_POST['answer_type'][$i];
            $answersData[$answerContent] = $productIDs;
        }

        $result = $classQuiz->addMainQuestion($parentQuestion, $numOptions, $numAnswer, $categoryID, $answersData);
    
        if ($result){
            header("Location: question.php");
        } else{
            echo "Failed to add question.";
        }
    } elseif (isset($_POST['updateMainQuestion'])){
        $parentQuestion = $_POST['pqID'];
        $numOptions     = $_POST['numOptions'];
        $numAnswer      = $_POST['numAnswer'];
        $category       = $_POST['category'];

        $answersData = array();
        for ($i = 0; $i < $numOptions; $i++) {
            $answerContent  = $_POST['answer'][$i];
            $productIDs     = $_POST['answer_type'][$i];
            $answersData[$answerContent] = $productIDs;
        }

        $result = $classQuiz->updateMainQuestion($parentQuestion, $numOptions, $numAnswer, $categoryID, $answersData);

        if($result){
            header("Location: question.php");
        } else{
            echo "Failed to update question.";
        }
    } // Conditional Question CRUD
     elseif (isset($_POST['addConditionalQuestion'])) {
        $mainQuestion           = $_POST['mainQuestion'];
        $mainQuestionAnswer     = $_POST['mainQuestionAnswer'];
        $conditionalQuestion    = $_POST['conditionalQuestion'];
        $cqNumOptions           = $_POST['cqNumOptions'];
        $cqNumAnswer            = $_POST['cqNumAnswer'];
        
        $answersData = array();
        for ($i = 0; $i < $cqNumOptions; $i++) {
            $answerContent  = $_POST['answer'][$i];
            $productIDs     = $_POST['answer_type'][$i];
            $answersData[$answerContent] = $productIDs;
        }

        $result = $classQuiz->addConditionalQuestion($mainQuestion, $mainQuestionAnswer, $conditionalQuestion, $cqNumOptions, $cqNumAnswer, $answersData);

        if ($result){
            header("Location: conditional-question.php");
        } else {
            echo "Failed to add question.";
        }
    } // Voucher Question CRUD 
     elseif (isset($_POST['addVoucherQuestion'])) {
        $voucherQuestion = $_POST['voucherQuestion'];
        $numOptions      = $_POST['numOptions'];
        $numAnswer       = $_POST['numAnswer'];
        $categoryID      = $_POST['category'];
    
        $answersData = array();
        for ($i = 0; $i < $numOptions; $i++) {
            $answerContent = $_POST['answer'][$i];
            $productIDs = $_POST['answer_type'][$i];
            $answersData[$answerContent] = $productIDs;
        }

        $result = $classQuiz->addVoucherQuestion($voucherQuestion, $numOptions, $numAnswer, $categoryID, $answersData);
    
        if ($result){
            header("Location: voucher-question.php");
        } else{
            echo "Failed to add question.";
        }
    } // Category CRUD 
     elseif(isset($_POST['addCategory'])){
        $categoryName        = $_POST['categoryName'];
        $categoryTitle       = $_POST['categoryTitle'];
        $categoryDescription = $_POST['categoryDescription'];

        $result = $classQuiz->addCategory($categoryName, $categoryTitle, $categoryDescription);

        if($result){
            header("Location: categories.php");
        } else{
            $result->error;
        }
    } elseif (isset($_POST['updateCategory'])){
        $categoryID          = $_POST['categoryID'];
        $categoryName        = $_POST['categoryName'];
        $categoryTitle       = $_POST['categoryTitle'];
        $categoryDescription = $_POST['categoryDescription'];

        $result = $classQuiz->updateCategory($categoryID, $categoryName, $categoryTitle, $categoryDescription);

        if($result){
            header("Location: categories.php");
        } else{
            $result->error;
        }
    } // Voucher CRUD
     elseif (isset($_POST['addVoucher'])){
      $voucherCode = $_POST['voucherCode'];
      $categoryID  = $_POST['voucherCategory'];
      
      $result = $classQuiz->addVoucher($voucherCode, $categoryID);

      if($result){
        header("Location: voucher.php");
      } else{
        $result->error;
      }
    } elseif (isset($_POST['updateVoucher'])){
        $voucherID   = $_POST['voucherID'];
        $voucherCode = $_POST['voucherCode'];
        $categoryID  = $_POST['voucherCategory'];

        $result = $classQuiz->updateVoucher($voucherID, $voucherCode, $categoryID);

        if($result){
            header("Location: voucher.php");
        } else{
            $result->error;
        }
    } // Product CRUD 
     elseif (isset($_POST['addProduct'])) {
        $prodName           = $_POST['prodName'];
        $prodDescription    = $_POST['prodDescription'];
        $prodURL            = $_POST['prodURL'];
        $prodCategory       = $_POST['prodCategory'];
    
        $targetDirectory = "uploads/";
        $targetFile = $targetDirectory . basename($_FILES["prodImage"]["name"]);
        $uploadOk = 1;

        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["prodImage"]["tmp_name"]);
        if ($check === false) {
            echo "<script>alert('File is not an image.');window.location.href='product.php';</script>";
            $uploadOk = 0;
        }
        if ($_FILES["prodImage"]["size"] > 500000) {
            echo "<script>alert('Sorry, your file is too large.');window.location.href='product.php';</script>";
            $uploadOk = 0;
        }
        $allowedFormats = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowedFormats)) {
            echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');window.location.href='product.php';</script>";
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            echo "<script>alert('Sorry, your file was not uploaded.');window.location.href='product.php';</script>";
        } else {
            if (move_uploaded_file($_FILES["prodImage"]["tmp_name"], $targetFile)) {
                // echo "<script>alert('The file " . htmlspecialchars(basename($_FILES["prodImage"]["name"])) . " has been uploaded.');window.location.href='product.php';</script>";
            } else {
                echo "<script>alert('Sorry, there was an error uploading your file..');window.location.href='product.php';</script>";
            }
        }
    
        $result = $classQuiz->addProduct($prodName, $prodDescription, $targetFile, $prodURL, $prodCategory);
    
        if ($result) {
            header("Location: product.php");
        } else {
            $result->error;
        }
    } elseif(isset($_POST['updateProduct'])){
        $prodID            = $_POST['prodID'];
        $prodName          = $_POST['prodName'];
        $prodDescription   = $_POST['prodDescription'];
        $prodURL           = $_POST['prodURL'];
    
        if(isset($_FILES["prodImage"]) && $_FILES["prodImage"]["error"] == 0) {
            $targetDirectory = "uploads/";
            $targetFile = $targetDirectory . basename($_FILES["prodImage"]["name"]);
            $uploadOk = 1;
    
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["prodImage"]["tmp_name"]);
            if ($uploadOk == 0) {
                echo "<script>alert('Sorry, your file was not uploaded.');window.location.href='product.php';</script>";
            } else {
                if (move_uploaded_file($_FILES["prodImage"]["tmp_name"], $targetFile)) {
                    echo "<script>alert('The file " . htmlspecialchars(basename($_FILES["prodImage"]["name"])) . " has been uploaded.');window.location.href='product.php';</script>";
                } else {
                    echo "<script>alert('Sorry, there was an error uploading your file.');window.location.href='product.php';</script>";
                }
            }
            $result = $classQuiz->updateProductWithImage($prodID, $prodName, $prodDescription, $targetFile, $prodURL);
        } else {
            $result = $classQuiz->updateProductWithoutImage($prodID, $prodName, $prodDescription, $prodURL);
        }
    
        if ($result) {
            header("Location: product.php");
        } else {
            echo "Failed to update product.";
        }
    } 
    