<?php

require_once __DIR__ . "/../../bin/.phpunit/phpunit-7.5/vendor/autoload.php";

use Behat\MinkExtension\Context\MinkContext;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext
{
    public const TEST_USERNAME = "bob";
    public const TEST_PASSWORD = "mdp";

    // Login

    /**
     * @Given I am authenticated
     */
    public function iAmAuthenticated()
    {
        $this->visit("/login");
        $this->fillField("username", self::TEST_USERNAME);
        $this->fillField("password", self::TEST_PASSWORD);
        $this->pressButton("Se connecter");
    }

    /**
     * @Then I should see a :element named :content
     */
    public function iShouldSeeA($element, $value)
    {
        Assert::assertTrue($this->getSession()->getPage()->has("named_exact", [$element, $value]));
    }

    /**
     * @Given I should see every tasks
     */
    public function iShouldSeeEveryTasks()
    {
        $taskCards = $this->getSession()
            ->getPage()
            ->findAll("css", "div.task-card")
        ;
        Assert::assertNotEmpty($taskCards);
    }

    /**
     * @Given I should see the task :title with its content :content
     */
    public function iShouldSeeTheTaskWithItsContent($title, $content)
    {
        $page = $this->getSession()->getPage();
        Assert::assertTrue($page->hasLink($title));
        $title = $page->find("named_exact", ["content", $title]);
        $content = $page->find("named_exact", ["content", $content]);
        Assert::assertNotNull($title);
        Assert::assertNotNull($content);
    }
}
