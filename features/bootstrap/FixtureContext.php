<?php

use App\Kernel;
use Behat\Behat\Context\Context;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class FixtureContext extends KernelTestCase implements Context
{
    use RefreshDatabaseTrait;

    static protected $kernel;

    /**
     * @BeforeSuite
     */
    public static function loadFixtures()
    {
        self::bootKernel();
//        $result = `php bin/console hautelook:fixtures:load`;
//        self::runCommand("hautelook:fixtures:load"); // Not working
    }

//    /**
//     * @AfterSuite
//     */
//    public static function emptyDatabase() // Here I try to empty the tables manually but it does not work
//    {
//        $container = static::$kernel->getContainer();
//        $orm = $container->get("doctrine");
//        $manager = $orm->getManager();
//        $connection = $manager->getConnection();
//        //$connection->query('DELETE FROM task');
//        // Next code comes from https://stackoverflow.com/questions/9686888/how-to-truncate-a-table-using-doctrine-2?answertab=votes#tab-top
//        $connection->beginTransaction();
//
//        try {
//            $connection->query('SET FOREIGN_KEY_CHECKS=0');
//            $connection->query('DELETE FROM task');
//            $connection->query('SET FOREIGN_KEY_CHECKS=1');
//            $connection->commit();
//        } catch (\Exception $e) {
//            $connection->rollback();
//        }
//    }

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