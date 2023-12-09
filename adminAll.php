<?php
include_once 'databaseConnect.php';

$sql = "SELECT * FROM comments";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    echo "<table id='commentsTable'>";
    echo "<tr>";
    echo "<th>Comment ID</th>";
    echo "<th>User ID</th>";
    echo "<th>Comment</th>";
    echo "<th>Contact</th>";
    echo "<th>Posted On</th>";
    echo "<th>Image</th>";
    echo "</tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["commentID"] . "</td>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["comment"] . "</td>";
        echo "<td>" . $row["contact"] . "</td>";
        echo "<td>" . $row["postedOn"] . "</td>";
        echo "<td>";
        $imagePath = $row["nameOfFile"];
        if (file_exists($imagePath)) {
            echo "<img src='" . $imagePath . "' width='100' height='100' />";
        } else {
            echo "No image available";
        }
        echo "</td>";

        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No comments found</p>";
}
$con->close();
?>