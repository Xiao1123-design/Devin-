<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = floatval($_POST['price']);
    $condition = $conn->real_escape_string($_POST['condition']);
    $description = $conn->real_escape_string($_POST['description']);
    
    // Handle image upload
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
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check === false) {
            header("Location: sell.php?error=not_image");
            exit();
        }
    }
    
    // Check file size
    if ($_FILES["image"]["size"] > 5000000) {
        header("Location: sell.php?error=file_too_large");
        exit();
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        header("Location: sell.php?error=invalid_format");
        exit();
    }
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO products (seller_id, title, category, price, condition_status, description, image_path) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issdss", $_SESSION['user_id'], $title, $category, $price, $condition, $description, $target_file);
        
        if ($stmt->execute()) {
            header("Location: dashboard.php?success=product_listed");
        } else {
            header("Location: sell.php?error=db_error");
        }
    } else {
        header("Location: sell.php?error=upload_failed");
    }
}
?>
