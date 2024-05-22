<?php
$pageTitle = "Analytics";
include 'header.php';
include 'navbar.php';
include '../db.php';

$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 0;

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

$timeFilterStart = isset($_GET['timestamp_start']) ? $_GET['timestamp_start'] : '';
$timeFilterEnd = isset($_GET['timestamp_end']) ? $_GET['timestamp_end'] : '';
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'general';

$query = "SELECT COUNT(DISTINCT IFNULL(s.guestID, 'NULL')) AS siteVisits, 
                     SUM(CASE WHEN s.status = 1 THEN 1 ELSE 0 END) AS dropOffSessions,
                     COUNT(DISTINCT CASE WHEN s.status = 2 THEN s.timestamp ELSE NULL END) AS completedSessions,
                     COUNT(DISTINCT CASE WHEN s.status IN (1, 2) THEN s.timestamp ELSE NULL END) AS totalSessions,
                     COUNT(DISTINCT CASE WHEN s.status != 0 THEN s.guestID ELSE NULL END) AS totalUsers,
                     DATE(s.timestamp) AS sessionDate,
                     s.device_type, 
                     c.categoryName,
                     p.prodName,
                     s.locationFrom
              FROM session s
              LEFT JOIN product p ON s.prodID = p.prodID
              LEFT JOIN category c ON p.categoryID = c.categoryID";
//Removed condition WHERE prodID = NULL to Count Site Visits correctly


// Append Conditions when a category is selected
if ($selectedCategory === 'general') {
    $query .= " WHERE 1=1"; // This ensures that the following conditions can be appended with "AND"
} elseif (!empty($selectedCategory)) {
    $query .= " WHERE p.categoryID = $selectedCategory";
}
// Append Conditions when a date filter is selected
if (!empty($timeFilterStart) && !empty($timeFilterEnd)) {
    $formattedTimestampStart = date("Y-m-d H:i:s", strtotime($timeFilterStart));
    $formattedTimestampEnd = date("Y-m-d H:i:s", strtotime($timeFilterEnd . ' + 1 day'));
    $query .= " AND s.timestamp BETWEEN '$formattedTimestampStart' AND '$formattedTimestampEnd'";
}

$query .= " GROUP BY DATE(s.timestamp), c.categoryName, s.device_type, s.guestID, s.locationFrom";
$result = mysqli_query($conn, $query);

$totalSessions          = 0;
$siteVisits             = 0;
$completedSessions      = 0;
$dropOffSessions        = 0;
$categoryCounts         = [];
$deviceCounts           = [];
$sessionDates           = [];
$completedSessionsData  = [];
$dropOffSessionsData    = [];
$countryCounts          = [];

while ($row = mysqli_fetch_assoc($result)) {
    $totalSessions              += $row['totalSessions'];
    $siteVisits                 += $row['siteVisits'];
    $categoryName               = $row['categoryName'];
    $sessionDates[]             = $row['sessionDate'];
    $completedSessions          += $row['completedSessions'];
    $dropOffSessions            += $row['dropOffSessions'];
    $completedSessionsData[]    = $row['completedSessions'];
    $dropOffSessionsData[]      = $row['dropOffSessions'];
    $deviceType                 = $row['device_type'];
    $prodName                   = $row['prodName'];
    $country                    = $row['locationFrom'];

    // Add condition to remove key-value pair if category name is NULL, it can be NULL if drop off and site visit
    if ($categoryName !== NULL) {
        $categoryCounts[$categoryName] = ($categoryCounts[$categoryName] ?? 0) + $row['totalSessions'];
    }

    $deviceCounts[$deviceType]      = ($deviceCounts[$deviceType] ?? 0) + $row['siteVisits'];
    $countryCounts[$country] = ($countryCounts[$country] ?? 0) + $row['siteVisits'];
}
// Separate query to count prodCounts
$prodCountsQuery = "SELECT COUNT(*) AS count, p.prodName
                    FROM session s
                    LEFT JOIN product p ON s.prodID = p.prodID
                    LEFT JOIN category c ON p.categoryID = c.categoryID";

// Append Conditions when a category is selected
if ($selectedCategory === 'general') {
    $prodCountsQuery .= " WHERE 1=1"; // This ensures that the following conditions can be appended with "AND"
} elseif (!empty($selectedCategory)) {
    $prodCountsQuery .= " WHERE p.categoryID = $selectedCategory";
}

// Append Conditions when a date filter is selected
if (!empty($timeFilterStart) && !empty($timeFilterEnd)) {
    $formattedTimestampStart = date("Y-m-d H:i:s", strtotime($timeFilterStart));
    $formattedTimestampEnd = date("Y-m-d H:i:s", strtotime($timeFilterEnd . ' + 1 day'));
    $prodCountsQuery .= " AND s.timestamp BETWEEN '$formattedTimestampStart' AND '$formattedTimestampEnd'";
}

