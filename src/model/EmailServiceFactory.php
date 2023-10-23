<?php
namespace App\model;
use GoogleEmailService;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;

class EmailServiceFactory {
    function makeSmtpGoogleTransporterMailer(
        $email,
        $password
    ) {
        $transporter = new GmailSmtpTransport(
            $email,
            $password
        );
        $service = new \App\model\GoogleEmailService();
        $service->setTransporter($transporter);
        return $service;
    }
}