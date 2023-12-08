<?php
include_once 'databaseConnect.php';

// Fetch all comments from the 'comments' table
$sql = "SELECT * FROM comments";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<tr>";
    echo "<th>Comment ID</th>";
    echo "<th>User ID</th>";
    echo "<th>Comment</th>";
    echo "<th>Contact</th>";
    echo "<th>Contact Type</th>";
    echo "<th>Posted On</th>";
    echo "</tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["commentID"] . "</td>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["comment"] . "</td>";
        echo "<td>" . $row["contact"] . "</td>";
        echo "<td>" . $row["contactType"] . "</td>";
        echo "<td>" . $row["postedOn"] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No comments found</td></tr>";
}
$con->close();
?>