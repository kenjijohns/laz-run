<?php
// Get the current page filename from the parameter
$current_page = isset($_GET['page']) ? $_GET['page'] : '';

// Check if the current page is customize.php
$isCustomizePage = ($current_page === 'customize.php' || $current_page === 'customize');
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <?php if ($isCustomizePage): ?>
        <link rel="stylesheet" href="./assets/styles.css">
    <?php else: ?>
        <link rel="stylesheet" href="./assets/styles.css">
    <?php endif; ?>
    <link rel="icon" type="image/x-icon" href="./assets/fav-icon.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@2.0.1/dist/css/multi-select-tag.css">
</head>

<?php
    if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
        header("Location: index.php");
        exit();
    }
    include 'db.php';
?>
