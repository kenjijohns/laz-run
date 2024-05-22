<?php
session_start();

if (!isset($_SESSION['user_authenticated'])) {
    header('Location: login.php');
    exit();
}

include '../db.php';
$username   = $_SESSION['username'];
$userQuery  = "SELECT * FROM user WHERE username = '$username'";
$userResult = mysqli_query($conn, $userQuery);

while ($userRow = mysqli_fetch_assoc($userResult)) {
    $userID        = $userRow['userID'];
    $userFirstName = $userRow['firstName'];
    $userLastName  = $userRow['lastName'];
}
$pageTitle = "Quiz Management";
include 'header.php';
// include 'navbar.php';
?>

<div class="container desktop-height d-flex justify-content-center align-items-center vh-100">
    <div class="row w-100 index-spacer">
        <!-- <div class="col-md-12 text-center">
            <h1>Welcome <?php $username ?> admin!</h1>
        </div> -->
        <div class="col-md-4">
            <a href="product.php" class="card text-center index-card py-3">
                <div class="card-body">
                    <i class="fa-solid fa-tag index-icon" style="color: #f17482;"></i>
                    <h5>Products</h5>
                    <button type="button" class="info-btn" aria-label="Information">
                        <i class="fas fa-info-circle"></i>
                        <span class="tooltip">Add, edit, or remove questions.</span>
                    </button>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="question.php" class="card text-center index-card py-3">
                <div class="card-body">
                    <i class="fa-solid fa-circle-question index-icon" style="color:#f3b76d;"></i>
                    <h5>Questions</h5>
                    <button type="button" class="info-btn" aria-label="Information">
                        <i class="fas fa-info-circle"></i>
                        <span class="tooltip">Add, edit, or remove questions.</span>
                    </button>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="voucher.php" class="card text-center index-card  py-3">
                <div class="card-body">
                    <i class="fa-solid fa-ticket index-icon" style="color: #94f191"></i>
                    <h5>Vouchers</h5>
                    <button type="button" class="info-btn" aria-label="Information">
                        <i class="fas fa-info-circle"></i>
                        <span class="tooltip">Add, edit, or remove vouchers.</span>
                    </button>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="analytics.php" class="card text-center index-card  py-3">
                <div class="card-body">
                    <i class="fa-solid fa-chart-column index-icon" style="color: #8bbed1"></i>
                    <h5>Analytics</h5>
                    <button type="button" class="info-btn" aria-label="Information">
                        <i class="fas fa-info-circle"></i>
                        <span class="tooltip">View the data.</span>
                    </button>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="customize.php" class="card text-center index-card  py-3">
                <div class="card-body">
                    <i class="fas fa-cogs index-icon" style="color: #dfded4;"></i>
                    <h5>Customization</h5>
                    <button type="button" class="info-btn" aria-label="Information">
                        <i class="fas fa-info-circle"></i>
                        <span class="tooltip">Customize the user interface of the quiz.</span>
                    </button>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="logout.php" class="card text-center logout-card  py-3">
                <div class="card-body color-white">
                    <i class="fa-solid fa-arrow-right-from-bracket index-icon"></i>
                    <h5>Logout </h5>
                </div>
            </a>
        </div>
    </div>
</div>

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
</script>