$prodCountsQuery .= " GROUP BY p.prodName";

// Execute the query
$prodCountsResult = mysqli_query($conn, $prodCountsQuery);

// Initialize prodCounts array
$prodTable = [];
$prodCounts = [];

// Fetch prodCounts results
while ($prodRow = mysqli_fetch_assoc($prodCountsResult)) {
    $prodTable[] = $prodRow; //Comment this line for Chart
    $prodCounts[$prodRow['prodName']] = $prodRow['count'];
}
// Source query
$sourceQuery = "SELECT s.source, COUNT(DISTINCT s.timestamp) AS count
                FROM session s
                LEFT JOIN product p ON s.prodID = p.prodID
                LEFT JOIN category c ON p.categoryID = c.categoryID";

if ($selectedCategory === 'general') {
    $sourceQuery .= " WHERE 1=1"; // This ensures that the following conditions can be appended with "AND"
} elseif (!empty($selectedCategory)) {
    $sourceQuery .= " WHERE p.categoryID = $selectedCategory";
}

if (!empty($timeFilterStart) && !empty($timeFilterEnd)) {
    $sourceQuery .= " AND s.timestamp BETWEEN '$formattedTimestampStart' AND '$formattedTimestampEnd'";
}

$sourceQuery .= " GROUP BY s.source";
$sourceQuery .= " ORDER BY s.source";
$sourceResult = mysqli_query($conn, $sourceQuery);

$sources = [];
while ($subrow = mysqli_fetch_assoc($sourceResult)) {
    $sources[$subrow['source']] = $subrow['count'];
}

$outBoundQuery = "  SELECT p.prodURL, p.prodName,
                        COUNT(s.outbound) AS count
                        FROM session s
                        INNER JOIN product p ON s.prodID = p.prodID
                        WHERE s.outbound = 1
    ";

if (!empty($timeFilterStart) && !empty($timeFilterEnd)) {
    $outBoundQuery .= " AND s.timestamp BETWEEN '$formattedTimestampStart' AND '$formattedTimestampEnd'";
}

if ($selectedCategory !== 'general') {
    $outBoundQuery .= " AND p.categoryID = $selectedCategory";
}

$outBoundQuery .= " GROUP BY p.prodURL";
$outBoundResult = mysqli_query($conn, $outBoundQuery);

$outBoundData = array();
while ($row = mysqli_fetch_assoc($outBoundResult)) {
    $outBoundData[] = array(
        'prodName' => $row['prodName'],
        'prodURL' => $row['prodURL'],
        'count' => $row['count']
    );
}

// Separated Total Users Query because and error occurs when combined in the main query
$totalUsersQuery = "SELECT COUNT(DISTINCT guestID) AS totalUsers 
    FROM session s
    LEFT JOIN product p ON s.prodID = p.prodID
    LEFT JOIN category c ON p.categoryID = c.categoryID
    WHERE s.status <> 0";

if (!empty($timeFilterStart) && !empty($timeFilterEnd)) {
    $formattedTimestampStart = date("Y-m-d H:i:s", strtotime($timeFilterStart));
    $formattedTimestampEnd = date("Y-m-d H:i:s", strtotime($timeFilterEnd . ' + 1 day'));
    $totalUsersQuery .= " AND s.timestamp BETWEEN '$formattedTimestampStart' AND '$formattedTimestampEnd'";
}

if ($selectedCategory !== 'general') {
    $totalUsersQuery .= " AND p.categoryID = $selectedCategory";
}


$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsersRow = mysqli_fetch_assoc($totalUsersResult);
$users = $totalUsersRow['totalUsers'];

// Query the Top three Products per Category For GENERAL
$productsQuery = "SELECT 
    c.categoryName,
    p.prodName,
    COUNT(*) AS productCount
    FROM session s
    LEFT JOIN product p ON s.prodID = p.prodID
    LEFT JOIN category c ON p.categoryID = c.categoryID
    WHERE s.prodID IS NOT NULL ";

if (!empty($timeFilterStart) && !empty($timeFilterEnd)) {
    $productsQuery .= " AND s.timestamp BETWEEN '$formattedTimestampStart' AND '$formattedTimestampEnd'";
}

$productsQuery .= " GROUP BY c.categoryName, p.prodName
                        ORDER BY c.categoryName, productCount DESC";

$result = mysqli_query($conn, $productsQuery);

$topProductsPerCategory = []; // Associative array to store top 3 products per category for GENERAL

