<?php

namespace App\Tests\Helper;


use Symfony\Bundle\FrameworkBundle\Client;

class Debug
{
    public static function printPage(Client $client)
    {
        echo $client->getResponse()->getContent();
    }
}