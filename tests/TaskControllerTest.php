<?php

namespace App\Tests\Controller;


use App\Tests\Helper\Login;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    public function testListAction_anonymous()
    {
        $client = static::createClient();
        $client->request("GET", "/tasks");
        // The user should be redirected to the login page
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals("/login", $client->getRequest()->getRequestUri());
    }

    public function testListAction_authenticated()
    {
        $client = static::createClient();
        Login::login($client, Login::TEST_USER_USERNAME, Login::TEST_USER_PASSWORD);
        $crawler = $client->request("GET", "/tasks");
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('div.task-card')->count());
    }
}