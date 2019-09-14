<?php

require_once __DIR__ . "/../../vendor/bin/.phpunit/phpunit-7.4/vendor/autoload.php";

use Behat\Behat\Context\Context;
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

    /**
     * @Given the anonymous user is on the login page
     */
    public function theAnonymousUserIsOnTheLoginPage()
    {
//        $this->session->visit("http://127.0.0.1:8000/login"); // Not working with Symfony server: cURL error 60: SSL certificate problem: self signed certificate in certificate chain
        $this->session->visit("http://todoco.local/login"); // Works with wamp virtual host
        Assert::equalTo(200, $this->session->getStatusCode());
        $this->currentPage = $this->session->getPage();
        Assert::assertTrue($this->currentPage->hasButton("Se connecter"));
        Assert::assertTrue($this->currentPage->hasField("username"));
        Assert::assertTrue($this->currentPage->hasField("password"));
    }

    /**
     * @When the user fill the login form and submit it
     */
    public function theUserFillTheLoginFormAndSubmitIt()
    {
        $usernameInput = $this->currentPage->findField("username");
        $usernameInput->setValue(self::TEST_USERNAME);
        $passwordInput = $this->currentPage->findField("password");
        $passwordInput->setValue(self::TEST_PASSWORD);
        $submitButton = $this->currentPage->findButton("Se connecter");
        $submitButton->click();
    }

    /**
     * @Then the user is redirected to the homepage
     */
    public function theUserIsRedirectedToTheHomepage()
    {
        Assert::equalTo(200, $this->session->getStatusCode());
        $this->testCurrentUri('/');
        Assert::assertTrue($this->currentPage->hasLink("Se déconnecter"));
        Assert::assertTrue($this->currentPage->hasLink("Créer une nouvelle tâche"));
        Assert::assertTrue($this->currentPage->hasLink("Consulter la liste des tâches à faire"));
        Assert::assertTrue($this->currentPage->hasLink("Consulter la liste des tâches terminées"));
    }

    // Private

    /**
     * Check if the given uri is the current one
     *
     * @param string $uri
     */
    private function testCurrentUri(string $uri)
    {
        Assert::assertEquals(1, preg_match('#^https?:\/\/(www\.)?.+\.[a-z]{1,6}' . $uri . '$#', $this->session->getCurrentUrl()));
    }
}
