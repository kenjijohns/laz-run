<?php
    session_start();
    $pageTitle = "P&G Laz-Run Quiz";
    include 'header.php';
    include 'db.php';
    include_once 'detect.php'; 

    // Device Type
    $mdetect = new MobileDetect(); 
 
    $deviceType = "";
    if($mdetect->isMobile()){ 
        if($mdetect->isTablet()){ 
            $deviceType = "tablet";
        }else{ 
            $deviceType = "mobile";
        } 
    } else{ 
        $deviceType = "desktop";
    }

    if (isset($deviceType)) {
        $_SESSION['deviceType'] = $deviceType;
    }

    // Guest ID
    if (!isset($_SESSION['guestID'])) {
        $guestID = rand(10, 999999999);
        $_SESSION['guestID'] = $guestID;
    } else {
        $guestID = $_SESSION['guestID'];
    }

    // Country
    function get_IP_address(){
        foreach (array('HTTP_CLIENT_IP',
                    'HTTP_X_FORWARDED_FOR',
                    'HTTP_X_FORWARDED',
                    'HTTP_X_CLUSTER_CLIENT_IP',
                    'HTTP_FORWARDED_FOR',
                    'HTTP_FORWARDED',
                    'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $IPaddress){
                    $IPaddress = trim($IPaddress); // Just to be safe

                    if (filter_var($IPaddress,
                                FILTER_VALIDATE_IP,
                                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
                        !== false) {

                        return $IPaddress;
                    }
                }
            }
        }
    }

    $ip = get_IP_address();
    $loc = file_get_contents("http://ip-api.com/json/$ip");
    $loc_o = json_decode($loc);
    $country = $loc_o->country;

    if (isset($country)) {
        $_SESSION['country'] = $country;
    }

    // Referrer
    if (!isset($_SESSION['referrer'])) {
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $_SESSION['referrer'] = !empty($referrer) ? $referrer : 'direct';
    }

    // Site Visit
    if (!isset($_SESSION['visit_insertion_done'])) {
        $guestID = $_SESSION['guestID'];
        $device  = $_SESSION['deviceType'];
        $country = $_SESSION['country'];
        $source  = $_SESSION['referrer'];

        $query = "INSERT INTO session(device_type, source, status, locationFrom) VALUES ('$device', '$source', '0', '$country')";
        $result = mysqli_query($conn, $query);
        if ($result) {
            $_SESSION['visit_insertion_done'] = true;
            $_SESSION['last_session_id'] = mysqli_insert_id($conn);
        }
    }
    // Check if user dropped off the quiz
    if (isset($_SESSION['quizProgress'])) {
        if (!isset($_SESSION['drop_insertion_done'])) {
            $guestID = $_SESSION['guestID'];
            $device  = $_SESSION['deviceType'];
            $country = $_SESSION['country'];
            $source  = $_SESSION['referrer'];
            $lastID  = $_SESSION['last_session_id'];
    
            $query  = "SELECT * FROM session WHERE sessionID = '$lastID'";
            $result = mysqli_query($conn, $query);
    
            if(mysqli_num_rows($result) > 0){
                $query = "UPDATE session
                        SET status = '1'
                        WHERE sessionID = '$lastID' AND guestID = '$guestID'";

                $result = mysqli_query($conn, $query);

                if ($result) {
                    $_SESSION['drop_insertion_done'] = true;
                }
            } else {
                $query = "INSERT INTO session(guestID, device_type, source, status, locationFrom) VALUES ('$guestID', '$device',  '$source', '1', '$country')";
                $result = mysqli_query($conn, $query);
    
                if ($result) {
                    if(isset($_SESSION['last_session_id'])){
                        unset($_SESSION['last_session_id']);
                        $_SESSION['last_session_id'] = mysqli_insert_id($conn);
                    }
                    $_SESSION['drop_insertion_done'] = true;
                }
            }
        }
        unset($_SESSION['quizProgress']);
        unset($_SESSION['selectedAnswers']);
        unset($_SESSION['currentQuestion']);
        unset($_SESSION['productTally']);
    }
    

    // Category Session (DO NOT TOUCH)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $_SESSION['selectedCategory'] = $_POST['category'];
        header("Location: category-page.php");
        exit();
    }

    $sql = "SELECT text FROM `texts` WHERE id = 'home-title'";
    $result = $conn->query($sql);

    // Check if a result was found
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['text'];    // Use $text variable containing the text
    }
?>
<div class="body-wrapper bg2">
    <div class="wrapper">
        <div class="spacer"></div>
        <div class="header-container">
            <h1 id="home-title"><?php echo $title; ?></h1>
        </div>
        <div class="spacer"></div>
        <div class="options-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form" method="post">
                <?php
                $categoryQuery = "SELECT * FROM category";
                $categoryResult = mysqli_query($conn, $categoryQuery);

                while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                    $categoryID = $categoryRow['categoryID'];
                    $categoryName = $categoryRow['categoryName'];
                    ?>
                    <div class="buttons">
                        <button type="submit" name="category" value="<?php echo $categoryID; ?>">
                            <?php echo $categoryName; ?>
                        </button>
                    </div>
                <?php
                }
                ?>
            </form>
        </div>
    </div>
</div>