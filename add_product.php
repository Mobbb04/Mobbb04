<?php

include('database.php');


session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}


$errors = [];


if (isset($_POST['add_product'])) {

    $productName = mysqli_real_escape_string($connection, $_POST['product_name']);
    $category = mysqli_real_escape_string($connection, $_POST['category']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);


    if (empty($productName)) {
        $errors[] = "Product name is required.";
    }
    if (empty($category)) {
        $errors[] = "Category is required.";
    }
    if ($quantity <= 0) {
        $errors[] = "Quantity must be greater than zero.";
    }
    if ($price <= 0) {
        $errors[] = "Price must be greater than zero.";
    }


    if (empty($errors)) {

        $query = "INSERT INTO products (product_name, category, quantity, price)
        VALUES ('$productName', '$category', $quantity, $price)";


        if (mysqli_query($connection, $query)) {

        $product_id = mysqli_insert_id($connection);
        $action = 'ADD';
        $details = 'Added product ID ' . $productName;

        $log_query = "INSERT INTO transaction_log (date, product_id, action, quantity, price, details)
                VALUES (NOW(), $product_id, '$action', $quantity, $price, '$details')";
        mysqli_query($connection, $log_query);

        echo '<script>alert("Product added successfully!")</script>';
        } else {
        echo "Error: " . mysqli_error($connection);
        }

    } else {
        
        foreach ($errors as $error) {
            echo "<script>alert('$error')</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="add_product.css">
</head>
<body>
    <div class="navbar">
        <div class="icon">
            <img src="logo.png" alt="">
        </div>
        <div class="menu">
            <ul class="list">
                <li><a href="dashboard.php">DASHBOARD</a></li>
                <li><a href="inventory.php">INVENTORY</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </div>
    </div>

    <div class="content">
        <h1>Add New Product</h1>

        <form action="" method="post">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" required>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">Select a category</option>
                <option value="Electronics">Electronics</option>
                <option value="Food & Beverages">Food & Beverages</option>
                <option value="School Supplies">School Supplies</option>
                <option value="Clothing & Apparel">Clothing & Apparel</option>
                <option value="Others">Others</option>
            </select>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" min="0.01" required>

            <button type="submit" name="add_product">Add Product</button>
        </form>
    </div>
</body>
</html>
