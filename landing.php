<?php
require 'config.php';

// --- HANDLE DELETE ---
if (isset($_GET['delete'])) {
    $prod_id = $_GET['delete'];
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
    $folder = "uploads/" . time() . "_" . $fileName;

    if (move_uploaded_file($tempName, $folder)) {
        $stmt = $pdo->prepare("INSERT INTO products (prod_name, description, cat_id, brand_id, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $cat, $brand, $folder]);
        echo "<script>alert('Product added successfully!');</script>";
    } else {
        echo "<script>alert('Upload failed.');</script>";
    }
}

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
    <style>
        /* --- CUSTOM CSS DESIGN --- */
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f4f4; 
            margin: 0; 
            padding: 20px; 
        }
        
        h2 { text-align: center; color: #333; }

        .main-container { 
            display: flex; 
            justify-content: center; 
            gap: 20px; 
            margin-top: 20px;
        }

        /* Form Design */
        .form-section { 
            background: #014421; /* Green color from your original */
            color: white; 
            padding: 25px; 
            border-radius: 10px; 
            width: 350px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .form-section h3 { margin-top: 0; border-bottom: 1px solid #fff; padding-bottom: 10px; }

        label { display: block; margin-top: 10px; font-weight: bold; }

        input[type="text"], textarea, select, input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: none;
            box-sizing: border-box; /* mahalaga para hindi lumagpas ang width */
        }

        .btn-save {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn-save:hover { background-color: #218838; }

        /* Table Design */
        .table-section { 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            border: 1px solid #ddd;
            flex-grow: 1;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }

        table, th, td { border: 1px solid #ddd; }

        th { background-color: #333; color: white; padding: 12px; text-align: left; }

        td { padding: 10px; vertical-align: middle; }

        tr:nth-child(even) { background-color: #f9f9f9; }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 12px;
        }

        .btn-delete:hover { background-color: #c82333; }

        img { border-radius: 5px; object-fit: cover; border: 1px solid #ddd; }

    </style>
</head>
<body>

<h2>Tech Management System</h2>

<div class="main-container">
    <div class="form-section">
        <h3>Add New Product</h3>
        <form method="POST" enctype="multipart/form-data">
            <label>Product Name:</label>
            <input type="text" name="prod_name" required>

            <label>Description:</label>
            <textarea name="description" rows="3" required></textarea>

            <label>Category:</label>
            <select name="cat_id">
                <option value="1">Smartphone</option>
                <option value="2">Laptop</option>
                <option value="3">Tablet</option>
            </select>

            <label>Brand:</label>
            <select name="brand_id">
                <option value="1">Apple</option>
                <option value="2">Samsung</option>
                <option value="3">Lenovo</option>
                <option value="4">Dell</option>
            </select>

            <label>Product Image:</label>
            <input type="file" name="image" accept="image/*" required>

            <button type="submit" name="add" class="btn-save">Save Product</button>
        </form>
    </div>

    <div class="table-section">
        <h3>Product List</h3>
        <table>
            <thead>
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
                    <td><b><?= htmlspecialchars($p['prod_name']) ?></b></td>
                    <td><?= htmlspecialchars($p['cat_name']) ?></td>
                    <td><?= htmlspecialchars($p['brand_name']) ?></td>
                    <td><small><?= htmlspecialchars($p['description']) ?></small></td>
                    <td>
                        <a href="?delete=<?= $p['prod_id'] ?>" class="btn-delete" onclick="return confirm('Sigurado ka ba?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
