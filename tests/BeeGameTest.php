<?php

require __DIR__ . '../../vendor/autoload.php';
require __DIR__ . '../../src/Commands/BeeGame.php';

use App\Commands\BeeGame;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\ConsoleOutput;

class BeeGameTest extends TestCase
{
    private CommandTester $commandTester;
    private ConsoleOutput $output;

    protected function setUp(): void
    {
        $application = new Application();
        $application->add(new BeeGame());
        $command = $application->find('beesinthetrap');
        $this->commandTester = new CommandTester($command);
        $this->output = new ConsoleOutput();
    }

    public function testCommandRunsSuccessfully()
    {
        $this->output->writeln("Welcome to Bees in the Trap! Type 'hit' to attack or 'auto' to autoplay.");
        $this->output->writeln("> ");
        $this->commandTester->execute([]);
        $this->assertStringContainsString("Game Over! You were stung to death.", $this->commandTester->getDisplay());
    }
}
