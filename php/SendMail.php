<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 07.04.2016
 * Time: 19:13
 */
require_once '../lib/PHPMailer-master/class.phpmailer.php';

//Содаем новый объект
$mail = new PHPMailer;

//Smtp отладка
$mail->SMTPDebug=2;
$mail->Debugoutput='html';

//настройки почтового сервера
$mail->Host='mx.medline.spb.ru';
$mail->Port=25;
$mail->Username='alex2@medline.spb.ru';
$mail->Password='ale129ucanlqjnxa';
$mail->CharSet='UTF-8';

//От кого отправляется письмо
$mail->setFrom('alex2@medline.spb.ru','Alexander Golikov');
//Кому письмо
$mail->addAddress('trimserver@apteka.spb.ru','Medline Server');
//Тема
$mail->Subject='test phpmailer';
//Тело письма
$mail->Body = 'Some text in English. Текст на Русском.';
//Вложение к письму
$mail->addAttachment('../output/test.xlsx');




//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
?>

