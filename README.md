# Number Guesser CLI Game

A simple, interactive command-line number guessing game built with Laravel.

## Description

**Number Guesser** is a command-line interface (CLI) game where you try to guess a randomly generated number. The game provides feedback on each guess, letting you know if your guess is too high, too low, or correct. The game features multiple difficulty levels, tracks your number of attempts, and can be replayed as many times as you like. This project demonstrates how to build interactive CLI applications using Laravel.

## Features

- Multiple difficulty levels (Easy, Medium, Hard)
- Random number generation for each round
- Interactive CLI prompts with Laravel Prompts
- Feedback for each guess (too high, too low, correct)
- Tracks number of attempts
- Allows exiting at any time (`exit`, `quit`, or `q`)
- Written in PHP using Laravel 

## Requirements

- PHP >= 8.0
- [Composer](https://getcomposer.org/)
- [Laravel](https://laravel.com/) (recommended: Laravel 10+)

## Installation

1. **Clone the repository:**

    ```bash
    git clone https://github.com/JVLoe/number-guesser.git
    cd number-guesser
    ```

2. **Install dependencies:**
    ```bash
    composer install
    ```

3. **Set up environment (optional):**
    Copy `.env.example` to `.env` and adjust as needed.

## Usage

Run the game using the Artisan CLI:

```bash
php artisan serve
```

and in a different terminal:

```bash
php artisan number-guesser
```

Follow the on-screen instructions to play the game. Choose a difficulty level, make guesses, and exit at any time by typing exit, quit, or q.

## Example 
```bash
Welcome to the Number Guesser game! 🎯
Choose your difficulty level:
  🟢 Easy (1-10, 5 attempts)
  🟡 Medium (1-50, 7 attempts)
  🔴 Hard (1-100, 10 attempts)
  ❌ Exit Game

=== Level 1: Easy ===
Guess between 1-10. You have 5 attempts!
💡 Tip: Type 'exit', 'quit', or 'q' at any time to leave the game

Enter your guess (5 attempts left): 5
Too low! Try a higher number 📈
Enter your guess (4 attempts left): 8
Too high! Try a lower number 📉
Enter your guess (3 attempts left): 7
🎉 Congratulations! You guessed the number 7 in 3 attempts!
Would you like to play again? (yes/no)
```