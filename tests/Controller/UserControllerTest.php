<?php

namespace App\Tests\Controller;


use App\Tests\Helper\Login;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    public function testListAction_anonymous()
    {
        $client = static::createClient();
        $client->request("GET", "/users");

        // The user should be redirected to the login page
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals("/login", $client->getRequest()->getRequestUri());
    }

    public function testListAction_authenticated()
    {
        $client = static::createClient();
        $client->request("GET", "/users");
        Login::login($client, Login::TEST_USER_USERNAME, Login::TEST_USER_PASSWORD);
        $client->request("GET", "/users");

        // The user should not reach this page
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testListAction_admin()
    {
        $client = static::createClient();
        Login::login($client, Login::TEST_ADMIN_USERNAME, Login::TEST_ADMIN_PASSWORD);
        $crawler = $client->request("GET", "/users");

        // The admin should see the list
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/users", $client->getRequest()->getRequestUri());
        $this->assertEquals(1, $crawler->filter('h1:contains("Liste des utilisateurs")')->count());
    }
}