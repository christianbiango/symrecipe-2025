<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailService
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;
    public function __construct(MailerInterface $mailer){
        $this->mail = $mailer;
    }

    public function sendEmail(string $from, string $subject, string $htmlTemplate, array $context, string $to = 'admin@symrecipe.com'): void{
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($htmlTemplate)
            ->context($context);

        $this->mailer->send($email);
    }
}