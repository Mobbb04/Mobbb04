<?php

session_start();
include('database.php');


if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}


$firstName = $_SESSION['firstName'];
$username = $_SESSION['username'];


$query = "SELECT category, product_name, quantity FROM products ORDER BY category";
$result = mysqli_query($connection, $query);

$productsByCategory = [];


while ($row = mysqli_fetch_assoc($result)) {
    $category = $row['category'];
    $productsByCategory[$category][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="navbar">
        <div class="icon">
            <img src="logo.png" alt="Logo">
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
        <h1>Welcome, <?php echo htmlspecialchars($firstName); ?>!</h1>
        <p>You are logged in as <?php echo htmlspecialchars($username); ?>.</p>
        <h2>Product Categories</h2>
        
        <div id="categories-container" class="container2">
        
            <div id="categories-container" class="categories-container">
                <?php
                
                $categories = ['Electronics', 'Food & Beverages', 'School Supplies', 'Clothing & Apparel', 'Others'];

                foreach ($categories as $category) {
                    if (isset($productsByCategory[$category])) {
                        echo "<div class='category-box'>";
                        echo "<h3>$category</h3>";
                        echo "<table class='category-table'>
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>";
                        foreach ($productsByCategory[$category] as $product) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($product['product_name']) . "</td>
                                    <td>" . htmlspecialchars($product['quantity']) . "</td>
                                </tr>";
                        }
                        echo "</tbody></table></div>";
                    }
                }
                ?>
            </div>
        </div>
        <button class="toggle-button">Toggle Categories</button>
    </div>

    <script>
        //pangjava Script
        document.querySelector('.toggle-button').addEventListener('click', function() {
            var container = document.getElementById('categories-container');
            if (container.style.display === 'none' || container.style.display === '') {
                container.style.display = 'flex';
            } else {
                container.style.display = 'none';
            }
        });
    </script>
</body>
</html>
