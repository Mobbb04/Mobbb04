<?php
// Include the database connection
include('database.php');

// Start the session to check if the user is logged in
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Initialize variables for error messages and product data
$errors = [];
$product = null;

// Check if the form is submitted
if (isset($_POST['update_product'])) {
    // Get the form data
    $productId = intval($_POST['product_id']);
    $productName = mysqli_real_escape_string($connection, $_POST['product_name']);
    $category = mysqli_real_escape_string($connection, $_POST['category']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);

    // Validate the inputs
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

    // Fetch old product data for comparison
    $old_product_query = "SELECT * FROM products WHERE product_id = $productId";
    $old_product_result = mysqli_query($connection, $old_product_query);

    if ($old_product_result) {
        $old_product = mysqli_fetch_assoc($old_product_result);
    } else {
        echo "<script>alert('Error: " . mysqli_error($connection) . "');</script>";
        exit();
    }

    // If there are no errors, proceed with updating the product
    if (empty($errors)) {
        // Prepare the SQL query
        $query = "UPDATE products 
                  SET product_name = '$productName', category = '$category', quantity = $quantity, price = $price
                  WHERE product_id = $productId";

        // Execute the query
        if (mysqli_query($connection, $query)) {
            // Log the transaction
            $action = 'UPDATE';
            $details = '';

            // Add price change to details if price has changed
            if ($price !== $old_product['price'] && $quantity !== $old_product['quantity']) {
                $details .= "Update product: $productName | ";
            } 

            // Generate details based on changes
            if ($productName !== $old_product['product_name'] && $category !== $old_product['category']) {
                $details .= "Name and category changed: $old_product[product_name] to $productName, $old_product[category] to $category: ";
            } elseif ($productName !== $old_product['product_name']) {
                $details .= "Name changed from $old_product[product_name] to $productName: ";
            } elseif ($category !== $old_product['category']) {
                $details .= "Category changed from $old_product[category] to $category: ";
            }

            

            $log_query = "INSERT INTO transaction_log (date, product_id, action, quantity, price, details)
                          VALUES (NOW(), $productId, '$action', $quantity, $price, '$details')";
            mysqli_query($connection, $log_query);

            echo '<script>alert("Product updated successfully!"); window.location.href = "inventory.php";</script>';
        } else {
            echo "<script>alert('Error: " . mysqli_error($connection) . "');</script>";
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}

// Fetch the product data if an ID is provided
if (isset($_GET['id'])) {
    $productId = intval($_GET['id']);
    $query = "SELECT * FROM products WHERE product_id = $productId";
    $result = mysqli_query($connection, $query);

    if ($result) {
        $product = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Error: " . mysqli_error($connection) . "');</script>";
        exit();
    }
} else {
    echo "<script>alert('Product ID is missing.'); window.location.href = 'inventory.php';</script>";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="edit.css">
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
        <h1>Edit Product</h1>

        <form action="" method="post">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">

            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>

            <label for="category">Category:</label>
            <select name="category" id="category" class="category-select" required>
                <option value="Electronics" <?php echo $product['category'] === 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
                <option value="Food & Beverages" <?php echo $product['category'] === 'Food & Beverages' ? 'selected' : ''; ?>>Food & Beverages</option>
                <option value="School Supplies" <?php echo $product['category'] === 'School Supplies' ? 'selected' : ''; ?>>School Supplies</option>
                <option value="Clothing & Apparel" <?php echo $product['category'] === 'Clothing & Apparel' ? 'selected' : ''; ?>>Clothing & Apparel</option>
                <option value="Others" <?php echo $product['category'] === 'Others' ? 'selected' : ''; ?>>Others</option>
            </select>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" min="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>

            <button type="submit" name="update_product">Update Product</button>
        </form>
    </div>
</body>
</html>
