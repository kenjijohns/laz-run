<?php
// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file'])) {
    // Define the target directory where you want to save the uploaded file
    $targetDirectory = "../assets/images/";

    // Get the filename and temporary location of the uploaded file
    $filename = $_FILES['file']["name"];
    $tempFilePath = $_FILES['file']["tmp_name"];

    // Check if the file was successfully uploaded
    if (move_uploaded_file($tempFilePath, $targetDirectory . $filename)) {
        // File uploaded successfully
        $fileUrl = 'url("./images/' . $filename . '")';
        // Return the file URL as JSON response
        echo json_encode(['success' => true, 'fileUrl' => $fileUrl]);
    } else {
        // Error uploading file
        echo json_encode(['success' => false, 'message' => 'Error uploading file.']);
    }
} else {
    // No file uploaded
    echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
}
?>
