<?php

namespace Tests\Unit;

use App\Services\NumberGuesserService;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use RuntimeException;
use Tests\Helpers\ReflectionHelper;

class NumberGuesserServiceTest extends TestCase
{
    use ReflectionHelper;
    private NumberGuesserService $service;

    protected function setUp():void
    {
        parent::setUp();
        $this->service = new NumberGuesserService();
    }

    public function testItCanSetupValidGameLevel(): void
    {
        $this->service->setupGame(1);

        $this->assertEquals(1, $this->service->getCurrentLevel());
        $this->assertEquals(5, $this->service->getRemainingAttempts());
    }

    public function testItCanSetupAValidGameLevel():void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid level: 99. Available levels: 1, 2, 3');

        $this->service->setupGame(99);
    }

    public function testItReturnsAvailableLevels(): void
    {
        $levels = $this->service->getAvailableLevels();

        $this->assertArrayHasKey(1, $levels);
        $this->assertArrayHasKey(2, $levels);
        $this->assertArrayHasKey(3, $levels);

        $this->assertEquals('Easy', $levels[1]['name']);
        $this->assertEquals([1, 10], $levels[1]['range']);
        $this->assertEquals(5, $levels[1]['max_attempts']);
    }


    public function testItReturnsLevelInfo(): void
    {
        $levelInfo = $this->service->getLevelInfo(2);

        $this->assertEquals('Medium', $levelInfo['name']);
        $this->assertEquals([1, 50], $levelInfo['range']);
        $this->assertEquals(7, $levelInfo['max_attempts']);
    }

    public function testItThrowsExceptionWhenMakingGuessWithoutActiveGame(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Game is not active');

        $this->service->makeGuess(5);
    }

    public function testItRejectsGuessOutsideValidRangeAndDoesNotIncrementAttempts(): void 
    {
        $this->service->setupGame(1);
        
        $result = $this->service->makeGuess(15);

        $this->assertEquals('invalid', $result['status']);
        $this->assertEquals('Please enter a number between 1 and 10', $result['message']);
        $this->assertEquals(0, $result['attempts']); 
    }

    public function testItProvidesTooLowFeedback(): void // Consider making a data provider to pass in low and high
    {
        $this->service->setupGame(1);

        $this->setSecretNumber($this->service, 8);

        $result = $this->service->makeGuess(3);

        $this->assertEquals('too_low', $result['status']);
        $this->assertEquals('Too low! Try a higher number 📈', $result['message']);
        $this->assertEquals(1, $result['attempts']);
        $this->assertEquals(4, $result['remaining']);
    }

    public function testItProvidesTooHighFeedback(): void
    {
        $this->service->setupGame(1);
        $this->setSecretNumber($this->service, 3);

        $result = $this->service->makeGuess(8);

        $this->assertEquals('too_high', $result['status']);
        $this->assertEquals('Too high! Try a lower number 📉', $result['message']);
        $this->assertEquals(1, $result['attempts']);
        $this->assertEquals(4, $result['remaining']);
    }

    public function testItHandlesCorrectGuess(): void
    {
        $this->service->setupGame(1);
        $this->setSecretNumber($this->service, 7);

        $result = $this->service->makeGuess(7);

        $this->assertEquals('won', $result['status']);
        $this->assertStringContainsString('🎉 Congratulations!', $result['message']);
        $this->assertEquals(7, $result['secret_number']);
        $this->assertEquals(1, $result['attempts']);
    }

    public function testItHandlesGameLossWhenAttempsExhausted(): void
    {
        $this->service->setupGame(1);
        $this->setSecretNumber($this->service, 10);

        for ($i = 1; $i <= 4; $i++) {
            $result = $this->service->makeGuess(1);
            $this->assertEquals('too_low', $result['status']);
        }

        $result = $this->service->makeGuess(1);

        $this->assertEquals('lost', $result['status']);
        $this->assertEquals('😞 Game over! The number was 10', $result['message']);
        $this->assertEquals(10, $result['secret_number']);
        $this->assertEquals(5, $result['attempts']);
    }

    public function testItTracksRemainingAttemptsCorrectly(): void
    {
        $this->service->setupGame(2);
        $this->setSecretNumber($this->service, 50);

        $this->assertEquals(7, $this->service->getRemainingAttempts());

        $this->service->makeGuess(1);
        $this->assertEquals(6, $this->service->getRemainingAttempts());

        $this->service->makeGuess(2);
        $this->assertEquals(5, $this->service->getRemainingAttempts());
    }

    public function testItPreventsGuessingAfterGameWon(): void
    {
        $this->service->setupGame(2);
        $this->setSecretNumber($this->service, 50);

        $this->assertEquals(7, $this->service->getRemainingAttempts());

        $this->service->makeGuess(1);
        $this->assertEquals(6, $this->service->getRemainingAttempts());

        $this->service->makeGuess(2);
        $this->assertEquals(5, $this->service->getRemainingAttempts());
    }

}
