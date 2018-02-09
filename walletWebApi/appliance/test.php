<?php 

$con = mysqli_connect("localhost","root","ubuntu@2017","warrantyWallet")or die("error");


$to = 'ak4abhilash@gmail.com';
$subject = 'Marriage Proposal';
$from = 'abhilash@onbvcorp.com';
 

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: '.$from."\r\n".'Reply-To: '.$from."\r\n";
 
// Compose a simple HTML email message
$message = '<html><body>';
$message .= '<h1 style="color:#f40;">Hi Jane!</h1>';
$message .= '<p style="color:#080;font-size:18px;">Will you marry me?</p>';
$message .= '</body></html>';
 
// Sending email
if(mail($to, $subject, $message, $headers)){
    echo 'Your mail has been sent successfully.';
} else{
    echo 'Unable to send email. Please try again.';
}



?>
