<?php
require 'config.php';

// 1. GET THE EXISTING DATA
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE prod_id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Product not found!");
    }
}

// 2. HANDLE THE UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['prod_id'];
    $name = $_POST['prod_name'];
    $desc = $_POST['description'];
    $cat = $_POST['cat_id'];
    $brand = $_POST['brand_id'];
    $old_image = $_POST['old_image'];

    // Check if a new image was uploaded
    if (!empty($_FILES['new_image']['name'])) {
        $fileName = time() . "_" . $_FILES['new_image']['name'];
        $folder = "uploads/" . $fileName;
        move_uploaded_file($_FILES['new_image']['tmp_name'], $folder);
        $image_to_save = $folder;
        
        // Optional: Delete old physical file to save space
        if (file_exists($old_image)) { unlink($old_image); }
    } else {
        $image_to_save = $old_image; // Keep the existing image
    }

    $sql = "UPDATE products SET prod_name=?, description=?, cat_id=?, brand_id=?, image_path=? WHERE prod_id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $desc, $cat, $brand, $image_to_save, $id]);

    header("Location: landing.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

<div class="container bg-white p-4 shadow-sm" style="max-width: 600px; border-radius: 8px;">
    <h2>Edit Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="prod_id" value="<?= $product['prod_id'] ?>">
        <input type="hidden" name="old_image" value="<?= $product['image_path'] ?>">

        <div class="mb-3">
            <label>Product Name</label>
            <input type="text" name="prod_name" class="form-control" value="<?= htmlspecialchars($product['prod_name']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" required><?= htmlspecialchars($product['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label>Current Image</label><br>
            <img src="<?= $product['image_path'] ?>" width="100" class="mb-2 border">
            <input type="file" name="new_image" class="form-control" accept="image/*">
            <small class="text-muted">Leave blank to keep current image</small>
        </div>

        <div class="mb-3">
            <label>Category ID (1: Smart, 2: Lap, 3: Tab)</label>
            <input type="number" name="cat_id" class="form-control" value="<?= $product['cat_id'] ?>">
        </div>

        <div class="mb-3">
            <label>Brand ID (1: Apple, 2: Sam, 3: Len, 4: Dell)</label>
            <input type="number" name="brand_id" class="form-control" value="<?= $product['brand_id'] ?>">
        </div>

        <button type="submit" name="update" class="btn btn-primary">Update Product</button>
        <a href="landing.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>