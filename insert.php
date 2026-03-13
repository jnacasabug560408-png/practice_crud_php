<?php
require 'config.php';

if(isset($_POST['add'])){
    $name = $_POST['prod_name'];
    $desc = $_POST['description'];
    $cat  = $_POST['cat_id'];
    $brand = $_POST['brand_id'];

    // IMAGE HANDLING
    $fileName = $_FILES['image']['name'];
    $tempName = $_FILES['image']['tmp_name'];
    $folder = "uploads/" . $fileName;

    // 1. Move the physical file to your 'uploads' folder
    if (move_uploaded_file($tempName, $folder)) {
        
        // 2. Save the details to the 'products' table
        $sql = "INSERT INTO products (prod_name, description, cat_id, brand_id, image_path) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $desc, $cat, $brand, $fileName]);

        echo "Product added and image uploaded!";
    } else {
        echo "Failed to upload image. Check if 'uploads' folder exists.";
    }
}
?>