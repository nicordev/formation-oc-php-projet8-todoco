<?php

use App\Kernel;
use Behat\Behat\Context\Context;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class FixtureContext implements Context
{
    /**
     * @BeforeSuite
     */
    public static function load()
    {
        self::runCommand("php bin/console hautelook:fixtures:load");
    }

    protected static function runCommand(string $command, array $args = []): void
    {
        $application = new Application(new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']));
        $application->setAutoExit(false);

        $input = new ArrayInput(
            ['command' => $command] + $args
        );

        $output = new BufferedOutput();
        $exitCode = $application->run($input, $output);

        if (0 !== $exitCode) {
            throw new \Exception('Error while running command ['.$command.']: '.$output->fetch());
        }
    }
}