<?php

require_once 'vendor/autoload.php';

$transport = (new Swift_SmtpTransport('smtp.mailtrap.io', '2525'))
    ->setUsername('a3b4d70cb8c256')
    ->setPassword('065e7be4cd7377')
    ->setEncryption('tls');

$mailer = new Swift_Mailer($transport);
