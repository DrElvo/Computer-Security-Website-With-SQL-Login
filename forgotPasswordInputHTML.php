<?php
    session_start();
    $_SESSION['sessionToken'] = bin2hex(random_bytes(32));

    if(!isset($_SESSION['question'])){
        header('Location: index.php');
        exit('Invalid Session');
    } else {
        $question = $_SESSION['question'];
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forgot Password Fresh</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>

<body onload="firstCheck()">

    <form action="forgotPasswordInput.php" method="POST">
        <div>
            <section id="login">
                <h1>New Password:</h1>
                <label for="newPassword">Your New Password:</label>
                <input type="hidden" name= "sessionToken" value = "<?php echo isset($_SESSION['sessionToken']) ? $_SESSION['sessionToken'] : ''; ?>" >
                <input type="password" name="newPassword" id="newPassword"  placeholder="New Password" onkeyup='check();' required/>
                <span id='passwordStrength'></span> 
                <br></br>
                <label for="confirmpassword">Repeat Your Passwordr</label>
                <input type="password" name="confirmpassword" id="confirmpassword" placeholder="Confirm Password" onkeyup='check();' required/>
                <span id='passwordMatch'></span> 
                <br></br>
                <label>Your question is: <?php echo $question; ?></label>
                <br></br>
                <label for="answer">Your Answer:</label>
                <input type="text" name="answer" id="answer" placeholder="Answer" onkeyup='check();' required>
                <br></br>
                <button type="submit" id="submit">Submit</button>
                <span id='message'></span>
            </section>
        </div>
    </form>

    <script>

        function firstCheck(){

            document.getElementById('submit').disabled = true;

            emailLink()
            confirmed()
            password_strength()
            check_password()
            check_signup()
        }

        function check(){
            check_signup()
            if(password_strength() == true && check_password() == true && check_signup() == true ){
                document.getElementById('submit').disabled = false;
            }
            else{
                document.getElementById('submit').disabled = true;
            }
        }

        function emailLink() {
            
            const urlParams = new URLSearchParams(window.location.search);
            const codeVal = urlParams.get('passToken')
            
        }

        function check_signup() {

            const password = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmpassword').value;
            const answer = document.getElementById('answer').value;

            let isValid = false;

            if (newPassword == '' || confirmPassword.value == '' || answer == '') {
                document.getElementById('message').style.color = 'red';
                document.getElementById('message').innerHTML = 'empty fields';
                isValid = false;
            
            } else {
                document.getElementById('message').style.color = 'green';
                document.getElementById('message').innerHTML = 'fields full';
                isValid = true;
            }

            return isValid
        }

        function check_password() {
            
            const password = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmpassword').value;

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

        function password_strength() {

            const userInput = document.getElementById('newPassword').value;

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
        const passfail = urlParams.get('passfail');
        const passfailmatch = urlParams.get('passfailmatch');

        if (passfail === '1') {
            alert('Passwords dont match or do not meet security requiremnets');
            urlParams.delete('incorrect');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }
        if (passfailmatch === '1') {
            alert('Please match passwords');
            urlParams.delete('incorrect');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }
        }
    </script>
</body>



</html>