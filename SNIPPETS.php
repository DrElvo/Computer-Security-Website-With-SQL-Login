echo "<pre>";
print_r($_SESSION); // Use var_dump($_SESSION) for more detailed information
echo "</pre>";


echo "<pre>";
    print_r($_POST); // Use var_dump($_SESSION) for more detailed information
echo "</pre>";


For uploading to the 000webhostapp session, these pieces of code are used to change the email links for both account verification and for password reset
login.php
"http://suth443.000webhostapp.com/confirm.php?linked=1&id=$id&verificationCode=$verification_code";

signup.php
"http://suth443.000webhostapp.com/confirm.php?linked=1&id=$id&verificationCode=$verification_code";

forgotpasswordhome.php
$body = "http://suth443.000webhostapp.com/forgotPasswordInput.php?linked=1&id=$id&passToken=$passToken";

confirm.php
"http://suth443.000webhostapp.com/confirm.php?linked=1&id=$id&verificationCode=$verification_code";
