<!-- gqT)Yfq-r2_fJTG -->
<!-- equasar -->
<?php
error_reporting(-1);
ini_set('display_errors', 'On');
//set_error_handler("var_dump");


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";

    $name = $_POST['name'];
    $email = $_POST['email'];
    $topic = $_POST['topic'];
    $message2 = $_POST['message'];

    // the message
    $msg = "New contact form request - <br/>Name: $name<br/>Email: $email<br/>Topic: $topic<br/>Message: $message2";
    //$msg = "hiiiii";

    // use wordwrap() if lines are longer than 70 characters
    $msg = wordwrap($msg,70);

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // More headers
    $headers .= 'From: <equasarsolutions@gmail.com>' . "\r\n";
    // $headers .= 'Cc: equasarsolutions@gmail.com' . "\r\n";

    // echo mail("equasarsolutions@gmail.com","SMRelations",$msg,$headers);
$mail = new PHPMailer();
$mail->IsSMTP();
$mail->Mailer = "smtp";
$mail->SMTPDebug  = 1;
$mail->SMTPAuth   = TRUE;
$mail->SMTPSecure = "tls";
$mail->Port       = 587;
$mail->Host       = "smtp.gmail.com";
$mail->Username   = "equasarsolutions@gmail.com";
$mail->Password   = "drsuruatzqdyxwoi";

$mail->IsHTML(true);
$mail->AddAddress("melissa@smrelations.com","SMRelations");
$mail->SetFrom("equasarsolutions@gmail.com", "eQ");
$mail->AddReplyTo("equasarsolutions@gmail.com", "eQ");
$mail->AddBCC("mpsm@equasar.com", "eQuasar");
$mail->Subject = "SMRelations - Contact Form - ".$_POST['name'];
$content = $msg;


$mail->MsgHTML($content); 
if(!$mail->Send()) {
  echo "Error while sending Email.";
  var_dump($mail);
} else {
  echo "Email sent successfully";
}

?>
