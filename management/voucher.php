<?php
    $pageTitle = "Voucher Page";
    include 'header.php';
    include 'navbar.php';
    if (isset($_SESSION['username'])) {
        $username   = $_SESSION['username'];
        $userQuery  = "SELECT * FROM user WHERE username = '$username'";
        $userResult = mysqli_query($conn, $userQuery);
    
        while ($userRow = mysqli_fetch_assoc($userResult)) {
            $userID    = $userRow['userID'];
    
            $_SESSION['userID'] = $userID;
        }
    } else {
        header('Location: index.php');
        exit();
    }
?>

<div class="container w-75 my-3">
    <h1 class="text-center fw-bold mt-4">Voucher Management</h1>
    <hr style="height:1px;border-width:0;color:gray;background-color:gray">
    <button class="btn btn-dark float-end mb-2  me-5" data-bs-toggle="modal" data-bs-target="#addVoucherModal">Add Voucher</button>

</div>
<div class="container w-100">
    <div class="table table-borderless ">
        <table class="table text-center table-hover">
            <thead>
                <tr>
                    <th scope="col">Voucher Code</th>
                    <th scope="col">Category</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $query = "SELECT v.*, c.categoryName
                              FROM voucher v
                              LEFT JOIN category c ON v.categoryID = c.categoryID";
                    $result = mysqli_query($conn, $query);
                    
                    while($row = mysqli_fetch_assoc($result)){
                        $voucherID             = $row['voucherID'];
                        $voucherCode           = $row['voucherCode'];
                        $categoryName          = $row['categoryName'];
                    ?>
                    <tr>
                        <td><?php echo $voucherCode?></td>
                        <td><?php echo $categoryName?></td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                <!-- Edit Button -->
                                <div class="text-center">
                                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#editVoucherModal<?php echo $voucherID; ?>">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </div>
                                </button>
                            </div>
                            </div>

                            <!-- Update Voucher -->
                            <div class="modal fade" id="editVoucherModal<?php echo $voucherID; ?>" tabindex="-1" aria-labelledby="editVoucherModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h3 class="text-center">Update Voucher</h3>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <?php
                                            $existingDataQuery = "SELECT v.*, c.categoryName
                                                                FROM voucher v
                                                                LEFT JOIN category c ON v.categoryID = c.categoryID
                                                                WHERE v.voucherID = $voucherID";
                                            $existingDataResult = mysqli_query($conn, $existingDataQuery);
                                            $existingData = mysqli_fetch_assoc($existingDataResult);
                                            
                                            $existingCategoryID     = $existingData['categoryID'];
                                            $existingCategoryName   = $existingData['categoryName'];
                                        ?>
                                        <div class="modal-body">
                                            
                                            <form action="functions.php" method="post">
                                                <input type="hidden" name="voucherID" value="<?php echo $voucherID; ?>">
                                                <!-- Card -->
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-floating my-3">
                                                            <select class="form-control w-100" name="voucherCategory" id="voucherCategory" value="<?php echo $categoryName;?>" required>
                                                                <option value="">Select Category</option>
                                                                <?php
                                                                $categoryQuery = "SELECT * FROM category";
                                                                $categoryResult = mysqli_query($conn, $categoryQuery);
    
                                                                while($row = mysqli_fetch_assoc($categoryResult)){
                                                                    $categoryID   = $row['categoryID'];
                                                                    $categoryName = $row['categoryName'];
    
                                                                    echo "<option value=\"$categoryID\"";
                                                                    echo ($categoryID == $existingCategoryID) ? ' selected' : '';
                                                                    echo ">$categoryName</option>";
                                                                }
                                                            ?>
                                                                ?>
                                                            </select>
                                                            <label for="voucherCategory">Voucher Category</label>
                                                        </div>
                                                        <div class="form-floating my-3">
                                                            <input type="text" class="form-control" name="voucherCode" id="voucherCode" value="<?php echo $voucherCode;?>">
                                                            <label for="voucherCode">Voucher Code</label>
                                                        </div>
                                                        <div class="text-end">
                                                            <button class="btn btn-success my-2 mx-2" name="updateVoucher" type="submit">Save changes</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Add Voucher Modal -->
<div class="modal fade" id="addVoucherModal" tabindex="-1" aria-labelledby="addVoucherModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-center">Add Voucher Form</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form -->
                <form action="functions.php" method="post" id="VoucherForm">
                    <div class="form-floating my-3">
                        <select class="form-control w-100" name="voucherCategory" id="voucherCategory" required>
                            <option value="">Select Category</option>
                            <?php
                            $categoryQuery = "SELECT * FROM category";
                            $categoryResult = mysqli_query($conn, $categoryQuery);

                            while($row = mysqli_fetch_assoc($categoryResult)){
                                $categoryID     = $row['categoryID'];
                                $categoryName   = $row['categoryName'];

                                echo "<option value=\"$categoryID\">$categoryName</option>";
                            }
                            ?>
                        </select>
                        <label for="voucherCategory">Voucher Category</label>
                    </div>
                    <div class="form-floating my-3">
                        <input type="text" class="form-control" id="voucherCode" name="voucherCode" placeholder="Enter voucher code." required>
                        <label for="voucherCode">Voucher Code</label>
                    </div>
            </div>
                <div class="modal-footer">
                    <button name="addVoucher" class="btn btn-secondary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
