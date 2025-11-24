<?php

namespace Tests\Feature;

use App\Services\NumberGuesserService;
use Illuminate\Foundation\Testing\TestCase;

class NumberGuesserCommandTest extends TestCase
{
    public function testItCanRunTheNumberGuesserCommand(): void
    {
        $this->artisan('number-guesser')
            ->expectsQuestion('Choose your difficulty level:', 'exit')
            ->expectsOutput('Thanks for playing 👋')
            ->assertExitCode(0);
    }

    public function testItDisplaysWelcomeMessage(): void
    {
        $this->artisan('number-guesser')
            ->expectsOutput('Welcome to the Number Guesser game! 🎯')
            ->expectsQuestion('Choose your difficulty level:', 'exit')
            ->assertExitCode(0);
    }
  
    public function testItCanStartEasyGameAndExit() 
    {
        $this->markTestSkipped();
    }
 
    public function testItCanStartMediumGameAndExit()
    {
        $this->markTestSkipped();
    }

    public function testItCanStartHardGameAndExit() 
    {
        $this->markTestSkipped();
    }

    public function testItHandlesInvalidNumericInput() 
    {
        $this->markTestSkipped();
    }

    public function testItHandlesOutOfRangeGuess(): void
    {
        $this->artisan('number-guesser')
            ->expectsQuestion('Choose your difficulty level:', '1') 
            ->expectsQuestion('Enter your guess (5 attempts left)', '15')
            ->expectsOutput('Please enter a number between 1 and 10')
            ->expectsQuestion('Enter your guess (5 attempts left)', 'exit')
            ->assertExitCode(0);
    }

    public function testItProvidesTooLowFeedback(): void
    {
        $this->markTestSkipped();
    }

    public function testItProvidesTooHighFeedback(): void
    {
        $this->markTestSkipped();
    }

    public function testItHandlesWinningTheGame() 
    {
        $this->markTestSkipped();
    }

    public function testItHandlesLosingTheGame()
    {
        $this->markTestSkipped();
    }

    public function testItAllowsPlayingMultipleGames()
    {
        $this->markTestSkipped();
    }
}