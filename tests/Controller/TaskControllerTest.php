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

    public function testEditAction_anonymous()
    {
        $client = static::createClient();
        $client->request("GET", "/tasks/1/edit");

        // The user should be redirected to the login page
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals("/login", $client->getRequest()->getRequestUri());
    }

    public function testEditAction_authenticated()
    {
        $client = static::createClient();
        Login::login($client, Login::TEST_USER_USERNAME, Login::TEST_USER_PASSWORD);
        $crawler = $client->request("GET", "/tasks");

        // The user choose a task he owns to edit
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/tasks", $client->getRequest()->getRequestUri());
        $link = $crawler->selectLink("test_task_1 title")->link();
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/tasks/1/edit", $client->getRequest()->getRequestUri());
        $this->assertEquals(1, $crawler->filter('input[name="task[title]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[value="test_task_1 title"]')->count());
        $this->assertEquals(1, $crawler->filter('textarea[name="task[content]"]:contains("test_task_1 content")')->count());

        // The user edit the task
        $form = $crawler->selectButton("Modifier")->form();
        $form['task[title]'] = "test_task_1 title - modified";
        $form['task[content]'] = "test_task_1 content - modified";
        $client->submit($form);

        // The modified task should appear in the list
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/tasks", $client->getRequest()->getRequestUri());
        $this->assertGreaterThan(0, $crawler->filter('div.task-card')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("test_task_1 title - modified")')->count());
    }

    public function testEditAction_authenticated_notOwned()
    {
        $client = static::createClient();
        Login::login($client, Login::TEST_USER_USERNAME, Login::TEST_USER_PASSWORD);
        $crawler = $client->request("GET", "/tasks");

        // The user choose a task he does not owns to edit
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/tasks", $client->getRequest()->getRequestUri());
        $link = $crawler->selectLink("test_task_3 title")->link();
        $client->click($link);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testEditAction_authenticated_anonymous()
    {
        $client = static::createClient();
        Login::login($client, Login::TEST_USER_USERNAME, Login::TEST_USER_PASSWORD);
        $crawler = $client->request("GET", "/tasks");

        // The user choose an anonymous task to edit
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/tasks", $client->getRequest()->getRequestUri());
        $link = $crawler->selectLink("test_task_4 title - anonymous")->link();
        $client->click($link);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testDeleteAction_anonymous()
    {
        $client = static::createClient();
        $client->request("GET", "/tasks/1/delete");

        // The user should be redirected to the login page
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals("/login", $client->getRequest()->getRequestUri());
    }

    public function testDeleteAction_authenticated()
    {
        $client = static::createClient();
        Login::login($client, Login::TEST_USER_USERNAME, Login::TEST_USER_PASSWORD);
        $crawler = $client->request("GET", "/tasks");

        // The user delete a task he owns
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/tasks", $client->getRequest()->getRequestUri());
        $form = $crawler->selectButton("task-2-delete-btn")->form();
        $client->submit($form);

        // The deleted task should not appear in the list anymore
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/tasks", $client->getRequest()->getRequestUri());
        $this->assertGreaterThan(0, $crawler->filter('div.task-card')->count());
        $this->assertEquals(0, $crawler->filter('a:contains("test_task_2 title")')->count());
    }

    public function testDeleteAction_authenticated_notOwned()
    {
        $client = static::createClient();
        Login::login($client, Login::TEST_USER_USERNAME, Login::TEST_USER_PASSWORD);
        $crawler = $client->request("GET", "/tasks");

        // The user tries to delete a task he does not owns
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/tasks", $client->getRequest()->getRequestUri());
        $form = $crawler->selectButton("task-3-delete-btn")->form();
        $client->submit($form);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testDeleteAction_authenticated_anonymous()
    {
        $client = static::createClient();
        Login::login($client, Login::TEST_USER_USERNAME, Login::TEST_USER_PASSWORD);
        $crawler = $client->request("GET", "/tasks");

        // The user tries to delete an anonymous task
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/tasks", $client->getRequest()->getRequestUri());
        $form = $crawler->selectButton("task-4-delete-btn")->form();
        $client->submit($form);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}