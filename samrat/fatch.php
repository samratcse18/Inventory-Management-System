<?php
include('database_connection.php');

// Fetch data from users table
$sql = "SELECT * FROM inventory_order";
$result = $connect->query($sql);

// Output fetched data as HTML
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo '<tr>
            <td>' . $row["payment_status"] . '</td>
            <td>' . $row["inventory_order_status"] . '</td>
            <td>' . $row["inventory_order_id"] . '</td><td><a href="view_order.php?pdf=1&order_id=' . $row["inventory_order_id"] . '" class="btn btn-info btn-xs">View PDF</a></td>
        </tr>';
    }
    echo "</table>";
} else {
    echo "0 results";
}
$connect->close();
?>
