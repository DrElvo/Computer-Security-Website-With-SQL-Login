<?php
function captcha(){
    $reCaptchaSecretKey = '6LdrRiIpAAAAAPp850fkuM1Hz7UgxifNGt7tX3Hk';
    if (!isset($_POST['token']) || empty($_POST['token'])) {
        exit('error: reCAPTCHA token missing or empty');
    }
    $token = $_POST['token'];
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($url . '?secret=' . $reCaptchaSecretKey . '&response=' . $token);
    $result = json_decode($response);

    if (!$result->success) {
        exit('error: reCAPTCHA verification failed: ' . $response);
    }
}
?>