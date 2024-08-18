<?php
// Include the database connection
include('database.php');

// Start the session to check if the user is logged in
session_start();

// Initialize search query and category filter variables
$search_query = '';
$selected_category = 'All';

// Check if a search term is submitted
if (isset($_POST['search'])) {
    $search_query = mysqli_real_escape_string($connection, $_POST['search_query']);
}

// Check if a category filter is selected
if (isset($_POST['category'])) {
    $selected_category = mysqli_real_escape_string($connection, $_POST['category']);
}

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Prepare the SQL query to fetch products based on search query and category filter
$query = "SELECT * FROM products WHERE (product_name LIKE '%$search_query%' OR category LIKE '%$search_query%')";

if ($selected_category !== 'All') {
    $query .= " AND category = '$selected_category'";
}

$query .= " ORDER BY product_id"; // Optionally sort by product_id or another column

$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="inventory.css">
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
        <h1>Inventory Management</h1>
        <div class="inventory-controls">
            <form action="inventory.php" method="post" class="search-form">
                <input type="text" name="search_query" placeholder="Search product..." value="<?php echo htmlspecialchars($search_query); ?>" class="search-input">
                <button type="submit" name="search" class="search-button">Search</button>
                
                <select name="category" id="category" onchange="this.form.submit()" class="category-select">
                    <option value="All" <?php echo $selected_category === 'All' ? 'selected' : ''; ?>>All</option>
                    <option value="Electronics" <?php echo $selected_category === 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
                    <option value="Food & Beverages" <?php echo $selected_category === 'Food & Beverages' ? 'selected' : ''; ?>>Food & Beverages</option>
                    <option value="School Supplies" <?php echo $selected_category === 'School Supplies' ? 'selected' : ''; ?>>School Supplies</option>
                    <option value="Clothing & Apparel" <?php echo $selected_category === 'Clothing & Apparel' ? 'selected' : ''; ?>>Clothing & Apparel</option>
                    <option value="Others" <?php echo $selected_category === 'Others' ? 'selected' : ''; ?>>Others</option>
                </select>
            </form>
            <button onclick="window.location.href='transaction_log.php'" class="add-button">Transaction Log</button>
            <button onclick="window.location.href='add_product.php'" class="add-button">Add New Product</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td>
                            <button onclick="window.location.href='edit.php?id=<?php echo $row['product_id']; ?>'">Edit</button>
                            <button onclick="window.location.href='delete.php?id=<?php echo $row['product_id']; ?>'">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
