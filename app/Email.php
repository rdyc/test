<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;


class Email
{
    protected $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        //Server settings
        //$this->mail->SMTPDebug = 2;                                 // Enable verbose debug output
        $this->mail->Mailer = 'smtp';                                      // Set mailer to use SMTP
        //$this->mail->sendMail = "C:\xampp\sendmail\sendmail.exe";
        //$this->mail->SMTPAutoTLS = false;
        $this->mail->Host = '172.16.20.69';  // Specify main and backup SMTP servers
        $this->mail->SMTPAuth = true;                               // Enable SMTP authentication
        $this->mail->Username = 'EKO.PRAYOGA';                 // SMTP username
        $this->mail->Password = 'EXIMBANK01!';                           // SMTP password
        $this->mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = 25;                                    // TCP port to connect to
        $this->mail->CharSet = 'utf-8';

        //Recipients
        $this->mail->setFrom('cis.support@ieb.go.id', 'ABC');
        $this->mail->addAddress('eko.prayoga@ieb.go.id', 'Test User');     // Add a recipient
        //$this->mail->addAddress('ellen@example.com');               // Name is optional
        $this->mail->addReplyTo('info@example.com', 'Information');
        //$this->mail->addCC('cc@example.com');
        //$this->mail->addBCC('bcc@example.com');

        //Attachments
        //$this->mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$this->mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        $this->send();
    }

    function send()
    {
        echo "Connecting SMTP server -> ";

        try {
            //Content
            $this->mail->isHTML(true);                                  // Set email format to HTML
            $this->mail->Subject = 'Here is the subject';
            $this->mail->Body = 'This is the HTML message body <b>in bold!</b>';
            $this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $this->mail->send();
            echo "Message has been sent\n";
        } catch (\Exception $e) {
            echo "Message could not be sent. Mailer Error: " . $this->mail->ErrorInfo ."\n";
        }
    }
}