<?php
// Include the database connection
include('database.php');

// Start the session to check if the user is logged in
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Initialize filters
$selected_action = isset($_POST['action']) ? mysqli_real_escape_string($connection, $_POST['action']) : 'All';
$selected_date = isset($_POST['date']) ? mysqli_real_escape_string($connection, $_POST['date']) : '';
$selected_product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : '';
$selected_start_hour = isset($_POST['start_hour']) ? intval($_POST['start_hour']) : '';
$selected_end_hour = isset($_POST['end_hour']) ? intval($_POST['end_hour']) : '';

// Check if the reset button was clicked
if (isset($_POST['reset_filters'])) {
    $selected_action = 'All';
    $selected_date = '';
    $selected_product_id = '';
    $selected_start_hour = '';
    $selected_end_hour = '';
}

// Create SQL query based on filters
$query = "SELECT * FROM transaction_log WHERE 1=1";

// Filter by action if selected
if ($selected_action !== 'All') {
    $query .= " AND action = '$selected_action'";
}

// Filter by date if selected
if (!empty($selected_date)) {
    $query .= " AND DATE(date) = '$selected_date'";
}

// Filter by product ID if selected
if (!empty($selected_product_id)) {
    $query .= " AND product_id = $selected_product_id";
}

// Filter by time range if selected
if (!empty($selected_start_hour) && !empty($selected_end_hour)) {
    $query .= " AND HOUR(date) BETWEEN $selected_start_hour AND $selected_end_hour";
}

// Order by date (latest first)
$query .= " ORDER BY date DESC";

// Execute the query
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Log</title>
    <link rel="stylesheet" href="transaction_log.css">
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
        <h1>Transaction Log</h1>

        <!-- Filter Form -->
        <form method="post" action="">
            <label for="date">Select Date:</label>
            <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($selected_date); ?>">

            <label for="start_hour">Start Hour:</label>
            <input type="number" name="start_hour" id="start_hour" min="0" max="23" value="<?php echo htmlspecialchars($selected_start_hour); ?>" placeholder="0-23">

            <label for="end_hour">End Hour:</label>
            <input type="number" name="end_hour" id="end_hour" min="0" max="23" value="<?php echo htmlspecialchars($selected_end_hour); ?>" placeholder="0-23">

            <label for="product_id">Product ID:</label>
            <input type="number" name="product_id" id="product_id" value="<?php echo htmlspecialchars($selected_product_id); ?>" placeholder="Enter Product ID">

            <label for="action">Filter by Action:</label>
            <select name="action" id="action">
                <option value="All" <?php echo $selected_action === 'All' ? 'selected' : ''; ?>>All</option>
                <option value="ADD" <?php echo $selected_action === 'ADD' ? 'selected' : ''; ?>>Add</option>
                <option value="UPDATE" <?php echo $selected_action === 'UPDATE' ? 'selected' : ''; ?>>Update</option>
                <option value="DELETE" <?php echo $selected_action === 'DELETE' ? 'selected' : ''; ?>>Delete</option>
            </select>

            <!-- Buttons for filtering and resetting -->
            <button type="submit" name="filter">Apply Filter</button>
            <button type="submit" name="reset_filters">Reset Filter</button>
        </form>

        <!-- Transaction Log Table -->
        <table>
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Date</th>
                    <th>Product ID</th>
                    <th>Action</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['transaction_id']; ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo $row['product_id']; ?></td>
                        <td><?php echo $row['action']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['details']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
