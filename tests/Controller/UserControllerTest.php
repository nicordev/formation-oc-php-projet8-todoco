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

    public function testCreateAction_anonymous()
    {
        $client = static::createClient();
        $client->request("GET", "/users/create");

        // The user should be redirected to the login page
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals("/login", $client->getRequest()->getRequestUri());
    }

    public function testCreateAction_authenticated()
    {
        $client = static::createClient();
        Login::login($client, Login::TEST_USER_USERNAME, Login::TEST_USER_PASSWORD);
        $crawler = $client->request("GET", "/users/create");

        // The user should not reach the page
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testCreateAction_admin()
    {
        $client = static::createClient();
        Login::login($client, Login::TEST_ADMIN_USERNAME, Login::TEST_ADMIN_PASSWORD);
        $crawler = $client->request("GET", "/users/create");

        // The admin should be on the user creation page
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/users/create", $client->getRequest()->getRequestUri());
        $this->assertEquals(1, $crawler->filter('input#user_username')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[username]"]')->count());
        $this->assertEquals(1, $crawler->filter('input#user_password_first')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][first]"]')->count());
        $this->assertEquals(1, $crawler->filter('input#user_password_second')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][second]"]')->count());
        $this->assertEquals(1, $crawler->filter('input#user_email')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[email]"]')->count());
        $this->assertEquals(1, $crawler->filter('select#user_roles')->count());
        $this->assertEquals(1, $crawler->filter('select[name="user[roles][]"]')->count());
        $this->assertEquals(1, $crawler->filter('button[type="submit"]:contains("Ajouter")')->count());

        // The admin adds a new user
        $form = $crawler->selectButton("Ajouter")->form();
        $form['user[username]'] = "Test add new user - name";
        $form['user[password][first]'] = "mdp";
        $form['user[password][second]'] = "mdp";
        $form['user[email]'] = "new.user@test.com";
        $client->submit($form);

        // The new user should appear in the list
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/users", $client->getRequest()->getRequestUri());
        $this->assertEquals(1, $crawler->filter('td:contains("Test add new user - name")')->count());
    }
}