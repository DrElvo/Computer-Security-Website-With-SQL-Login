<?php
    session_start();
    $_SESSION['sessionToken'] = bin2hex(random_bytes(32));

    if(!isset($_SESSION['loggedin'], $_SESSION['id'])){
        header('Location: index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Request Evaluation</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>

<body onload="intialise_page()">

<section id="RequestEval">
    <h1>Request Evaluation: <a href="home.php">Home</a></h1>
    <form action="requestEvaluation.php" method="post" enctype="multipart/form-data" id="commentForm">
        <label for="comment">Please comment your evaluation request: </label>
        <input type="hidden" name= "sessionToken" value = "<?php echo isset($_SESSION['sessionToken']) ? $_SESSION['sessionToken'] : ''; ?>" >
        <input type="text" id="comment" name="comment" placeholder="Comment" onkeyup='check();' required>
        <br><br>
        <label for="contact">Select Input Type:</label>
        <select id="contact" name="contact">
            <option value="email">Email</option>           
            <option value="phone">Phone Number</option>
        </select>
        <br><br>
        <input type="file" name="fileToUpload" id="fileToUpload">
        <br><br>
        <button type="submit" id="submit">Request Evaluation</button>
        <span id='message'></span>
    </form>
</section>
</body>

<script>

    function intialise_page() {
        confirmed()
        check();
    }

    function check()  {

        var comment = document.getElementById('comment').value;

        if (comment == '') {
            document.getElementById('message').style.color = 'red';
            document.getElementById('message').innerHTML = 'empty fields';
            submit.disabled = true;
        } else{
            document.getElementById('message').style.color = 'green';
            document.getElementById('message').innerHTML = 'fields full';
            submit.disabled = false;
        }
    }

    function confirmed() {

    const urlParams = new URLSearchParams(window.location.search);
    const incorrect = urlParams.get('incorrect');

    if (incorrect === '1') {
        alert('Your username or password were incorrect');
    } else if(incorrect === '2') {
        alert('Please fill both the username and password fields!');
    }
    urlParams.delete('incorrect');
        const newURL = window.location.pathname + '?' + urlParams.toString();
        history.replaceState(null, '', newURL);

    }

</script>
</html>