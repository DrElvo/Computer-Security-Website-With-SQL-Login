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

<section id="signup">
    <h1>Sign Up: <a href="loginHTML.php">Login</a> <a href="index.php">Home</a></h1>
    <form action="signup.php" method="post" id="signupForm">
        <input type="hidden" name= "sessionToken" value = "<?php echo isset($_SESSION['sessionToken']) ? $_SESSION['sessionToken'] : ''; ?>" >
        <input type="text" id="username_signup" name="username_signup" placeholder="Username" onkeyup='test_page();' required>
        <br></br>
        <input type="email" id="email" name="email" placeholder="Email" onkeyup='test_page();' required>
        <br></br>
        <input type="phoneNumber" id="phoneNumber" name="phoneNumber" placeholder="Phone Number" onkeyup='test_page();' required>
        <br></br>
        <input type="password" name="password_signup" id="password_signup"  placeholder="Password" onkeyup='test_page();' required/>
        <br></br>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" onkeyup='test_page();' required/>
        <br></br>
        <span id='message_signup'></span>
        <br></br>
        <span id='passwordStrength'></span> 
        <br></br>
        <input type="hidden" name="token" id="token"> 
        <button type="button" id="submit_signup" onclick="getRecaptchaToken()">Sign Up</button>
    </form>
</section>

</body>

<script>

  function getRecaptchaToken() {
        grecaptcha.ready(function () {
            grecaptcha.execute('6LdrRiIpAAAAAPNvdZx84VErn6h5RD-E0aPRVbpx', { action: 'submit' }).then(function (token) {
                formAction = 'signup.php'
                document.getElementById('token').value = token;
                document.getElementById('submit_signup').formAction = formAction;
                document.getElementById('submit_signup').form.submit();
            });
        });
    }

    function intialise_page() {
        confirmed()
        check_signup();
        password_strength();
    }

    function test_page() {
        check_signup();
        password_strength();
    }

    var check_signup = function() {
        var username = document.getElementById('username_signup').value;
        var email = document.getElementById('email').value;
        var password = document.getElementById('password_signup').value;
        var confirmPassword = document.getElementById('confirm_password').value;

        if (username === '' || email == '' || password == '' || confirmPassword.value == '' ) {
            document.getElementById('message_signup').style.color = 'red';
            document.getElementById('message_signup').innerHTML = 'empty fields';
            document.getElementById('submit_signup').disabled = true;
        } else if (password != confirmPassword) {
            document.getElementById('message_signup').style.color = 'yellow';
            document.getElementById('message_signup').innerHTML = 'not matching';
            document.getElementById('submit_signup').disabled = true;
        } else {
            document.getElementById('message_signup').style.color = 'green';
            document.getElementById('message_signup').innerHTML = 'fields full';
            document.getElementById('submit_signup').disabled = false;
        }
    }

    var password_strength = function() {
        const userInput = document.getElementById('password_signup').value;

        const lengthRegex = /^.{8,}$/; // Checks for a minimum length of 8 characters
        const numberRegex = /\d/;      // Checks for at least one digit
        const specialCharRegex = /[!@#$%^&*()_+[\]{};':"\\|,.<>/?-]/; // Checks for special characters

        const isLengthValid = lengthRegex.test(userInput);
        const hasNumber = numberRegex.test(userInput);
        const hasSpecialChar = specialCharRegex.test(userInput);

        let message = '';
        let color = '';

        if (!isLengthValid) {
            message = 'Password should be at least 8 characters long.';
            color = 'red';
        } else if (!hasNumber) {
            message = 'Password should contain at least one digit.';
            color = 'orange';
        } else if (!hasSpecialChar) {
            message = 'Password should contain at least one special character.';
            color = 'yellow';
        } else {
            message = 'Password meets complexity requirements.';
            color = 'green';
        }

        const passwordStrength = document.getElementById('passwordStrength');
        passwordStrength.textContent = message;
        passwordStrength.style.color = color;
        }

    function confirmed() {

        const urlParams = new URLSearchParams(window.location.search);
        const incorrect = urlParams.get('incorrect');
        const passfail = urlParams.get('passfail');

        if (incorrect === '1') {
            alert('Username already exists. Please choose a different username.');
        } else if(incorrect === '2') {
            alert('Email already linked.');
        }
            urlParams.delete('incorrect');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);

        if (passfail === '1') {
            alert('Username already exists. Please choose a different username.');
            urlParams.delete('incorrect');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }
    }

</script>
</html>