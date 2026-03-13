<?php
require 'config.php';

// --- HANDLE DELETE ---
if (isset($_GET['delete'])) {
    $prod_id = $_GET['delete'];
    // Optional: Delete the physical file here too
    $stmt = $pdo->prepare("DELETE FROM products WHERE prod_id = ?");
    $stmt->execute([$prod_id]);
    header("Location: landing.php");
    exit();
}

// --- HANDLE INSERT ---
if (isset($_POST['add'])) {
    $name = $_POST['prod_name'];
    $desc = $_POST['description'];
    $cat  = $_POST['cat_id'];
    $brand = $_POST['brand_id'];

    $fileName = $_FILES['image']['name'];
    $tempName = $_FILES['image']['tmp_name'];
    $folder = "uploads/" . time() . "_" . $fileName; // Added timestamp to prevent duplicate names

    if (move_uploaded_file($tempName, $folder)) {
        $stmt = $pdo->prepare("INSERT INTO products (prod_name, description, cat_id, brand_id, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $cat, $brand, $folder]);
        echo "<script>alert('Product added successfully!');</script>";
    } else {
        echo "<script>alert('Upload failed. Check if uploads folder exists.');</script>";
    }
}

// --- SELECT DATA WITH JOIN ---
// This retrieves the names from the related tables instead of just IDs
$sql = "SELECT p.*, c.cat_name, b.brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.cat_id = c.cat_id 
        LEFT JOIN brands b ON p.brand_id = b.brand_id";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Product Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding: 20px; }
        .main-container { display: flex; gap: 20px; }
        .form-section { background: #014421; color: white; padding: 20px; border-radius: 8px; width: 400px; }
        .table-section { flex-grow: 1; background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd; }
        img { border-radius: 4px; object-fit: cover; }
    </style>
</head>
<body>

<h2 class="text-center mb-4">Tech Management System</h2>

<div class="main-container">
    <div class="form-section">
        <h3>Add New Product</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-2">
                <label>Product Name:</label>
                <input type="text" name="prod_name" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>Description:</label>
                <textarea name="description" class="form-control" rows="2" required></textarea>
            </div>
            <div class="mb-2">
                <label>Category:</label>
                <select name="cat_id" class="form-control">
                    <option value="1">Smartphone</option>
                    <option value="2">Laptop</option>
                    <option value="3">Tablet</option>
                </select>
            </div>
            <div class="mb-2">
                <label>Brand:</label>
                <select name="brand_id" class="form-control">
                    <option value="1">Apple</option>
                    <option value="2">Samsung</option>
                    <option value="3">Lenovo</option>
                    <option value="4">Dell</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Product Image:</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" name="add" class="btn btn-success w-100">Save Product</button>
        </form>
    </div>

    <div class="table-section">
        <h3>Product List</h3>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><img src="<?= $p['image_path'] ?>" width="60" height="60"></td>
                    <td><?= htmlspecialchars($p['prod_name']) ?></td>
                    <td><?= htmlspecialchars($p['cat_name']) ?></td>
                    <td><?= htmlspecialchars($p['brand_name']) ?></td>
                    <td><small><?= htmlspecialchars($p['description']) ?></small></td>
                    <td>
                        <a href="?delete=<?= $p['prod_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>