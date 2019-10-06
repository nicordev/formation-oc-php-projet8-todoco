<?php

namespace App\Tests\Controller;


use App\Tests\Helper\Login;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    public function testIndexAction_anonymous()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        // The user should be redirected to the login page
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals("/login", $client->getRequest()->getRequestUri());
    }

    public function testIndexAction_login()
    {
        $client = static::createClient();
        Login::login($client, Login::TEST_USER_USERNAME, Login::TEST_USER_PASSWORD);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/", $client->getRequest()->getRequestUri());
    }
}