<?php
namespace App\Services;

use InvalidArgumentException;
use RuntimeException;

class NumberGuesserService 
{
    // initialise properties with default values - php 7.4+ will throw an error if not set
    private array $levels = [
        1 => [
            'name' => 'Easy',
            'range' => [1, 10],
            'max_attempts' => 5,
        ],
        2 => [
            'name' => 'Medium',
            'range' => [1, 50],
            'max_attempts' => 7,
        ],
        3 => [
            'name' => 'Hard',
            'range' => [1, 100],
            'max_attempts' => 10,
        ],
    ];
    
    private int $currentLevel = 0;
    private int $secretNumber = 0;
    private int $attempts = 0;
    private int $maxAttempts = 0;
    private bool $gameActive = false;


    public function setupGame(int $level): void
    {
        // validate level
        if (!isset($this->levels[$level])) {
            throw new InvalidArgumentException("Invalid level: $level. Available levels: " . implode(', ', array_keys($this->levels)));
        }

        $this->currentLevel = $level;
        $this->attempts = 0;
        $this->maxAttempts = $this->levels[$level]['max_attempts'];
        [$min, $max] = $this->levels[$level]['range']; // array destructuring syntax. It's a shorthand way to extract values from an array and assign them to separate variables.
        $this->secretNumber = rand($min, $max);
        $this->gameActive = true;
    }

    public function makeGuess(int $guess): array
    {
        if (!$this->gameActive) {
            throw new RuntimeException('Game is not active');
        }

        // Range validation
        [$min, $max] = $this->levels[$this->currentLevel]['range'];
        if ($guess < $min || $guess > $max) {
            return [
                'status' => 'invalid',
                'message' => "Please enter a number between $min and $max", 
                'attempts' => $this->attempts,
                'remaining' => $this->getRemainingAttempts(),
            ];
        }

        $this->attempts++;

        if ($guess < $this->secretNumber) {
            if ($this->attempts >= $this->maxAttempts) {
                return $this->handleLoss();
            }
            return $this->feedback('too_low', 'Too low! Try a higher number 📈');
        }

        if ($guess > $this->secretNumber) {
            if ($this->attempts >= $this->maxAttempts) {
                return $this->handleLoss();
            }
            return $this->feedback('too_high', 'Too high! Try a lower number 📉');
        }

        $this->gameActive = false; 

        return [
            'status' => 'won',
            'message' => "🎉 Congratulations! You guessed the number {$this->secretNumber} in {$this->attempts} attempts!",
            'attempts' => $this->attempts,
            'secret_number' => $this->secretNumber,
        ]; 
    }

    public function getLevelInfo(int $level): ?array
    {
        return $this->levels[$level] ?? null;
    }

    public function getAvailableLevels(): array
    {
        return $this->levels;
    }

    public function getRemainingAttempts(): int
    {
        return max(0, $this->maxAttempts - $this->attempts);
    }

    public function getCurrentLevel(): int
    {
        return $this->currentLevel;
    }

    private function handleLoss(): array
    {
        $this->gameActive = false;
        return [
            'status' => 'lost',
            'message' => "😞 Game over! The number was {$this->secretNumber}",
            'attempts' => $this->attempts,
            'secret_number' => $this->secretNumber,
        ];
    }

    private function feedback(string $status, string $message): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'attempts' => $this->attempts,
            'remaining' => max(0, $this->maxAttempts - $this->attempts),
        ];
    }

    
}
