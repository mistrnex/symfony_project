<?php


namespace App\Event;


use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserRegisterEvent extends Event
{
    const NAME = 'user.register';

    /**
     * @var User
     */
    private $registeredUSer;

    public function __construct(User $registeredUSer){

        $this->registeredUSer = $registeredUSer;
    }

    /**
     * @return User
     */
    public function getRegisteredUser(): User
    {
        return $this->registeredUSer;
    }



}