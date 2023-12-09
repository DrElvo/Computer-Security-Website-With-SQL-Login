<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Display Comments</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<h1>All Comments: <a href="adminHome.php">Home</a></h1>

<table id="commentsTable">
    <tr>
        <th>Comment ID</th>
        <th>User ID</th>
        <th>Comment</th>
        <th>Contact</th>
        <th>Contact Type</th>
        <th>Posted On</th>
        <th>File Name</th>
        <th>Images</th>
    </tr>
</table>

<script>
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("commentsTable").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "adminAll.php", true);
    xhttp.send();
</script>

</body>
</html>