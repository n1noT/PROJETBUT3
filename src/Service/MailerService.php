<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $emailTo, string $eventName, string $eventDate): Response
    {
        if (!$emailTo) {
            return new Response('No email provided');
        }

        if (!filter_var($emailTo, FILTER_VALIDATE_EMAIL)) {
            return new Response('Invalid email');
        }

        $email = (new TemplatedEmail())
            ->from('smtpmmi@gmail.com')
            ->to(new Address($emailTo))
            ->subject('You\'ve been invited to an event!')

            // path of the Twig template to render
            ->htmlTemplate('emails/addToEvent.html.twig')

            // change locale used in the template, e.g. to match user's locale
            ->locale('de')

            // pass variables (name => value) to the template
            ->context([
                'event_name' => $eventName,
                'event_date' => $eventDate,
            ])
;

        $this->mailer->send($email);

        return new Response('Email sent');
    }
}