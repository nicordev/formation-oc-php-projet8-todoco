<?php

namespace App\Tests\functional\Controller;

use App\Tests\Helper\Login;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    /*
     * List
     */

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

    /*
     * Create
     */

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
        $client->request("GET", "/users/create");

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

    /*
     * Edit
     */

    public function testEditAction_anonymous()
    {
        $client = static::createClient();
        $client->request("GET", "/users/2/edit");

        // The user should be redirected to the login page
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals("/login", $client->getRequest()->getRequestUri());
    }

    public function testEditAction_authenticated()
    {
        $client = static::createClient();
        $client->request("GET", "/users/2/edit");
        Login::login($client, Login::TEST_USER_USERNAME, Login::TEST_USER_PASSWORD);
        $client->request("GET", "/users/2/edit");

        // The user should not reach the page
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testEditAction_admin()
    {
        $client = static::createClient();
        $client->request("GET", "/users/2/edit");
        Login::login($client, Login::TEST_ADMIN_USERNAME, Login::TEST_ADMIN_PASSWORD);
        $crawler = $client->request("GET", "/users/2/edit");

        // The admin should be on the user edition page
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/users/2/edit", $client->getRequest()->getRequestUri());
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
        $this->assertEquals(1, $crawler->filter('button[type="submit"]:contains("Modifier")')->count());
        $this->assertEquals("testuser2", $crawler->filter('input#user_username')->attr("value"));
        $this->assertEquals("user2@test.com", $crawler->filter('input#user_email')->attr("value"));

        // The admin edit the profile
        $form = $crawler->selectButton("Modifier")->form();
        $form['user[username]'] = "testuser2_modified";
        $form['user[password][first]'] = "mdp";
        $form['user[password][second]'] = "mdp";
        $form['user[email]'] = "user2.modified@test.com";
        $client->submit($form);

        // The modified user should appear in the list
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/users", $client->getRequest()->getRequestUri());
        $this->assertEquals(1, $crawler->filter('td:contains("testuser2_modified")')->count());
    }

    /*
     * Delete
     */

    public function testDeleteAction_anonymous()
    {
        $client = static::createClient();
        $client->request("GET", "/users/3/delete");

        // The user should be redirected to the login page
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals("/login", $client->getRequest()->getRequestUri());
    }

    public function testDeleteAction_authenticated()
    {
        $client = static::createClient();
        $client->request("GET", "/users/3/delete");
        Login::login($client, Login::TEST_USER_USERNAME, Login::TEST_USER_PASSWORD);
        $client->request("GET", "/users/3/delete");

        // The user should not reach the page
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testDeleteAction_admin()
    {
        $client = static::createClient();
        $client->request("GET", "/users/3/delete");
        Login::login($client, Login::TEST_ADMIN_USERNAME, Login::TEST_ADMIN_PASSWORD);
        $client->request("GET", "/users/3/delete");

        // The deleted user should not appear in the list anymore
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("/users", $client->getRequest()->getRequestUri());
        $this->assertEquals(0, $crawler->filter('td:contains("testuser3")')->count());
    }
}
