<?php

require_once __DIR__ . "/../../vendor/bin/.phpunit/phpunit-7.4/vendor/autoload.php";

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Session;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var \Behat\Mink\Element\DocumentElement
     */
    private $currentPage;

    //    public const URL = "http://127.0.0.1:8000"; // Not working with Symfony server: cURL error 60: SSL certificate problem: self signed certificate in certificate chain
    public const URL = "http://todoco.local"; // Works with wamp virtual host
    public const TEST_USERNAME = "bob";
    public const TEST_PASSWORD = "mdp";

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $driver = new GoutteDriver();
        $this->session = new Session($driver);
        $this->session->start();
    }

    // login.feature

    /**
     * @Given I am on the login page
     */
    public function iAmOnTheLoginPage()
    {
        $this->session->visit(self::URL . '/');
        Assert::assertEquals(200, $this->session->getStatusCode());
        $this->testCurrentUri('/login');
        $this->goToLoginPage();
    }

    /**
     * @When I fill the login form and submit it
     */
    public function iFillTheLoginFormAndSubmitIt()
    {
        $this->fillLoginFormAndSubmit();
    }

    /**
     * @Then I am redirected to the homepage fully authenticated
     */
    public function iAmRedirectedToTheHomepageFullyAuthenticated()
    {
        $this->checkHomepage();
    }

    /**
     * @When I click on the link to logout
     */
    public function iClickOnTheLinkToLogout()
    {
        $logoutButton = $this->currentPage->findLink("Se déconnecter");
        $logoutButton->click();
    }

    /**
     * @Then I am redirected to the login page as an anonymous user
     */
    public function iAmRedirectedToTheLoginPageAsAnAnonymousUser()
    {
        Assert::assertEquals(200, $this->session->getStatusCode());
        $this->testCurrentUri('/login');
        Assert::assertNull($this->currentPage->findLink("Se déconnecter"));
    }

    // tasks.feature

    /**
     * @Given I am authenticated
     */
    public function iAmAuthenticated()
    {
        $this->loginThroughLoginPage();
    }

    /**
     * @Given I am on the homepage
     */
    public function iAmOnTheHomepage()
    {
        $this->checkHomepage();
    }

    /**
     * @When I click on the link to show the task list
     */
    public function iClickOnTheLinkToShowTheTaskList()
    {
        $taskListLink = $this->currentPage->findLink("Consulter la liste des tâches à faire");
        $taskListLink->click();
        Assert::assertEquals(200, $this->session->getStatusCode());
    }

    /**
     * @Then I am redirected to the task list
     */
    public function iAmRedirectedToTheTaskList()
    {
        $this->testCurrentUri("/tasks");
        Assert::assertTrue($this->currentPage->hasLink("Créer une tâche"));
        $taskCards = $this->currentPage->findAll("css", "div.task-card");
        Assert::assertNotEmpty($taskCards);
    }

    // Private

    /**
     * Check if the given uri is the current one
     *
     * @param string $uri
     */
    private function testCurrentUri(string $uri)
    {
        $url = $this->session->getCurrentUrl();
        $escapedUri = str_replace('/', '\\/', $uri);
        $regex = '#^https?:\/\/(www\.)?.+\.[a-z]{1,6}' . $escapedUri . '$#';
        Assert::assertEquals(1, preg_match($regex, $url));
    }

    private function loginThroughLoginPage()
    {
        $this->goToLoginPage();
        $this->fillLoginFormAndSubmit();
    }

    private function goToLoginPage()
    {
        $this->session->visit(self::URL . "/login");
        Assert::assertEquals(200, $this->session->getStatusCode());
        $this->currentPage = $this->session->getPage();
        Assert::assertTrue($this->currentPage->hasButton("Se connecter"));
        Assert::assertTrue($this->currentPage->hasField("username"));
        Assert::assertTrue($this->currentPage->hasField("password"));
    }

    private function fillLoginFormAndSubmit()
    {
        $usernameInput = $this->currentPage->findField("username");
        $usernameInput->setValue(self::TEST_USERNAME);
        $passwordInput = $this->currentPage->findField("password");
        $passwordInput->setValue(self::TEST_PASSWORD);
        $submitButton = $this->currentPage->findButton("Se connecter");
        $submitButton->click();
    }

    private function checkHomepage()
    {
        Assert::assertEquals(200, $this->session->getStatusCode());
        $this->testCurrentUri('/');
        Assert::assertTrue($this->currentPage->hasLink("Se déconnecter"));
        Assert::assertTrue($this->currentPage->hasLink("Créer une nouvelle tâche"));
        Assert::assertTrue($this->currentPage->hasLink("Consulter la liste des tâches à faire"));
        Assert::assertTrue($this->currentPage->hasLink("Consulter la liste des tâches terminées"));
    }
}
