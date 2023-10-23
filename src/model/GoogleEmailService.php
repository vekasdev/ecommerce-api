<?php

namespace App\model;
use App\interfaces\CodeValidationSenderInterface;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;


// it is must be GoogleEmailService 
// and if there is another class and another service it must be with its name
// as well as it is implements CodeValidationSenderInterface
class GoogleEmailService implements CodeValidationSenderInterface {
    private Email $email ;
    private TransportInterface $transporter;

    function __construct() {}
    function send() :void {
        $EEmail = new Mailer($this->transporter);
        $EEmail->send($this->email);
    }

    function setEmail(Email $email) {
        $this->email = $email;
    }

    function setTransporter(TransportInterface $transporter) {
        $this->transporter = $transporter;
    }

}