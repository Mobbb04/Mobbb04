<?php
// Include the database connection
include('database.php');

// Start the session to check if the user is logged in
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Check if the product ID is provided
if (isset($_GET['id'])) {
    $productId = intval($_GET['id']);

    // Retrieve product information before deletion for logging
    $productQuery = "SELECT * FROM products WHERE product_id = $productId";
    $productResult = mysqli_query($connection, $productQuery);

    if (mysqli_num_rows($productResult) > 0) {
        $product = mysqli_fetch_assoc($productResult);

        // Log the transaction before deletion
        $action = 'DELETE';
        $details = 'Deleted product: ' . $product['product_name'];
        $quantity = $product['quantity']; // Log the quantity that was deleted
        $price = $product['price']; // Log the price of the deleted product

        $log_query = "INSERT INTO transaction_log (date, product_id, action, quantity, price, details)
                      VALUES (NOW(), $productId, '$action', $quantity, $price, '$details')";
        mysqli_query($connection, $log_query);

        // Now proceed to delete the product
        $deleteQuery = "DELETE FROM products WHERE product_id = $productId";

        if (mysqli_query($connection, $deleteQuery)) {
            echo '<script>alert("Product deleted successfully!"); window.location.href = "inventory.php";</script>';
        } else {
            echo "<script>alert('Error: " . mysqli_error($connection) . "'); window.location.href = 'inventory.php';</script>";
        }
    } else {
        echo "<script>alert('Product not found.'); window.location.href = 'inventory.php';</script>";
    }
} else {
    echo "<script>alert('Product ID is missing.'); window.location.href = 'inventory.php';</script>";
    exit();
}
?>
