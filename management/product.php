<?php
$pageTitle = "Product Page";
include 'header.php';
include 'navbar.php';
if (isset($_SESSION['username'])) {
    $username   = $_SESSION['username'];
    $userQuery  = "SELECT * FROM user WHERE username = '$username'";
    $userResult = mysqli_query($conn, $userQuery);

    while ($userRow = mysqli_fetch_assoc($userResult)) {
        $userID     = $userRow['userID'];

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

$query = "SELECT c.categoryID, c.categoryName, p.*
              FROM category c 
              NATURAL JOIN product p";

if ($categoryFilter > 0) {
    $query .= " WHERE c.categoryID = $categoryFilter";
}

$query .= " GROUP BY p.prodID DESC
               LIMIT $offset, $recordsPerPage";

$result = mysqli_query($conn, $query);
?>

<div class="container">
    <h1 class="text-center fw-bold mt-4">Product Management</h1>
    <?php include 'product_nav.php'; ?>
    <div class="col-md-12 p-0">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Form on the left -->
            <form class="form-inline d-inline">
                <select class="custom-select mr-3 data" name="category" id="category" onchange="this.form.submit()">
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

            <!-- Button on the right -->
            <button class="btn btn-dark h-100" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
        </div>
    </div>

</div>
<div class="container w-100">
    <div class="table-responsive">
        <table class="table table-hover">
            <!-- <thead class="text-center">
                <tr>
                    <th scope="col">Category</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Action</th>
                </tr>
            </thead> -->
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $categoryID      = $row['categoryID'];
                    $categoryName    = $row['categoryName'];
                    $prodID          = $row['prodID'];
                    $prodName        = $row['prodName'];
                    $prodDescription = $row['prodDescription'];
                    $prodImage       = $row['prodImage'];
                    $prodURL         = $row['prodURL'];

                ?>
                    <tr class="card w-100 d-flex flex-row justify-content-between mb-3 p-4">
                        <td><?php echo $categoryName ?></td>
                        <td class="w-75 "><?php echo $prodName ?></td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                <!-- View Button -->
                                <div class="text-center me-1">
                                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#viewProductModal<?php echo $prodID; ?>">
                                        <div class="d-flex align-items-center">
                                            <i class="fa-solid fa-eye"></i>
                                        </div>
                                    </button>
                                </div>
                                <!-- Edit Button -->
                                <div class="text-center">
                                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#editProductModal<?php echo $prodID; ?>">
                                        <div class="d-flex align-items-center">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="viewProductModal<?php echo $prodID; ?>" tabindex="-1" aria-labelledby="viewProductModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-md">
                                    <div class="modal-content rounded-15 p-2">
                                        <div class="modal-body">
                                            <img src="<?php echo $prodImage; ?>" alt="Product Image" class="img-fluid rounded-15 mb-3">
                                            <h5 id="viewProductModalLabel"><?php echo $prodName; ?></h5>
                                            <p><?php echo $prodDescription; ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="<?php echo $prodURL; ?>" target="_blank" class="btn btn-primary">View Product</a>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
    </div>

    <!-- Update Product -->
    <div class="modal fade" id="editProductModal<?php echo $prodID; ?>" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="text-center">Update Product</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="functions.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="prodID" value="<?php echo $prodID; ?>">
                        <!-- Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="<?php echo $prodImage; ?>" href="<?php echo $prodURL ?>" class="rounded" style="width: 70%" alt="Product Image">
                                </div>
                                <div class="my-3">
                                    <label for="prodImage" class="text-muted mb-2">Product Image</label>
                                    <input type="file" class="form-control" name="prodImage" id="prodImage">
                                </div>
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="prodName" id="prodName" value="<?php echo $prodName; ?>">
                                    <label for="prodName">Product Name</label>
                                </div>
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="prodURL" id="prodURL" value="<?php echo $prodURL; ?>">
                                    <label for="prodURL">Product URL</label>
                                </div>
                                <div class="form-floating my-3">
                                    <textarea type="text" style="resize: none; height: 100px;" class="form-control" id="prodDescription" name="prodDescription"><?php echo $prodDescription; ?></textarea>
                                    <label for="prodDescription">Product Description</label>
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-success my-2 mx-2" name="updateProduct" type="submit">Save changes</button>
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

<!-- Pagination -->
<div class="container w-75">
    <ul class="pagination justify-content-center">
        <?php
        $totalRecordsQuery = "SELECT COUNT(*) AS totalRecords FROM product";
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

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-center">Add Product</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form -->
                <form action="functions.php" method="post" id="productForm" enctype="multipart/form-data">
                    <div class="main-form">
                        <div class="form-floating my-3">
                            <input type="text" class="form-control" id="prodName" name="prodName" placeholder="Enter product's name" required>
                            <label for="prodName">Product Name</label>
                        </div>
                        <div class="form-floating my-3">
                            <textarea type="text" style="resize: none; height: 100px;" class="form-control" id="prodDescription" name="prodDescription" placeholder="Enter product's description"></textarea>
                            <label for="prodDescription" class="form-label">Product Description</label>
                        </div>
                        <div class="text-muted my-3">
                            <label for="prodImage">Product Image</label>
                            <input type="file" class="form-control" id="prodImage" name="prodImage">
                        </div>
                        <div class="form-floating my-3">
                            <input type="text" class="form-control" id="prodURL" name="prodURL" placeholder="Enter product's URL" required>
                            <label for="prodURL">Product URL</label>
                        </div>
                        <div class="form-floating my-3">
                            <select class="form-control w-100" name="prodCategory" id="prodCategory" required>
                                <option value="">Select Category</option>
                                <?php
                                $categoryQuery = "SELECT * FROM category";
                                $categoryResult = mysqli_query($conn, $categoryQuery);

                                while ($row = mysqli_fetch_assoc($categoryResult)) {
                                    $categoryID     = $row['categoryID'];
                                    $categoryName   = $row['categoryName'];

                                    echo "<option value=\"$categoryID\">$categoryName</option>";
                                }
                                ?>
                            </select>
                            <label for="prodCategory">Product Category</label>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button name="addProduct" class="btn btn-secondary" type="submit" id="submitAdd">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@2.0.1/dist/js/multi-select-tag.js"></script>