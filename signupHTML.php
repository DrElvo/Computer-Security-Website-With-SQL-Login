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


<body onload="firstCheck()">

<section id="signup">
    <h1>Sign Up: <a href="loginHTML.php">Login</a> <a href="index.php">Home</a></h1>
    <form action="signup.php" method="post" id="signupForm">
        <input type="hidden" name= "sessionToken" value = "<?php echo isset($_SESSION['sessionToken']) ? $_SESSION['sessionToken'] : ''; ?>" >
        <input type="text" id="username_signup" name="username_signup" placeholder="Username" onkeyup='check();' required>
        <br></br>
        <input type="email" id="email" name="email" placeholder="Email" onkeyup='check();' required>
        <br></br>
        <input type="tel" id="phoneNumber" name="phoneNumber" placeholder="Phone Number" pattern="\+?\d{1,13}" onkeyup='check();' required>
        <span id='numberValidity'></span> 
        <br></br>
        <input type="password" name="password_signup" id="password_signup"  placeholder="Password" onkeyup='check();' required/>
        <span id='passwordStrength'></span> 
        <br></br>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" onkeyup='check();' required/>
        <span id='passwordMatch'></span> 
        <br></br>
        <label for="securityQuestion">Select a Security Question:</label>
        <select id="securityQuestion" name="securityQuestion">
            <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
            <option value="What city were you born in?">What city were you born in?</option>
            <option value="What is the name of your first pet?">What is the name of your first pet?</option>
            <option value="What is your favorite movie?">What is your favorite movie?</option>
            <option value="What is your favorite book?">What is your favorite book?</option>
        </select>
        <br></br>
        <label for="answer">Your Answer:</label>
        <input type="text" name="answer" id="answer" placeholder="Answer" onkeyup='check();' required>
        <br></br>
        <input type="hidden" name="token" id="token">
        <button type="button" id="submit_signup" onclick="getRecaptchaToken()">Sign Up</button>
        <span id='message_signup'></span>
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

    function firstCheck(){
        document.getElementById('submit_signup').disabled = true;
        confirmed()
        check_signup()
        phone_valid()
        password_strength()
        check_password()
        check_signup()
    }

    function check(){
        check_signup()
        if(phone_valid() == true && password_strength() == true && check_password() == true && check_signup() == true ){
            document.getElementById('submit_signup').disabled = false;
        }
        else{
            document.getElementById('submit_signup').disabled = true;
        }
    }

    function check_signup() {

        const username = document.getElementById('username_signup').value;
        const email = document.getElementById('email').value;
        const phoneNumber = document.getElementById('phoneNumber').value;
        const password = document.getElementById('password_signup').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const answer = document.getElementById('answer').value;

        let isValid = false;

        if (username === '' || email == '' || password == '' || confirmPassword.value == '' || phoneNumber == '' || answer == '') {
            document.getElementById('message_signup').style.color = 'red';
            document.getElementById('message_signup').innerHTML = 'empty fields';
            isValid = false;
        
        } else {
            document.getElementById('message_signup').style.color = 'green';
            document.getElementById('message_signup').innerHTML = 'fields full';
            isValid = true;
        }

        return isValid
    }

    function check_password() {
        
        const password = document.getElementById('password_signup').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        let isValid = false;
        
        if (password != confirmPassword || password == '') {
            document.getElementById('passwordMatch').style.color = 'red';
            document.getElementById('passwordMatch').innerHTML = 'not matching or empty';
            isValid = false;
        } else {
            document.getElementById('passwordMatch').style.color = 'green';
            document.getElementById('passwordMatch').innerHTML = 'matching';
            isValid = true;
        }

        return isValid

    }

    function phone_valid() {
        const phoneNumber = document.getElementById('phoneNumber');
        phoneNumber.value = phoneNumber.value.replace(/[^0-9+]/g, '');
        const plusCount = (phoneNumber.value.match(/\+/g) || []).length;

        let isValid = false;    

        if (plusCount > 1 || (plusCount === 1 && phoneNumber.value.indexOf('+') !== 0)) {
            document.getElementById('numberValidity').style.color = 'red';
            document.getElementById('numberValidity').innerHTML = 'you can only have one + in your phone number';
            isValid = false;
        } else {
            document.getElementById('numberValidity').style.color = 'green';
            document.getElementById('numberValidity').innerHTML = 'valid';
            isValid = true;
        }

        if (phoneNumber.value.startsWith('+')) {
            if (phoneNumber.value.length > 13) {
                phoneNumber.value = phoneNumber.value.slice(0, 13);
            }
        } else {
            if (phoneNumber.value.length > 12) {
                phoneNumber.value = phoneNumber.value.slice(0, 12);
            }
        }

        return isValid
    }

    function password_strength() {

        const userInput = document.getElementById('password_signup').value;

        const lowerRegex = /[a-z]/
        const upperRegex = /[A-Z]/
        const lengthRegex = /^.{8,}$/; // Checks for a minimum length of 8 characters
        const numberRegex = /\d/;      // Checks for at least one digit
        const specialCharRegex = /[!@#$%^&*()_+[\]{};':"\\|,.<>/?-]/; // Checks for special characters

        const Lower = lowerRegex.test(userInput);
        const Upper = upperRegex.test(userInput);
        const Length = lengthRegex.test(userInput);
        const Number = numberRegex.test(userInput);
        const SpecialChar = specialCharRegex.test(userInput);

        let isValid = false; 

        if (!Length) {
            document.getElementById('passwordStrength').textContent = 'Password should be at least 8 characters long.';
            document.getElementById('passwordStrength').style.color = 'red';
            isValid = false;
        } else if (!Upper) {
            document.getElementById('passwordStrength').textContent = 'Password should contain at least one upper character.';
            document.getElementById('passwordStrength').style.color = 'red';
            isValid = false;
        }else if (!Lower) {
            document.getElementById('passwordStrength').textContent = 'Password should contain at least one lower character.';
            document.getElementById('passwordStrength').style.color = 'red';
            isValid = false;
        }else if (!Number) {
            document.getElementById('passwordStrength').textContent = 'Password should contain at least one digit.';
            document.getElementById('passwordStrength').style.color = 'yellow';
            isValid = false;
        } else if (!SpecialChar) {
            document.getElementById('passwordStrength').textContent = 'Password should contain at least one special character.';
            document.getElementById('passwordStrength').style.color = 'yellow';
            isValid = false;
        } else {
            document.getElementById('passwordStrength').textContent = 'Password meets complexity requirements.';
            document.getElementById('passwordStrength').style.color = 'green';
            isValid = true;
        }

        return isValid
    }

    function confirmed() {

        const urlParams = new URLSearchParams(window.location.search);

        const emailfail = urlParams.get('emailfail');
        const usefail = urlParams.get('usefail');
        const passfail = urlParams.get('passfail');

        if (emailfail === '1') {
            alert('Email already linked.');
            urlParams.delete('incorrect');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }
        if (usefail === '1') {
            alert('Username already exists. Please choose a different username.');
            urlParams.delete('usefail');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }
        if (passfail === '1') {
            alert('Password does not meet security requirements.');
            urlParams.delete('passfail');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }
    }

</script>
</html>