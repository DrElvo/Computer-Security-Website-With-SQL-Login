<?php
    session_start();
    $_SESSION['sessionToken'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <script src="https://www.google.com/recaptcha/api.js?render=6LdrRiIpAAAAAPNvdZx84VErn6h5RD-E0aPRVbpx"></script>
</head>


<body onload="intialise_page()">

<section id="login">
    <h1>Login: <a href="signupHTML.php">Signup</a> <a href="index.php">Home</a></h1>
    <form action="login.php" method="post" id="login">
        <input type="hidden" name= "sessionToken" value = "<?php echo isset($_SESSION['sessionToken']) ? $_SESSION['sessionToken'] : ''; ?>" >
        <input type="text" id="username_login"     name="username_login" placeholder="Username" onkeyup='check_login();' required>
        <input type="password" id="password_login" name="password_login" placeholder="Password" onkeyup='check_login();' required>
        <label for="password_login">
            <i class="fas fa-lock"></i>
        </label>                
        <input type="hidden" name="token" id="token">
        <button type="button" id="submit_login" onclick="getRecaptchaToken()">Login</button>
        <span id='message_login'></span>
    </form>
</section>
</body>

<script>
    function getRecaptchaToken() {
        grecaptcha.ready(function () {
            grecaptcha.execute('6LdrRiIpAAAAAPNvdZx84VErn6h5RD-E0aPRVbpx', { action: 'submit' }).then(function (token) {
                formAction = 'login.php'

                document.getElementById('token').value = token;
                document.getElementById('submit_login').formAction = formAction;
                document.getElementById('submit_login').form.submit();
            });
        });
    }

    function intialise_page() {
        confirmed()
        check_login();
    }

    function check_login()  {

    var username = document.getElementById('username_login').value;
    var password = document.getElementById('password_login').value; 

    if (username == '' || password == '') {
        document.getElementById('message_login').style.color = 'red';
        document.getElementById('message_login').innerHTML = 'empty fields';
        submit_login.disabled = true;
    } else{
        document.getElementById('message_login').style.color = 'green';
        document.getElementById('message_login').innerHTML = 'fields full';
        submit_login.disabled = false;
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