<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $image_path = null;
    
    // Handle image upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;
        
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        // Check if image file is actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check === false) {
            header("Location: donations.php?error=not_image");
            exit();
        }
        
        // Check file size
        if ($_FILES["image"]["size"] > 5000000) {
            header("Location: donations.php?error=file_too_large");
            exit();
        }
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            header("Location: donations.php?error=invalid_format");
            exit();
        }
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }
    
    $sql = "INSERT INTO donations (donor_id, title, description, image_path) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $_SESSION['user_id'], $title, $description, $image_path);
    
    if ($stmt->execute()) {
        header("Location: donations.php?success=donation_posted");
    } else {
        header("Location: donations.php?error=post_failed");
    }
    
    $stmt->close();
}
$conn->close();
?>
