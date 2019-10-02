<?php

namespace App\Helper;


use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class PasswordEncoder
{
    private static $encoder;
    /**
     * @var User
     */
    private static $user;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        self::$user = new User();
        self::$encoder = $encoderFactory->getEncoder(self::$user);
    }
    
    public static function hashPassword(string $password)
    {
        return self::$encoder->encodePassword($password, self::$user->getSalt());
    }
}