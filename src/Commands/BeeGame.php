<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BeeGame extends Command
{
    protected static $defaultName = 'beesinthetrap';

    private int $playerHP = 100;
    private array $hive = [];

    protected function configure(): void
    {
        $this->setDescription('Play the Bees in the Trap game.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initializeHive();
        $output->writeln("Welcome to Bees in the Trap! Type 'hit' to attack or 'auto' to autoplay.");
        while ($this->playerHP > 0 && !$this->isHiveDestroyed()) {
            $output->write("> ");
            $command = trim(fgets(STDIN));
            if ($command === 'hit') {
                $this->playerTurn($output);
            } elseif ($command === 'auto') {
                $this->autoPlay($output);
                break;
            } else {
                $output->writeln("Invalid command. Type 'hit' or 'auto'.");
                break;
            }

            if ($this->playerHP > 0 && !$this->isHiveDestroyed()) {
                $this->beesTurn($output);
            }
        }

        $this->endGame($output);
        return Command::SUCCESS;
    }

    private function initializeHive(): void
    {
        $this->hive = [
            'queen' => ['count' => 1, 'hp' => 100, 'damage' => 10, 'hitDamage' => 10],
            'worker' => ['count' => 5, 'hp' => 75, 'damage' => 5, 'hitDamage' => 25],
            'drone' => ['count' => 25, 'hp' => 60, 'damage' => 1, 'hitDamage' => 30],
        ];
    }

    private function playerTurn(OutputInterface $output): void
    {
        $beeType = $this->selectRandomBee();
        if ($beeType) {
            $this->hive[$beeType]['hp'] -= $this->hive[$beeType]['hitDamage'];
            $output->writeln(
                "Direct Hit! You took {$this->hive[$beeType]['hitDamage']} hit points from a {$beeType} bee."
            );
            if ($beeType === 'queen' && $this->hive[$beeType]['hp'] <= 0) {
                $output->writeln("The Queen Bee is dead! All bees are defeated.");
                $this->hive = [];
            }
        } else {
            $output->writeln("Miss! You just missed the hive, better luck next time!");
        }
    }

    private function beesTurn(OutputInterface $output): void
    {
        $beeType = $this->selectRandomBee();
        if ($beeType) {
            $this->playerHP -= $this->hive[$beeType]['damage'];
            $output->writeln(
                "Sting! You just got stung by a {$beeType} bee and lost {$this->hive[$beeType]['damage']} HP."
            );
        } else {
            $output->writeln("Buzz! That was close! The bees missed you!");
        }
    }

    private function selectRandomBee()
    {
        $aliveBees = [];
        foreach ($this->hive as $type => $bee) {
            if ($bee['hp'] > 0) {
                $aliveBees = array_merge($aliveBees, array_fill(0, $bee['count'], $type));
            }
        }
        return $aliveBees ? $aliveBees[array_rand($aliveBees)] : null;
    }

    private function isHiveDestroyed()
    {
        foreach ($this->hive as $bee) {
            if ($bee['hp'] > 0) {
                return false;
            }
        }
        return true;
    }

    private function autoPlay(OutputInterface $output): void
    {
        while ($this->playerHP > 0 && !$this->isHiveDestroyed()) {
            $this->playerTurn($output);
            if ($this->playerHP > 0 && !$this->isHiveDestroyed()) {
                $this->beesTurn($output);
            }
        }
    }

    private function endGame(OutputInterface $output): void
    {
        if ($this->playerHP <= 0) {
            $output->writeln("Game Over! You were stung to death.");
        } else {
            $output->writeln("Congratulations! You destroyed the hive.");
        }
    }
}
