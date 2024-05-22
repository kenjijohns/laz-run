<?php
include '../db.php';

if (isset($_GET['pqID'])) {
    $pqID = $_GET['pqID'];

    $categoryQuery  = "SELECT categoryID FROM parent_question WHERE pqID = $pqID";
    $categoryResult = mysqli_query($conn, $categoryQuery);
    $categoryRow    = mysqli_fetch_assoc($categoryResult);
    $categoryID     = $categoryRow['categoryID'];

    $productsQuery  = "SELECT prodID, prodName FROM product WHERE categoryID = $categoryID";
    $productsResult = mysqli_query($conn, $productsQuery);

    if ($productsResult) {
        while ($row = mysqli_fetch_assoc($productsResult)) {
            $prodID     = $row['prodID'];
            $prodName   = $row['prodName'];
            echo "<option value=\"$prodID\">$prodName</option>";
        }
    } else {
        echo "<option value=''>No products found</option>";
    }
} else {
    echo "<option value=''>Invalid request</option>";
}
?>
