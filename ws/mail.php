GNU nano 6.2                                       mail.php                                                 
<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if (isset($_POST['send_email'])) {
    $to = 'martin.schiegl@geosphere.at';
    $subject = 'Update Notification';
    $txt= 'A request to update has been made';
    $headers = "From: gbaineo@ce-gic.org";

   mail($to,$subject,$txt,$headers);
}
?>




