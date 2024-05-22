<?php
include '../db.php';

if (isset($_GET['categoryID'])) {
    $categoryID = $_GET['categoryID'];

    $query = "SELECT * FROM product WHERE categoryID = '$categoryID'";
    $result = mysqli_query($conn, $query);

    $options = '';
    while ($row = mysqli_fetch_assoc($result)) {
        $prodID   = $row['prodID'];
        $prodName = $row['prodName'];
        $options .= "<option value=\"$prodID\">$prodName</option>";
    }

    echo $options;
} else {
    echo 'Invalid request';
}
?>