while ($row = mysqli_fetch_assoc($result)) {
    $categoryName = $row['categoryName'];
    $prodName = $row['prodName'];
    $productCount = $row['productCount'];

    // Initialize category array
    if (!isset($topProductsPerCategory[$categoryName])) {
        $topProductsPerCategory[$categoryName] = [
            "Top 1 Products" => [],
            "Top 2 Products" => [],
            "Top 3 Products" => []
        ];
    }

    // Store the top products per category
    foreach ($topProductsPerCategory[$categoryName] as $key => $value) {
        if (count($value) < 1) {
            $topProductsPerCategory[$categoryName][$key][] = [
                'prodName' => $prodName,
                'productCount' => $productCount
            ];
            break;
        }
    }
}
// Use to create the chart
$generalProductChartData = [];

foreach ($topProductsPerCategory as $category => $tops) {
    foreach ($tops as $top => $products) {
        foreach ($products as $product) {
            $generalProductChartData[$category][$top][] = [
                'name' => $product['prodName'],
                'count' => $product['productCount']
            ];
        }
    }
}

//Execute the query to fetch question answer data ONLY IF a category is selected

if (isset($selectedCategory) && $selectedCategory !== 'general') {
    // Query to get data for the selected category with optional date filtering
    $queryQuestionAnswer = "SELECT pq.pqID, pq.pqContent AS question, a.answerContent AS answer,
                                COUNT(DISTINCT s.guestID) AS totalUsers,
                                COUNT(sa.saID) AS clickCount
                                FROM parent_question pq
                                LEFT JOIN question_answer qa ON pq.pqID = qa.pqID
                                LEFT JOIN answer a ON qa.answerID = a.answerID
                                LEFT JOIN session_answers sa ON a.answerID = sa.answerID
                                LEFT JOIN session s ON sa.sessionID = s.sessionID
                                LEFT JOIN product p ON s.prodID = p.prodID
                                WHERE p.categoryID = $selectedCategory";

    if (!empty($timeFilterStart) && !empty($timeFilterEnd)) {
        $queryQuestionAnswer .= " AND s.timestamp BETWEEN '$formattedTimestampStart' AND '$formattedTimestampEnd'";
    }
    $queryQuestionAnswer .= " GROUP BY pq.pqID, pq.pqContent, a.answerID, a.answerContent";

    $resultQuestionAnswer = mysqli_query($conn, $queryQuestionAnswer);

    $questionAnswerData = [];

    while ($row = mysqli_fetch_assoc($resultQuestionAnswer)) {
        $question = $row['question'];
        $answer = $row['answer'];
        $clickCount = (int)$row['clickCount'];
        $totalUsers = (int)$row['totalUsers'];

        // Add data to the array
        $questionAnswerData[$question][] = [
            'answer' => $answer,
            'clickCount' => $clickCount,
            'totalUsers' => $totalUsers,
        ];
    }
}

// Use to hide divs if in general category
$hideIfGeneral = !isset($selectedCategory) || $selectedCategory === 'general';

// Use to hide divs if in a selected category
$hideIfCategory = isset($selectedCategory) && $selectedCategory !== 'general';
?>

