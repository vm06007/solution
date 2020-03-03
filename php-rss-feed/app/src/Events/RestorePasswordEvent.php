<?php


namespace App\Events;


use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class RestorePasswordEvent extends Event
{

    const SEND_RESTORE_PASSWORD_EMAIL = 'event.restore.password';

    /* var UserInterface */
    protected $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function getUser(): User {
        return $this->user;
    }

}