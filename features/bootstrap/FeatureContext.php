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

    // Login

    /**
     * @Given I am authenticated
     */
    public function iAmAuthenticated()
    {
        $this->iAmOn("/login");
        $this->iFillInWith("username", self::TEST_USERNAME);
        $this->iFillInWith("password", self::TEST_PASSWORD);
        $this->iPress("Se connecter");
    }

    // Navigation

    /**
     * @Given I am on :uri
     */
    public function iAmOn($uri)
    {
        $this->session->visit(self::URL . $uri);
        $this->currentPage = $this->session->getPage();
    }

    /**
     * @When I follow :link
     */
    public function iFollow($link)
    {
        $taskListLink = $this->currentPage->findLink($link);
        $taskListLink->click();
    }

    // Actions

    /**
     * @Given I press :button
     */
    public function iPress($button)
    {
        Assert::assertTrue($this->currentPage->hasButton($button));
        $button = $this->currentPage->findButton($button);
        $button->click();
    }

    /**
     * @When I fill in :input with :value
     */
    public function iFillInWith($input, $value)
    {
        Assert::assertTrue($this->currentPage->hasField($input));
        $field = $this->currentPage->findField($input);
        $field->setValue($value);
    }

    // Assertions

    /**
     * @Then the response status code should be :code
     */
    public function theResponseStatusCodeShouldBe($code)
    {
        Assert::assertEquals($code, $this->session->getStatusCode());
    }

    /**
     * @Then I should be on :uri
     */
    public function iShouldBeOn($uri)
    {
        $url = $this->session->getCurrentUrl();
        $escapedUri = str_replace('/', '\\/', $uri);
        $regex = '#^https?:\/\/(www\.)?.+\.[a-z]{1,6}' . $escapedUri . '$#';
        Assert::assertEquals(1, preg_match($regex, $url));
    }

    /**
     * @Given I should see a :element :content
     */
    public function iShouldSeeA($element, $value)
    {
        Assert::assertTrue($this->currentPage->has("named_exact", [$element, $value]));
    }

    /**
     * @Given I should see every tasks
     */
    public function iShouldSeeEveryTasks()
    {
        $taskCards = $this->currentPage->findAll("css", "div.task-card");
        Assert::assertNotEmpty($taskCards);
    }

    /**
     * @Given I should see the task :title with its content :content
     */
    public function iShouldSeeTheTaskWithItsContent($title, $content)
    {
        Assert::assertTrue($this->currentPage->hasLink($title));
        $title = $this->currentPage->find("named_exact", ["content", $title]);
        $content = $this->currentPage->find("named_exact", ["content", $content]);
        Assert::assertNotNull($title);
        Assert::assertNotNull($content);
    }
}
