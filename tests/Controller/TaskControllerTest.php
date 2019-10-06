<?php

namespace App\Tests\Controller;


use App\Tests\Helper\Debug;
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

        // The user should see the list
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('div.task-card')->count());
    }

    public function testCreateAction_anonymous()
    {
        $client = static::createClient();
        $client->request("GET", "/tasks/create");

        // The user should be redirected to the login page
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals("/login", $client->getRequest()->getRequestUri());
    }

    public function testCreateAction_authenticated()
    {
        $client = static::createClient();
        Login::login($client, Login::TEST_USER_USERNAME, Login::TEST_USER_PASSWORD);
        $crawler = $client->request("GET", "/tasks/create");

        // The user should be on the task creation page
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/tasks/create", $client->getRequest()->getRequestUri());
        $this->assertEquals(1, $crawler->filter('input#task_title')->count());
        $this->assertEquals(1, $crawler->filter('input[name="task[title]"]')->count());
        $this->assertEquals(1, $crawler->filter('textarea#task_content')->count());
        $this->assertEquals(1, $crawler->filter('textarea[name="task[content]"]')->count());
        $this->assertEquals(1, $crawler->filter('button[type="submit"]:contains("Ajouter")')->count());

        // The user creates a new task
        $form = $crawler->selectButton("Ajouter")->form();
        $form['task[title]'] = "Test new task - title";
        $form['task[content]'] = "Test new task - content";
        $client->submit($form);

        // The new task should appear in the list
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/tasks", $client->getRequest()->getRequestUri());
        $this->assertGreaterThan(0, $crawler->filter('div.task-card')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Test new task - title")')->count());
    }
}