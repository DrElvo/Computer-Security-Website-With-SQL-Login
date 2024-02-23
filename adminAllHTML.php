<?php
session_start();

$_SESSION['sessionToken'] = bin2hex(random_bytes(32));

if (!(isset($_SESSION['loggedin'], $_SESSION['id']) && $_SESSION['loggedin'] === true)) {
    header('Location: index.php');
    exit('Invalid session');
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Display Comments</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <h1>All Comments: <a href="adminHome.php">Home</a></h1>
</head>

<body>
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
</body>

<script>
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("commentsTable").innerHTML = this.responseText;
        }
    };

    var sessionToken = "<?php echo $_SESSION['sessionToken']; ?>";

    xhttp.open("GET", "adminAll.php?sessionToken=" + sessionToken, true);
    xhttp.send();
</script>

</body>
</html>