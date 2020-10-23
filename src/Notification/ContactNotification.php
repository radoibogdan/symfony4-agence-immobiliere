<?php

namespace App\Notification;

use App\Entity\Contact;
use Swift_Mailer;
use Twig\Environment;

class ContactNotification {
    /**
     * @var SwiftMailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $renderer;

    /**
     * ContactNotification constructor.
     * Twig\Environment = pour générer un email en format HTML
     * @param Swift_Mailer $mailer
     * @param Environment $renderer
     */
    public function __construct(Swift_Mailer $mailer, Environment $renderer) {

        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    public function notify(Contact $contact){
        // setBody on peut mettre du HTML ou du Texte
        // on va utiliser twig pour générer un email en format HTML
        $message = (new \Swift_Message('Agence : ' , $contact->getProperty()->getTitle()))
            ->setFrom('noreply@agence.fr')
            ->setTo('contact@agence.fr')
            ->setReplyTo($contact->getEmail())
            ->setBody($this->renderer->render('emails/contact.html.twig',[
                'contact' => $contact
            ]),'text/html')
        ;
        $this->mailer->send($message);
    }
}