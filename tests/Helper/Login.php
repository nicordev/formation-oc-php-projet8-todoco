<?php

namespace App\Tests\Helper;

use Symfony\Bundle\FrameworkBundle\Client;

class Login
{
    public const TEST_USER_USERNAME = "testuser";
    public const TEST_USER_PASSWORD = "mdp";
    public const TEST_ADMIN_USERNAME = "testadmin";
    public const TEST_ADMIN_PASSWORD = self::TEST_USER_PASSWORD;

    /**
     * Login using the login form on /login
     *
     * @param Client $client
     * @param string $login
     * @param string $password
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public static function login(
        Client $client,
        string $login = self::TEST_USER_USERNAME,
        string $password = self::TEST_USER_PASSWORD
    ) {
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton("Se connecter")->form();
        $form["_username"] = $login;
        $form["_password"] = $password;
        $client->submit($form);

        return $client->followRedirect();
    }
}
