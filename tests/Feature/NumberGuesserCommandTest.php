<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestCase;
use App\Services\NumberGuesserService; 

class NumberGuesserCommandTest extends TestCase
{
    public function testItDisplaysWelcomeMessage(): void
    {
        $this->artisan('number-guesser')
            ->expectsOutput('Welcome to the Number Guesser game! 🎯')
            ->expectsQuestion('Choose your difficulty level:', 'exit')
            ->assertExitCode(0);
    }
  
    public function testItCanStartEasyGameAndExit(): void 
    {
        $serviceMock = $this->mock(NumberGuesserService::class);

        $serviceMock->shouldReceive('getAvailableLevels')
            ->andReturn([
                1 => ['name' => 'Easy', 'range' => [1, 10], 'max_attempts' => 5],
                2 => ['name' => 'Medium', 'range' => [1, 50], 'max_attempts' => 7],
                3 => ['name' => 'Hard', 'range' => [1, 100], 'max_attempts' => 10],
            ]);

        $serviceMock->shouldReceive('setupGame')
            ->with(1)
            ->once();

        $serviceMock->shouldReceive('getLevelInfo')
            ->with(1)
            ->andReturn(['name' => 'Easy', 'range' => [1, 10], 'max_attempts' => 5]);

        $serviceMock->shouldReceive('getRemainingAttempts')
            ->andReturn(5, 4, 3); 


        $serviceMock->shouldReceive('makeGuess')
            ->with(2)
            ->once()
            ->andReturnUsing(fn () => 
            [
                'status' => 'too_low', 
                'message' => 'Too low! Try a higher number 📈',
                'attempts' => 1,
                'remaining' => 4
            ]);

        $serviceMock->shouldReceive('makeGuess')
            ->with(7)
            ->once()
            ->andReturnUsing(fn () => 
            [
                'status' => 'won',
                'message' => '🎉 Congratulations! You guessed the number 7 in 2 attempts!',
                'attempts' => 2,
                'secret_number' => 7,
            ]);

        $this->artisan('number-guesser')
            ->expectsOutput('Welcome to the Number Guesser game! 🎯')
            ->expectsQuestion('Choose your difficulty level:', '1')
            ->expectsQuestion("Enter your guess (5 attempts left)", 2)
            ->expectsOutput('Too low! Try a higher number 📈') 
            ->expectsQuestion("Enter your guess (4 attempts left)", 7)
            ->expectsOutput('🎉 Congratulations! You guessed the number 7 in 2 attempts!')
            ->expectsQuestion('Would you like to play again?', false)
            ->assertExitCode(0);
    }
 
    public function testItCanStartMediumGameAndExit(): void
    {
        $serviceMock = $this->mock(NumberGuesserService::class);

        $serviceMock->shouldReceive('getAvailableLevels')
            ->andReturn([
                1 => ['name' => 'Easy', 'range' => [1, 10], 'max_attempts' => 5],
                2 => ['name' => 'Medium', 'range' => [1, 50], 'max_attempts' => 7],
                3 => ['name' => 'Hard', 'range' => [1, 100], 'max_attempts' => 10],
            ]);

        $serviceMock->shouldReceive('setupGame')
            ->with(2)
            ->once();

        $serviceMock->shouldReceive('getLevelInfo')
            ->with(2)
            ->andReturn(['name' => 'Medium', 'range' => [1, 50], 'max_attempts' => 7]);

        $serviceMock->shouldReceive('getRemainingAttempts')
            ->andReturn(7,6);

        $serviceMock->shouldReceive('makeGuess')
            ->with(32)
            ->once()
            ->andReturnUsing(fn() =>
                [
                    'status' => 'won',
                    'message' => '🎉 Congratulations! You guessed the number 32 in 1 attempts!',
                    'attempts' => 1,
                    'secret_number' => 32,
                ]);

        $this->artisan('number-guesser')
            ->expectsOutput('Welcome to the Number Guesser game! 🎯')
            ->expectsQuestion('Choose your difficulty level:', '2')
            ->expectsQuestion("Enter your guess (7 attempts left)", 32)
            ->expectsOutput('🎉 Congratulations! You guessed the number 32 in 1 attempts!')
            ->expectsQuestion('Would you like to play again?', false)
            ->assertExitCode(0);
    }

    public function testItCanStartHardGameAndExit(): void
    {
        $serviceMock = $this->mock(NumberGuesserService::class);

        $serviceMock->shouldReceive('getAvailableLevels')
            ->andReturn([
                1 => ['name' => 'Easy', 'range' => [1, 10], 'max_attempts' => 5],
                2 => ['name' => 'Medium', 'range' => [1, 50], 'max_attempts' => 7],
                3 => ['name' => 'Hard', 'range' => [1, 100], 'max_attempts' => 10],
            ]);

        $serviceMock->shouldReceive('setupGame')
            ->with(3)
            ->once();

        $serviceMock->shouldReceive('getLevelInfo')
            ->with(3)
            ->andReturn(['name' => 'Hard', 'range' => [1, 100], 'max_attempts' => 10]);

        $serviceMock->shouldReceive('getRemainingAttempts')
            ->andReturn(10, 9);

        $serviceMock->shouldReceive('makeGuess')
            ->with(99)
            ->once()
            ->andReturnUsing(fn() =>
                [
                    'status' => 'too_high',
                    'message' => 'Too high! Try a lower number 📉',
                    'attempts' => 1,
                    'remaining' => 9
                ]);

        $serviceMock->shouldReceive('makeGuess')
            ->with(50)
            ->once()
            ->andReturnUsing(fn() =>
                [
                    'status' => 'won',
                    'message' => '🎉 Congratulations! You guessed the number 50 in 2 attempts!',
                    'attempts' => 2,
                    'secret_number' => 50,
                ]);

        $this->artisan('number-guesser')
            ->expectsOutput('Welcome to the Number Guesser game! 🎯')
            ->expectsQuestion('Choose your difficulty level:', '3')
            ->expectsQuestion("Enter your guess (10 attempts left)", 99)
            ->expectsOutput('Too high! Try a lower number 📉')
            ->expectsQuestion("Enter your guess (9 attempts left)", 50)
            ->expectsOutput('🎉 Congratulations! You guessed the number 50 in 2 attempts!')
            ->expectsQuestion('Would you like to play again?', false)
            ->assertExitCode(0);
    }

    public function testItHandlesNonNumericInput(): void
    {
        $this->artisan('number-guesser')
            ->expectsQuestion('Choose your difficulty level:', '1') 
            ->expectsQuestion('Enter your guess (5 attempts left)', 'NaN')
            ->expectsOutput('Please enter a number between 1 and 10')
            ->expectsQuestion('Enter your guess (5 attempts left)', 'exit')
            ->assertExitCode(0);
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

    public function testItHandlesLosingTheGame()
    {
        $this->markTestSkipped();
    }

    public function testItAllowsPlayingMultipleGames()
    {
        $this->markTestSkipped();
    }

    public function testItHandlesExitDuringGame()
    {
        $this->markTestSkipped();
    }

    public function testItHandlesExitBeforeStartingGame()
    {
        $this->markTestSkipped();
    }
}