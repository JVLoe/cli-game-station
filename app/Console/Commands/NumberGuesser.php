<?php
namespace App\Console\Commands;

use App\Services\NumberGuesserService;
use function Laravel\Prompts\select;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;
use Illuminate\Console\Command;

class NumberGuesser extends Command
{
    protected $signature = 'number-guesser';
    protected $description = 'Number guesser game (interactive)';

    public function __construct(private NumberGuesserService $gameService)
    {
        parent::__construct();
    }

    public function handle()
    {

        do {
            $this->info('Welcome to the Number Guesser game! 🎯');

            $options = $this->buildLevelOptions();

            $selection = select(
                label: 'Choose your difficulty level:',
                options: $options,
                default: '1'
            );

            if ($selection === 'exit') {
                break;
            }

            $level = (int) $selection;
            $shouldExit = $this->playLevel($level);

            if ($shouldExit) {
                break;
            }


        } while (confirm("Would you like to play again?", true));

        $this->info("Thanks for playing 👋");
    }

    private function buildLevelOptions(): array 
    {
        $levels = $this->gameService->getAvailableLevels();
        $options = []; 

        foreach ($levels as $levelNum => $levelData) {
            $emoji = match($levelNum) {
                1 => '🟢',
                2 => '🟡',
                3 => '🔴',
                default => '⚪'
            };

            $range = $levelData['range'];
            $attempts = $levelData['max_attempts'];
            $options[(string) $levelNum] = "{$emoji} {$levelData['name']} ({$range[0]}-{$range[1]}, {$attempts} attempts)";
        }

        $options['exit'] = '❌ Exit Game';

        return $options;
    }

    private function playLevel(int $level): bool
    {
        $this->gameService->setupGame($level);

        $levelInfo = $this->gameService->getLevelInfo($level);
        $range = $levelInfo['range']; 

        $this->info("\n=== Level {$level}: {$levelInfo['name']} ===");
        $this->info("Guess between {$range[0]}-{$range[1]}. You have {$levelInfo['max_attempts']} attempts!");
        $this->info("💡 Tip: Type 'exit', 'quit', or 'q' at any time to leave the game");


        while (true) {
            $remaining = $this->gameService->getRemainingAttempts();
            $guess = text(
                label: "Enter your guess ({$remaining} attempts left)",
                placeholder: 'Type a number...'
            );

            if ($this->shouldExit($guess)) {
                return true;
            }

            $result = $this->gameService->makeGuess((int) $guess);

            switch ($result['status']) {
                case 'won':
                    $this->info($result['message']); 
                    return false;
                case 'lost':
                    $this->info($result['message']);
                    return false;
                case 'invalid':
                    $this->info($result['message']); 
                    break; 
                case 'too_low':
                case 'too_high':
                    $remaining = $result['remaining'] ?? $this->gameService->getRemainingAttempts();
                    if ($remaining > 0 && $remaining < 3) {
                        $this->info("⚠️  Only {$remaining} attempts left!");
                    } else {
                        $this->info($result['message']);
                    }
                    break;
            }
        }
    }

    private function shouldExit(string $input): bool
    {
        $exitCommands = ['exit', 'quit', 'q'];
        if (in_array(strtolower(trim($input)), $exitCommands)) {
            return true;
        }
        return false;
    }
}
