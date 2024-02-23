<?php

session_start();

include_once 'databaseConnect.php';

if(!isset($_SESSION['sessionToken'], $_GET['sessionToken']) || $_SESSION['sessionToken'] != $_GET['sessionToken']){
    header('Location: index.php');
    exit('Invalid Session');
}

$sql = "SELECT commentID, id, comment, contact, postedOn, nameOfFile FROM comments";
$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

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
        echo "<td>" . htmlspecialchars($row["commentID"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["comment"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["contact"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["postedOn"]) . "</td>";
        echo "<td>";
        $imagePath = $row["nameOfFile"];
        if (file_exists($imagePath)) {
            echo "<img src='" . htmlspecialchars($imagePath) . "' width='100' height='100' />";
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

$stmt->close();
$con->close();

?>