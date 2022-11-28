<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService {

    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($from, $subject, $htmlTemplate, $context) {
        /** sending mail */
        $email = (new TemplatedEmail())
                ->from($from)
                ->to('ndiayeactu@gmail.com')
                ->subject($subject)
                ->htmlTemplate($htmlTemplate)
                ->context($context);

        $this->mailer->send($email);
        /** end sending mail */
    }

}