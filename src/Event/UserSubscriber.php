<?php


namespace App\Event;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
                UserRegisterEvent::NAME => 'onUserRegister'
        ];
    }

    public function onUserRegister(UserRegisterEvent $event)
    {
        $body = $this->twig->render('email/registration.html.twig', [
            'user' => $event->getRegisteredUSer()
        ]);

        $message = (new \Swift_Message())
            ->setSubject('Welcome to Micro Post App!')
            ->setFrom('ahoj@micropost.com')
            ->setTo($event->getRegisteredUSer()->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}