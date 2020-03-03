<?php


namespace App\Subscribers;

use App\Events\RestorePasswordEvent;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber extends AbstractController implements EventSubscriberInterface
{
    private $email;
    private $sender;
    private $domain;

    public function __construct(
        EmailService $emailController,
        string $senderEmail,
        string $domain
    ){
        $this->email = $emailController;
        $this->sender = $senderEmail;
        $this->domain = $domain;
    }

    public static function getSubscribedEvents()
    {
        return [
            RestorePasswordEvent::SEND_RESTORE_PASSWORD_EMAIL=>'onRestorePasswordSendEmail',
        ];
    }

    public function onRestorePasswordSendEmail(RestorePasswordEvent $event){

        $user = $event->getUser();

        $recipients =[$user->getUsername()];

        // Send email
        $this->email->sendContactMail(
            '/email/recover.html',
            $this->sender,
            $recipients,
            'Password recover',
            ['domain'=>$this->domain,'code'=>$user->getRecover()]
        );
    }
}