<div class="container">
    <div class="row align-content-center">
        <div class="row">
            <div class="col-md-12">
                <form id="filterForm" action="" method="GET">
                    <div class="row align-items-center">
                        <div class="col">
                            <div>
                                <?php
                                // Fetch category details from the category table
                                $categoryQuery = "SELECT categoryID, categoryName FROM category";
                                $categoryResult = mysqli_query($conn, $categoryQuery);
                                $categories = array();

                                while ($categoryRow = $categoryResult->fetch_assoc()) {
                                    $categories[$categoryRow['categoryID']] = $categoryRow['categoryName'];
                                }
                                ?>
                                <form class="form-inline d-inline">
                                    <label for="categoryDropdown">Select Category:</label>
                                    <select class="custom-select data mr-3" name="category" id="category" onchange="window.location.href=this.value;">
                                        <option value="analytics.php">General</option>
                                        <?php foreach ($categories as $categoryID => $categoryName) : ?>
                                            <option value="?category=<?php echo $categoryID; ?>" <?php echo ($categoryFilter == $categoryID) ? 'selected' : ''; ?>><?php echo $categoryName; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="col">
                            <label for="timestamp_start">Start Date:</label>
                            <input type="date" class="form-control" name="timestamp_start" id="timestamp_start" value="<?php echo $timeFilterStart ? $timeFilterStart : date('Y-m-01'); ?>">
                        </div>
                        <div class="col">
                            <label for="timestamp_end">End Date:</label>
                            <input type="date" class="form-control" name="timestamp_end" id="timestamp_end" value="<?php echo $timeFilterEnd ? $timeFilterEnd : date('Y-m-t'); ?>">
                        </div>
                        <div class="col">
                            <label class="d-none d-md-block"><br /></label>
                            <input type="hidden" name="category" id="selected_category" value="<?php echo $selectedCategory; ?>" class="d-none d-md-block">
                            <button type="submit" class="btn btn-dark w-100 filter-btn">FILTER</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row ">
            <?php if (!$hideIfCategory) : ?>
                <div class="col ">
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" class="info-btn" aria-label="Information">
                                <i class="fas fa-info-circle"></i>
                                <span class="tooltip">Total number of visits to the website. </span>
                            </button>
                            <h5>Site Visits</h5>
                            <h2><?php echo $siteVisits; ?></h2>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Users<button type="button" class="info-btn" aria-label="Information">
                                <i class="fas fa-info-circle"></i>
                                <span class="tooltip">Total count of unique individuals who accessed the quiz.</span>
                            </button>
                        </h5>
                        <h2><?php echo $users; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Sessions
                            <button type="button" class="info-btn" aria-label="Information">
                                <i class="fas fa-info-circle"></i>
                                <span class="tooltip">Total number of user engagements or interactions. </span>
                            </button>
                        </h5>
                        <h2><?php echo $totalSessions; ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Device Count
                            <button type="button" class="info-btn" aria-label="Information">
                                <i class="fas fa-info-circle"></i>
                                <span class="tooltip">Total count of unique devices used to access the site. </span>
                            </button>
                        </h5>
                        <div id="deviceChart"></div>
                    </div>
                </div>
            </div>
            <?php if (!$hideIfGeneral) : ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-center">Country <button type="button" class="info-btn" aria-label="Information">
                                    <i class="fas fa-info-circle"></i>
                                    <span class="tooltip">Visitors' country of origin.</span>
                                </button></h5>
                            <div id="countryChart"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!$hideIfCategory) : ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-center">Completed & Drop Off Sessions
                                <button type="button" class="info-btn" aria-label="Information">
                                    <i class="fas fa-info-circle"></i>
                                    <span class="tooltip">
                                        Completed sessions denote successful attempts, while drop-off sessions represent unfinished attempts.
                                    </span>
                                </button>
                            </h5>
                            <div id="completedDropOffChart"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!$hideIfGeneral) : ?>
            <div class="row d-none d-md-block">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-center">Most Recommended Products</h5>
                            <div id="productRecommendedChartCategory"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!$hideIfGeneral) : ?>
            <div class="row d-block d-md-none">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-center">Most Recommended Products</h5>
                            <div class="text-center">
                                <table class="table">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Product</th>
                                            <th>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prodTable as $row) : ?>
                                            <tr>
                                                <td><?php echo $row['prodName']; ?></td>
                                                <td><?php echo $row['count']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!$hideIfCategory) : ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-center">Most Recommended Products
                            </h5>
                            <button id="exportProducts" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                            </button>
                            <button type="button" class="info-btn" aria-label="Information">
                                <i class="fas fa-info-circle"></i>
                                <span class="tooltip">Top 3 most recommended products per category.</span>
                            </button>
                            <div id="productRecommendedChartGeneral"></div>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>
        <?php if (!$hideIfGeneral) : ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-center">Questionnaire Data</h5>
                            <button type="button" class="info-btn" aria-label="Chart Information">
                                <i class="fas fa-info-circle"></i>
                                <span class="tooltip">Total count of each answer option selected. </span>
                            </button>
                            <span class="visually-hidden">Chart Information</span> <!-- Visible label for button -->
                            <div id="questionAnswerChart" class="overflow-auto"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!$hideIfCategory) : ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-center">Country <button type="button" class="info-btn" aria-label="Information">
                                    <i class="fas fa-info-circle"></i>
                                    <span class="tooltip">Visitors' country of origin.</span>
                                </button></h5>
                            <div id="countryChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-center">Session per Category<button type="button" class="info-btn" aria-label="Information">
                                    <i class="fas fa-info-circle"></i>
                                    <span class="tooltip">Total number of sessions categorized by type.</span>
                                </button>
                            </h5>
                            <div id="categoryChart"></div>
                        </div>
                    </div>
                </div>
        </div>
        <?php endif; ?>
        <div class="row">
            <div class="col col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Source <button type="button" class="info-btn" aria-label="Information">
                                <i class="fas fa-info-circle"></i>
                                <span class="tooltip">Origin of user website access. </span>
                            </button></h5>
                            <button id="exportSource" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                            </button>
                            <div class="text-center">
                            <table class="table text-center">
                                <thead>
                                    <tr>
                                        <th>Source</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sources as $source => $count) { ?>
                                        <tr>
                                            <td><?php echo $source; ?></td>
                                            <td><?php echo $count; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <table class="table">
                            <button id="exportOutbound" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                            </button>
                                <thead class="text-center">
                                    <tr>
                                        <th>Product Recommended</th>
                                        <th>Click Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($outBoundData as $row) : ?>
                                        <tr>
                                            <td> <a href="<?php echo $row['prodURL']; ?>" target="_blank"> <?php echo $row['prodName']; ?></a></td>
                                            <td><?php echo $row['count']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@2.11.5/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const infoBtns = document.querySelectorAll('.info-btn');
        const isMobile = window.matchMedia("only screen and (max-width: 768px)").matches;

        infoBtns.forEach(function(infoBtn) {
            let tooltipShown = false;

            // Toggle tooltip visibility on button click
            infoBtn.addEventListener('click', function(event) {
                event.stopPropagation(); // Prevents the click event from propagating to document
                tooltipShown = !tooltipShown;
                this.classList.toggle('show-tooltip', tooltipShown);
            });

            if (!isMobile) {
                // Show tooltip on hover if not on mobile
                infoBtn.addEventListener('mouseenter', function() {
                    if (!tooltipShown) {
                        this.classList.add('show-tooltip');
                    }
                });

                // Hide tooltip when mouse leaves button if not on mobile
                infoBtn.addEventListener('mouseleave', function() {
                    if (!tooltipShown) {
                        this.classList.remove('show-tooltip');
                    }
                });
            }
        });

        // Close tooltip when page is hidden
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'hidden') {
                infoBtns.forEach(function(infoBtn) {
                    infoBtn.classList.remove('show-tooltip');
                });
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
    const infoBtns = document.querySelectorAll('.info-btn');
    const isMobile = window.matchMedia("only screen and (max-width: 768px)").matches;
 
    infoBtns.forEach(function(infoBtn) {
        let tooltipShown = false;
 
        // Toggle tooltip visibility on button click
        infoBtn.addEventListener('click', function(event) {
            event.stopPropagation(); // Prevents the click event from propagating to document
            tooltipShown = !tooltipShown;
            this.classList.toggle('show-tooltip', tooltipShown);
        });
 
        if (!isMobile) {
            // Show tooltip on hover if not on mobile
            infoBtn.addEventListener('mouseenter', function() {
                if (!tooltipShown) {
                    this.classList.add('show-tooltip');
                }
            });
 
            // Hide tooltip when mouse leaves button if not on mobile
            infoBtn.addEventListener('mouseleave', function() {
                if (!tooltipShown) {
                    this.classList.remove('show-tooltip');
                }
            });
        }
    });
 
    // Close tooltip when clicking anywhere on the document
    document.addEventListener('click', function() {
        infoBtns.forEach(function(infoBtn) {
            infoBtn.classList.remove('show-tooltip');
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Update the form action URL when category is selected
        document.querySelectorAll('#categoryDropdown a').forEach(function(categoryLink) {
            categoryLink.addEventListener('click', function(event) {
                event.preventDefault();
                var categoryID = this.getAttribute('data-category-id');
                document.getElementById('selected_category').value = categoryID;
                document.getElementById('filterForm').submit();
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('timestamp_start');
        const endDateInput = document.getElementById('timestamp_end');

        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            if (endDateInput.value < this.value) {
                endDateInput.value = '';
            }
        });


        // Prepare data for pie chart
        var device_types = <?php echo json_encode($deviceCounts); ?>;
        var deviceCounts = {};
        for (var device in device_types) {
            if (device_types.hasOwnProperty(device)) {
                var toUpperCase = device.charAt(0).toUpperCase() + device.slice(1);
                deviceCounts[toUpperCase] = device_types[device];
            }
        }
        // var totalSessions = Object.values(deviceCounts).reduce((acc, val) => acc + val, 0);
        var deviceChartData = Object.values(deviceCounts).map(count => Number(count));
        var deviceLabels = Object.keys(deviceCounts);

        var deviceChartOptions = {
            chart: {
                type: 'donut',
                height: 240,
                toolbar: {
                            show: true,
                            export: {
                                csv: {
                                    filename: "device_types",
                                    headerCategory: 'Device Type',
                                    headerValue: 'Count',
                                },
                           },
                        },
            },
            series: deviceChartData,
            labels: deviceLabels,
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                        },
                        size: '60%'
                    },
                    dataLabels: {
                        show: true,
                    },
                },
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                       
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var deviceChart = new ApexCharts(document.querySelector("#deviceChart"), deviceChartOptions);
        deviceChart.render();

        var categoryCounts = <?php echo json_encode($categoryCounts); ?>;
        var categoryChartData = Object.values(categoryCounts).map(count => Number(count));
        var categoryLabels = Object.keys(categoryCounts);

        var sessionsPerCategoryChartOptions = {
            chart: {
                type: 'donut',
                height: 240,
                toolbar: {
                            show: true,
                            export: {
                                csv: {
                                    filename: "sessions_per_category",
                                    headerCategory: 'Category',
                                    headerValue: 'Sessions',
                                },
                           },
                        },
            },
            series: categoryChartData,
            labels: categoryLabels,
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                        },
                        size: '60%'
                    },
                    dataLabels: {
                        show: true,
                    },
                },
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                      
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var sessionsPerCategoryChart = new ApexCharts(document.querySelector("#categoryChart"), sessionsPerCategoryChartOptions);
        sessionsPerCategoryChart.render();

        <?php $completedDropOffData = array($completedSessions, $dropOffSessions); ?>
        var completedDropOffData = <?php echo json_encode($completedDropOffData); ?>;
        var completedDropOffOptions = {
            chart: {
                height: 250,
                type: 'bar', // Change chart type to bar
                toolbar: {
                            show: false,
                        },
            },
            plotOptions: {
                bar: {
                    horizontal: true // Make bars horizontal
                }
            },
            series: [{
                data: completedDropOffData // Use the combined data directly as series data
            }],
            xaxis: {
                categories: ['Completed', 'Drop Off'], // Specify categories for y-axis
            },
        };

        var completedDropOffChart = new ApexCharts(document.querySelector("#completedDropOffChart"), completedDropOffOptions);
        completedDropOffChart.render();


        // top products per category chart - GENERAL
        var generalProductChartData = <?php echo json_encode($generalProductChartData); ?>;
        console.log(generalProductChartData);

        // Prepare data for chart
        let categories = [];
        let seriesTop1 = [];
        let seriesTop2 = [];
        let seriesTop3 = [];

        for (let category in generalProductChartData) {
            categories.push(category);
            let top1Count = generalProductChartData[category]["Top 1 Products"] ? parseInt(generalProductChartData[category]["Top 1 Products"][0].count) : 0;
            let top2Count = generalProductChartData[category]["Top 2 Products"] ? parseInt(generalProductChartData[category]["Top 2 Products"][0].count) : 0;
            let top3Count = generalProductChartData[category]["Top 3 Products"] ? parseInt(generalProductChartData[category]["Top 3 Products"][0].count) : 0;

            seriesTop1.push(top1Count);
            seriesTop2.push(top2Count);
            seriesTop3.push(top3Count);
        }

          var generalProductOptions = {
    chart: {
        type: 'bar',
        height: 250,
        toolbar: {
            show: false,
        }
    },
    plotOptions: {
        bar: {
            horizontal: true
        }
    },
    series: [{
        name: 'Top 1',
        data: seriesTop1
    }, {
        name: 'Top 2',
        data: seriesTop2
    }, {
        name: 'Top 3',
        data: seriesTop3
    }],
    xaxis: {
        categories: categories,
        title: {
            text: 'Category'
        }
    },
    yaxis: {
        title: {
            text: 'Count'
        }
    },
    legend: {
        position: 'top'
    },
    tooltip: {
        y: {
            formatter: function(value, { series, seriesIndex, dataPointIndex }) {
                let product = null;
                switch (seriesIndex) {
                    case 0:
                        product = generalProductChartData[categories[dataPointIndex]]["Top 1 Products"];
                        break;
                    case 1:
                        product = generalProductChartData[categories[dataPointIndex]]["Top 2 Products"];
                        break;
                    case 2:
                        product = generalProductChartData[categories[dataPointIndex]]["Top 3 Products"];
                        break;
                }
                if (product && product.length > 0) {
                    let productName = product[0].name;
                    // Logic for wrapping product name
                    const MAX_WIDTH = 35; // Adjust this value as needed
                    if (productName.length > MAX_WIDTH) {
                        const words = productName.split(' ');
                        const lines = [];
                        let currentLine = '';
                        for (const word of words) {
                            if (currentLine.length + word.length > MAX_WIDTH) {
                                lines.push(currentLine.trim());
                                currentLine = '';
                            }
                            currentLine += word + ' ';
                        }
                        lines.push(currentLine.trim());
                        productName = lines.join('<br>'); // Use <br> for line break
                    }
                    return productName + ': ' + value;
                } else {
                    return 'No product available: ' + value;
                }
            }
        }
    }
};


        var generalProductChart = new ApexCharts(document.querySelector("#productRecommendedChartGeneral"), generalProductOptions);
        generalProductChart.render();

        function exportGeneralProductsToCSV(data, filename) {
            // Create CSV content
            let csvContent = "data:text/csv;charset=utf-8,";

            // Add headers
            csvContent += "Product,Count\n";

            // Add data rows
            for (let category in data) {
                let topProducts = data[category];
                for (let key in topProducts) {
                    let productName = topProducts[key][0].name;
                    let productCount = parseInt(topProducts[key][0].count);
                    // Escape double quotes in product name and enclose in double quotes
                    productName = '"' + productName.replace(/"/g, '""') + '"';
                    csvContent += `${productName},${productCount}\n`;
                }
            }

            // Create a link element and trigger download
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            // Ensure UTF-8 encoding for proper character display
            encodedUri = encodedUri.replace(/%E2%80%8B/g, ""); // Remove any UTF-8 BOM characters
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", filename);
            document.body.appendChild(link); // Required for Firefox
            link.click();
        }

            document.getElementById('exportProducts').addEventListener('click', function() {
                exportGeneralProductsToCSV(generalProductChartData, "top_products_general.csv");
        });


        <?php arsort($prodCounts); ?> // Sort Products descending order by prodCount

        // If a category is selected - Most Recommended Products
        var categoryProductChartData = <?php echo json_encode($prodCounts); ?>;
        var products = Object.keys(categoryProductChartData);
        var counts = Object.values(categoryProductChartData).map(count => Number(count));
        console.log(categoryProductChartData);

        // Define the maximum label length
        const MAX_LABEL_LENGTH = 20; // Adjust this value as needed

        // Modify products data to wrap long labels into multiple lines
        const modifiedProducts = products.map(product => {
            if (product.length > MAX_LABEL_LENGTH) {
                // Logic for splitting product name and creating multi-line labels
                const words = product.split(' ');
                const lines = [];
                let currentLine = '';
                for (const word of words) {
                    if (currentLine.length + word.length > MAX_LABEL_LENGTH) {
                        lines.push(currentLine.trim());
                        currentLine = '';
                    }
                    currentLine += word + ' ';
                }
                lines.push(currentLine.trim());
                return lines;
            }
            return product; // Keep as-is if not too long
        });

        // Define chart options
        var productRecommendedOptions = {
            chart: {
                type: 'bar',
                height: 240,
                toolbar: {
                    // show: false // Hide the toolbar
                    export: {
                        csv: {
                            filename: 'recommended_products',
                            columnDelimiter: ',',
                            headerCategory: 'Product',
                            headerValue: 'Count'
                        }
                    }
                 }
            },
            series: [{
                name: 'Sessions',
                data: counts
            }],
            xaxis: {
                categories: modifiedProducts,
                labels: {
                    show: true,
                    style: {
                        fontFamily: "Inter, sans-serif",
                        cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
                    },
                }
            }
        };
        var categoryProductRecommendedChart = new ApexCharts(document.querySelector("#productRecommendedChartCategory"), productRecommendedOptions);
        categoryProductRecommendedChart.render();



        <?php if (!$hideIfGeneral) : ?> // Don't execute if no category is selected
            var questionAnswerData = <?php echo json_encode($questionAnswerData); ?>;
            for (const question in questionAnswerData) { // draw chart for each question
                if (questionAnswerData.hasOwnProperty(question)) {
                    drawQuestionAnswerChart(question, questionAnswerData[question]);
                }
            }

            function drawQuestionAnswerChart(question, questionData) {
                var chartContainerId = 'chartContainer_' + question; // Unique ID for each chart container

                // Create a new div element for each chart
                var chartContainer = document.createElement('div');
                chartContainer.id = chartContainerId;
                chartContainer.classList.add('p-4', 'bg-white', 'rounded', 'shadow-md', 'mb-4');
                document.getElementById('questionAnswerChart').appendChild(chartContainer);

                // Define the maximum label length
                const MAX_LABEL_LENGTH = 20; // Adjust this value as needed

                // Modify questionData to wrap long labels into multiple lines
                const modifiedQuestionData = questionData.map(answerData => {
                    const label = answerData.answer;
                    if (label.length > MAX_LABEL_LENGTH) {
                        const words = label.split(' '); // Split by space
                        const lines = [];
                        let currentLine = '';
                        for (const word of words) {
                            if (currentLine.length + word.length > MAX_LABEL_LENGTH) {
                                lines.push(currentLine.trim());
                                currentLine = '';
                            }
                            currentLine += word + ' ';
                        }
                        lines.push(currentLine.trim()); // Add the last line
                        return {
                            ...answerData,
                            answer: lines
                        }; // Use multi-line array as label
                    }
                    return answerData; // Keep as-is if not too long
                });

                const modifiedQuestion = question.length > 25 ?
                    wrapText(question, 25) : [question];

                function wrapText(text, maxLength) {
                    const words = text.split(' '); // Split by space
                    const lines = [];
                    let currentLine = '';
                    for (const word of words) {
                        if (currentLine.length + word.length > maxLength) {
                            lines.push(currentLine.trim());
                            currentLine = '';
                        }
                        currentLine += word + ' ';
                    }
                    lines.push(currentLine.trim()); // Add the last line
                    console.log(lines);
                    return lines;
                }

                // Define options object
                var options = {
                    title: {
                        text: question,
                        align: 'center',

                    },
                    // colors: ["#1A56DB", "#FDBA8C"],
                    responsive: [{
                        breakpoint: 768, // Adjust breakpoint for mobile size
                        options: {
                            title: {
                                text: modifiedQuestion,
                                align: 'center',
                                style: {
                                    fontSize: "12px",
                                }
                            },
                        }
                    }],

                    series: [{
                        name: 'Click Count',
                        data: questionData.map(answerData => answerData.clickCount)
                    }, {
                        name: 'Total Users',
                        data: questionData.map(answerData => answerData.totalUsers)
                    }],
                    chart: {
                        type: "bar",
                        height: "320px",
                        fontFamily: "Inter, sans-serif",
                        toolbar: {
                            show: true,
                            export: {
                                csv: {
                                    filename: "questionnaire_data",
                                    headerCategory: 'Answers',
                                },
                           },
                        },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: "65%",
                            borderRadiusApplication: "end",
                            // borderRadius: 10,
                        },
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        style: {
                            fontFamily: "Inter, sans-serif",
                        },
                        title: {
                            formatter: function(val) {
                                return question; // Display the full question here
                            }
                        }
                    },
                    states: {
                        hover: {
                            filter: {
                                type: "darken",
                                value: 1,
                            },
                        },
                    },
                    stroke: {
                        show: true,
                        width: 0,
                        colors: ["transparent"],
                    },
                    grid: {
                        show: false,
                        strokeDashArray: 4,
                        padding: {
                            left: 2,
                            right: 2,
                            top: -14
                        },
                    },
                    dataLabels: {
                        enabled: true,
                    },
                    legend: {
                        show: true,
                    },
                    xaxis: {
                        floating: false,
                        categories: modifiedQuestionData.map(answerData => answerData.answer),
                        labels: {
                            rotate: 0,
                            show: true,
                            style: {
                                fontFamily: "Inter, sans-serif",
                                cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
                            },
                        },
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false,
                        },
                    },
                    yaxis: {
                        show: false,
                    },
                    fill: {
                        opacity: 1,
                    },

                };

                var chart = new ApexCharts(chartContainer, options);
                chart.render();

            }

        <?php endif; ?>

        // Countries Chart
        var countryCounts = <?php echo json_encode($countryCounts); ?>;
        var countryChartData = Object.values(countryCounts).map(count => Number(count));
        var countryLabels = Object.keys(countryCounts);

        var countryChartOptions = {
            chart: {
                type: 'donut',
                height: 240,
                toolbar: {
                            show: true,
                            export: {
                                csv: {
                                    filename: "countries",
                                    headerCategory: 'Country',
                                    headerValue: 'Sessions',
                                },
                           },
                        },
            },
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                        },
                        size: '60%'
                    },
                    dataLabels: {
                        show: true,
                    },
                },
            },
            series: countryChartData,
            labels: countryLabels,
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var countryChart = new ApexCharts(document.querySelector("#countryChart"), countryChartOptions);
        countryChart.render();


    });

    var sources = <?php echo json_encode($sources) ?>;
var outBoundData = <?php echo json_encode($outBoundData) ?>;

function exportToCSV(data, filename) {
    let csvContent = "data:text/csv;charset=utf-8,";

    // Extract headers dynamically from the first object in the data array
    let headers = Object.keys(data[0]);
    csvContent += headers.join(',') + '\n';

    // Add data rows
    data.forEach(function(item) {
        let row = [];
        headers.forEach(function(header) {
            // Escape double quotes in each item and enclose in double quotes
            let value = item[header] || ''; // If item[header] is undefined, set it to an empty string
            value = '"' + value.toString().replace(/"/g, '""') + '"';
            row.push(value);
        });
        csvContent += row.join(',') + '\n';
    });

    // Create a link element and trigger download
    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    encodedUri = encodedUri.replace(/%E2%80%8B/g, ""); // Remove any UTF-8 BOM characters
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", filename);
    document.body.appendChild(link); // Required for Firefox
    link.click();
}

document.getElementById('exportSource').addEventListener('click', function() {
    exportToCSV(sources, "sources.csv");
});

document.getElementById('exportOutbound').addEventListener('click', function() {
    exportToCSV(outBoundData, "outBoundData.csv");
});


</script>